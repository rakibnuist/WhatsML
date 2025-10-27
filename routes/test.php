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

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'WhatsML',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});
