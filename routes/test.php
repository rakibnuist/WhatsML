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
    return response()->json([
        'status' => 'healthy',
        'service' => 'WhatsML',
        'version' => '1.0.0',
        'timestamp' => now(),
        'uptime' => 'running'
    ]);
});

// Simple ping endpoint
Route::get('/ping', function () {
    return response()->json(['pong' => true, 'timestamp' => now()]);
});
