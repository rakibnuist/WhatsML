<?php
/**
 * Test script to verify the application works locally
 */

echo "Testing WhatsML Application\n";
echo "==========================\n\n";

// Test 1: Check if Laravel can bootstrap
echo "1. Testing Laravel bootstrap...\n";
try {
    require_once __DIR__ . '/bootstrap/app.php';
    echo "   ✅ Laravel bootstrap successful\n";
} catch (Exception $e) {
    echo "   ❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check environment variables
echo "\n2. Testing environment variables...\n";
$requiredVars = ['APP_NAME', 'APP_ENV', 'APP_KEY'];
foreach ($requiredVars as $var) {
    if (env($var)) {
        echo "   ✅ $var is set\n";
    } else {
        echo "   ⚠️ $var is not set\n";
    }
}

// Test 3: Check database connection
echo "\n3. Testing database connection...\n";
try {
    $connection = config('database.default');
    echo "   📊 Database driver: $connection\n";
    
    if ($connection === 'sqlite') {
        $dbPath = config('database.connections.sqlite.database');
        if (file_exists($dbPath)) {
            echo "   ✅ SQLite database file exists\n";
        } else {
            echo "   ⚠️ SQLite database file not found, will be created\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Database configuration error: " . $e->getMessage() . "\n";
}

// Test 4: Check storage directories
echo "\n4. Testing storage directories...\n";
$storageDirs = [
    'storage/app/public',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "   ✅ $dir exists and is writable\n";
    } else {
        echo "   ⚠️ $dir missing or not writable\n";
    }
}

echo "\n🎉 Application test completed!\n";
echo "\nTo start the development server:\n";
echo "php artisan serve\n";
echo "\nTo test the health endpoint:\n";
echo "php test-health.php\n";
