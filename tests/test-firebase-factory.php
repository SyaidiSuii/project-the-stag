<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Firebase Initialization ===\n\n";

echo "1. Checking if Firebase factory exists...\n";
try {
    $firebaseFactory = app(\Kreait\Firebase\Factory::class);
    echo "   ✅ Factory loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n2. Initializing Firebase app...\n";
try {
    $firebaseApp = $firebaseFactory
        ->withServiceAccount(config('services.fcm.service_account_path'))
        ->create();

    echo "   ✅ Firebase app initialized\n";
    echo "   Project ID: " . $firebaseApp->getProjectId() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n3. Getting Messaging service...\n";
try {
    $messaging = app(\Kreait\Firebase\Contract\Messaging::class);
    echo "   ✅ Messaging service loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== All Tests Passed! ===\n";
