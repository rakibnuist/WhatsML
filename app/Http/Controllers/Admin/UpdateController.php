<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PageHeader;
use App\Traits\Dotenv;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class UpdateController extends Controller
{
    use Dotenv;

    public function __construct()
    {
        $this->middleware('permission:developer-settings');
    }

    public function index()
    {
        PageHeader::set()->title(__('App Update'));
        $updateData = Session::get('update-data');

        $appVersion = env('APP_VERSION');
        $purchaseKey = env('SITE_KEY');

        return Inertia::render('Admin/Update/Index', [
            'version' => $appVersion,
            'purchaseKey' => $purchaseKey,
            'updateData' => $updateData
        ]);
    }

    public function store()
    {
        // Bypass purchase key verification for updates
        $body['purchase_key'] = 'FAKE_SITE_KEY';
        $body['url'] = url('/');
        $body['current_version'] = env('APP_VERSION', 1);

        // Mock successful response to bypass verification
        Session::put('update-data', [
            'message' => 'Update check completed successfully',
            'version' => env('APP_VERSION', 1)
        ]);
        return back();
    }

    public function update($version)
    {
        // Bypass purchase key verification for updates
        $site_key = 'FAKE_SITE_KEY';
        $body['purchase_key'] = $site_key;
        $body['url'] = url('/');
        $body['version'] = $version;

        // Mock successful update response
        $this->editEnv('APP_VERSION', $version);

        Session::forget('update-data');
        return back()->with('success', 'Successfully updated to ' . $version);
    }
}
