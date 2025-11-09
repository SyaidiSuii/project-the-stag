<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Detailed Firebase Test ===\n\n";

echo "1. Checking Firebase Manager...\n";
try {
    $manager = app(\Kreait\Laravel\Firebase\FirebaseProjectManager::class);
    echo "   ✅ Manager loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n2. Getting Firebase project...\n";
try {
    $project = $manager->project('app');
    echo "   ✅ Project 'app' loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n3. Getting Messaging service...\n";
try {
    $messaging = $project->messaging();
    echo "   ✅ Messaging service loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n4. Testing Messaging via container...\n";
try {
    $messaging2 = app(\Kreait\Firebase\Contract\Messaging::class);
    echo "   ✅ Messaging service via container\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== All Firebase Tests Passed! ===\n";
