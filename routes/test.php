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
