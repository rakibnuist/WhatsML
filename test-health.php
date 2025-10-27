<?php
/**
 * Simple health check test script
 * Run this to test if the health endpoint works locally
 */

// Test both health endpoints
$endpoints = [
    'Laravel Route' => 'http://localhost:8000/health',
    'Standalone PHP' => 'http://localhost:8000/health.php'
];

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
        'header' => 'User-Agent: Railway Health Check'
    ]
]);

echo "Testing health endpoints\n";
echo "========================\n\n";

foreach ($endpoints as $name => $url) {
    echo "Testing $name: $url\n";
    echo str_repeat('-', 50) . "\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "❌ Health check failed - could not connect to $url\n";
        echo "Make sure the Laravel development server is running:\n";
        echo "php artisan serve\n\n";
        continue;
    }
    
    $data = json_decode($response, true);
    
    if ($data === null) {
        echo "❌ Health check failed - invalid JSON response\n";
        echo "Response: $response\n\n";
        continue;
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
    }
    
    echo "\n";
}

echo "🎉 Health endpoint testing completed!\n";
