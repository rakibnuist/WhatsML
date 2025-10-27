<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallerController extends Controller
{
    public function index()
    {
        // Check if already installed
        if (file_exists(base_path('public/uploads/installed'))) {
            return redirect('/');
        }

        return view('installer.index');
    }

    public function requirements()
    {
        $requirements = [
            'PHP >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'mbstring' => extension_loaded('mbstring'),
            'bcmath' => extension_loaded('bcmath'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
        ];

        $allRequirementsMet = !in_array(false, $requirements);

        return response()->json([
            'requirements' => $requirements,
            'all_met' => $allRequirementsMet
        ]);
    }

    public function verify()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Database connection successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function database(Request $request)
    {
        try {
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Create storage link
            Artisan::call('storage:link');
            
            // Create installed file
            $installedPath = base_path('public/uploads');
            if (!File::exists($installedPath)) {
                File::makeDirectory($installedPath, 0755, true);
            }
            File::put($installedPath . '/installed', now()->toISOString());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Database setup completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database setup failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function complete()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Installation completed successfully',
            'redirect_url' => '/'
        ]);
    }
}
