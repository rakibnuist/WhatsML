<?php

// Simple test route for Railway deployment
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'WhatsML is working!',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'database' => config('database.default'),
        'app_name' => config('app.name')
    ]);
});

// Health check endpoint for Railway
Route::get('/health', function () {
    try {
        // Basic health check without database dependencies
        return response()->json([
            'status' => 'healthy',
            'service' => 'WhatsML',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
            'uptime' => 'running',
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
});

// Simple ping endpoint
Route::get('/ping', function () {
    return response()->json(['pong' => true, 'timestamp' => now()]);
});

// Simple installer route
Route::get('/simple-install', function () {
    return response()->file(public_path('install.html'));
});

// Debug installer route
Route::get('/install-debug', function () {
    return response()->file(public_path('install-debug.html'));
});

// Test installer requirements endpoint
Route::get('/test-requirements', function () {
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
        'all_met' => $allRequirementsMet,
        'php_version' => PHP_VERSION,
        'extensions' => get_loaded_extensions()
    ]);
});

// Installer endpoints (temporary fix)
Route::get('/install-requirements', function () {
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
        'all_met' => $allRequirementsMet,
        'php_version' => PHP_VERSION
    ]);
});

Route::post('/install-verify', function () {
    try {
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
});

Route::post('/install-database', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('storage:link');
        
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
});
