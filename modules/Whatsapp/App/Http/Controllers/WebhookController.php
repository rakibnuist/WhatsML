<?php

namespace Modules\Whatsapp\App\Http\Controllers;

use App\Models\Message;
use App\Models\Customer;
use App\Models\Platform;
use App\Traits\Uploader;
use App\Models\CampaignLog;
use App\Models\PlatformLog;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Jobs\HandleAutoReplyJob;
use App\Jobs\IncomingMessageJob;
use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Jobs\IncomingConversationJob;
use Illuminate\Support\Facades\Storage;
use App\Events\IncomingMessageUpdateEvent;
use Modules\Whatsapp\App\Services\WhatsappClient;

class WebhookController extends Controller
{
    use Uploader;

    public function store(Request $request, $uuid)
    {
        Log::info('whatsapp webhook (post):', $request->all());

        Telescope::tag(function () use ($uuid) {
            return ['platform:' => $uuid];
        });

        if ($request->has('hub_mode')) {
            return $this->verify($request, $uuid);
        }

        $data = $request->all();
        $platform = Platform::whatsapp()->where('uuid', $uuid)->first();

        if (!$platform) {
            return response()->noContent(200);
        }

        if ($data['object'] === 'whatsapp_business_account') {
            $this->handleEntries($data['entry'] ?? []);
        }

        return response()->noContent(200);
    }

    public function verify(Request $request, $uuid)
    {
        Log::info('whatsapp webhook (verify):', $request->all());

        $mode = $request->input('hub_mode');
        $token = $request->input('hub_verify_token');
        $challenge = $request->input('hub_challenge');

        if ($uuid != $token) {
            return response([], 403);
        }

        $platform = Platform::where('uuid', $token)->first();

        if ($mode !== 'subscribe' || !$platform) {
            return response([], 403);
        }

        $newMeta = [
            ...$platform->meta,
            'webhook_connected' => true
        ];

        $platform->updateQuietly(['meta' => $newMeta]);

        echo $challenge;
        exit;
    }

    private function resolveConversationAndCustomer(array $value): Conversation
    {
        $member = $value['contacts'][0] ?? null;

        $memberId = $member['wa_id'] ?? null;
        $memberName = $member['profile']['name'] ?? null;

        $ownerNumberId = $value['metadata']['phone_number_id'];
        $platform = Platform::query()->where('uuid', $ownerNumberId)->firstOrFail();
        $owner = $platform->user()->firstOrFail();

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $formattedNumber = $phoneUtil->parse($memberId, "BD");

        DB::beginTransaction();
        $customer = Customer::query()->firstOrCreate(
            [
                'module' => 'whatsapp',

                'owner_id' => $owner->id,
                'uuid' => $memberId,
            ],
            [
                'picture' => 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
                'name' => $memberName,
                'meta' => [
                    'dial_code' => $formattedNumber->getCountryCode(),
                    'phone' => $formattedNumber->getNationalNumber(),
                ]
            ]
        );

        $conversation = Conversation::query()->firstOrCreate(
            [
                'module' => 'whatsapp',
                'owner_id' => $owner->id,
                'customer_id' => $customer->id,
                'platform_id' => $platform->id,
            ],
            [
                'auto_reply_enabled' => $platform->isAutoReplyEnabled(),
                'meta' => []
            ]
        );

        DB::commit();

        return $conversation;
    }

    private function handleEntries(array $entries)
    {
        foreach ($entries as $entryItem) {
            $this->handleChanges($entryItem['changes'] ?? []);
        }
    }

    private function handleChanges(array $changes)
    {
        foreach ($changes as $changesItem) {
            if ($changesItem['field'] === 'messages') {
                $this->handleMessageField($changesItem['value']);
            }
        }
    }

    private function handleMessageField(array $value)
    {
        // new messages events
        if (isset($value['messages'])) {
            $this->handleNewMessages($value);
        }

        // status update events
        if (isset($value['statuses'])) {
            $this->handleMessageStatuses($value);
        }
    }

