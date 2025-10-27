<?php
/**
 * Standalone health check endpoint for Railway
 * This bypasses Laravel entirely to ensure reliability
 */

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Simple health check response
$response = [
    'status' => 'healthy',
    'service' => 'WhatsML',
    'version' => '1.0.0',
    'timestamp' => date('c'),
    'uptime' => 'running',
    'php_version' => PHP_VERSION,
    'server_time' => time()
];

// Return JSON response
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
exit(0);
