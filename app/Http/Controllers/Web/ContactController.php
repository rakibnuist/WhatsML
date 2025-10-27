<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Helpers\SeoMeta;
use App\Traits\Seo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ContactController extends Controller
{
    use Seo;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contact = get_option('contact_page', true);

        SeoMeta::init('seo_contact');
        return Inertia::render('Web/Contact', [
            'contact_page' => $contact,
        ]);
    }

    /**
     * Send email to the admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:40'],
            'subject' => 'required|max:100',
            'message' => 'required|max:500',
        ]);

        try {
            $mailTo = env('MAIL_TO');
            if (!$mailTo) {
                \Log::warning('MAIL_TO environment variable is not set. Contact form submission will not send emails.');
                return back()->with('warning', 'Contact form is temporarily unavailable. Please try again later.');
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $useQueue = env('QUEUE_MAIL', false);
            
            if ($useQueue) {
                Mail::to($mailTo)->queue(new ContactMail($data));
                \Log::info('Contact form email queued for sending', ['email' => $request->email]);
            } else {
                Mail::to($mailTo)->send(new ContactMail($data));
                \Log::info('Contact form email sent immediately', ['email' => $request->email]);
            }
            
            return back()->with('success', 'Message has been sent successfully');
            
        } catch (Exception $e) {
            \Log::error('Failed to send contact form email', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'subject' => $request->subject
            ]);
            
            return back()->with('danger', 'Failed to send message. Please try again later.');
        }
    }
}