    private function handleNewMessages(array $value)
    {
        $conversation = $this->resolveConversationAndCustomer($value);
        foreach ($value['messages'] ?? [] as $message) {
            $messageType = $message['type'];

            $meta = [];

            if (in_array($messageType, ['document', 'sticker', 'image', 'video', 'audio'])) {
                $meta = [
                    'attachment_loaded' => false,
                    'media_url' => '',
                ];
            }

            $params = [
                'id' => $message['id'],
                'type' => $messageType,
                'body' => $message[$messageType] ?? $message['errors'] ?? [],
                'meta' => $meta,
            ];

            // reply
            if (isset($message['context']['id'])) {
                $replyMessage = Message::firstWhere('uuid', $message['context']['id']);
                if ($replyMessage) {
                    $params['meta']['context'] = [
                        'id' => $replyMessage->uuid,
                        'title' => $replyMessage->body['text'] ?? $replyMessage->type,
                    ];
                }
            }

            DB::beginTransaction();
            $incomingMessage = $conversation->messages()
                ->updateOrCreate(
                    [
                        'module' => 'whatsapp',
                        'uuid' => $params['id'],
                        'owner_id' => $conversation->owner_id,
                        'platform_id' => $conversation->platform_id,
                    ],
                    [
                        'customer_id' => $conversation->customer_id,
                        'conversation_id' => $conversation->id,
                        'direction' => 'in',
                        ...$params,
                        'status' => 'received',
                    ]
                );

            PlatformLog::create([
                'module' => 'whatsapp',
                'owner_id' => $conversation->owner_id,
                'platform_id' => $conversation->platform_id,
                'customer_id' => $conversation->customer_id,
                'direction' => 'in',
                'message_type' => $messageType,
                'message_text' => $incomingMessage->getText(),
                'meta' => $params,
            ]);

            // unblock conversation if blocked
            if (data_get($conversation->meta, 'is_24h_passed', false)) {
                $conversation->update([
                    'meta' => [
                        ...$conversation->meta ?? [],
                        'is_24h_passed' => false
                    ]
                ]);
            }

            DB::commit();

            // send event to the live chat
            if ($conversation->wasRecentlyCreated) {
                dispatch(new IncomingConversationJob($conversation->toArray()));
            } else {
                dispatch(new IncomingMessageJob($incomingMessage->toArray()));
            }

            $canAutoReply = validateUserPlan('auto_reply', true, $incomingMessage->platform->owner_id);
            if ($canAutoReply && in_array($messageType, ['text', 'interactive'])) {
                HandleAutoReplyJob::dispatch($incomingMessage);
            }
        }
    }

    private function handleMessageStatuses($value)
    {
        $statuses = $value['statuses'] ?? [];
        foreach ($statuses as $status) {
            $this->handleMessageStatusUpdate($status['id'], $status);
        }
    }

    private function handleMessageStatusUpdate(string $wamId, array $statusData = [])
    {
        $status = $statusData['status'];

        /**
         * @var Message $message
         */
        $message = Message::query()->where('uuid', $wamId)->first();

        if (!$message) {
            return;
        }

        $status = $statusData['status'];

        DB::beginTransaction();

        $message->update(['status' => $status]);

        $this->updateCampaignLog(
            $wamId,
            $statusData
        );

        if ($status === 'failed') {
            if (data_get($statusData, 'errors.0.code') === 131047) {

                // mark the message as 24h passed
                $message->update([
                    'meta' => [
                        ...$message->meta ?? [],
                        'is_24h_passed' => true,
                        'errors' => $statusData['errors']
                    ]
                ]);

                $chat = $message->conversation;
                $chat->update([
                    'meta' => [
                        ...$chat->meta,
                        'is_24h_passed' => true,
                        'errors' => $statusData['errors']
                    ]
                ]);
            }
        }

        DB::commit();

        broadcast(new IncomingMessageUpdateEvent($message))->toOthers();
    }

    private function updateCampaignLog(string $wamId, array $statusData)
    {
        $campaign_log = CampaignLog::where('meta->wamid', $wamId)->first();
        if (!$campaign_log) {
            return;
        }
        $meta = $campaign_log->meta ?? [];

        $status = $statusData['status'];

        if ($status === 'failed') {
            if (data_get($statusData, 'errors.0.code') === 131047) {
                $meta['errors'] = $statusData['errors'];
            }
        }

        $campaign_log->update([
            "{$status}_at" => now()->timestamp($statusData['timestamp'] ?? now())->toDateTimeLocalString(),
            'meta' => $meta
        ]);
    }
}
