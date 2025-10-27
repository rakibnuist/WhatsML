<?php
/**
 * Simple health check test script
 * Run this to test if the health endpoint works locally
 */

// Test the health endpoint
$url = 'http://localhost:8000/health';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'header' => 'User-Agent: Railway Health Check'
    ]
]);

echo "Testing health endpoint: $url\n";
echo "================================\n";

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ Health check failed - could not connect to $url\n";
    echo "Make sure the Laravel development server is running:\n";
    echo "php artisan serve\n";
    exit(1);
}

$data = json_decode($response, true);

if ($data === null) {
    echo "❌ Health check failed - invalid JSON response\n";
    echo "Response: $response\n";
    exit(1);
}

if (isset($data['status']) && $data['status'] === 'healthy') {
    echo "✅ Health check passed!\n";
    echo "Service: " . ($data['service'] ?? 'Unknown') . "\n";
    echo "Version: " . ($data['version'] ?? 'Unknown') . "\n";
    echo "Environment: " . ($data['environment'] ?? 'Unknown') . "\n";
    echo "PHP Version: " . ($data['php_version'] ?? 'Unknown') . "\n";
    echo "Timestamp: " . ($data['timestamp'] ?? 'Unknown') . "\n";
} else {
    echo "❌ Health check failed - unhealthy status\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

echo "\n🎉 Health endpoint is working correctly!\n";
