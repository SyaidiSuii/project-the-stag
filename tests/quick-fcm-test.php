<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FCM Quick Test ===\n\n";

echo "1. Testing Firebase package...\n";
try {
    $fcm = app(\Kreait\Firebase\Contract\Messaging::class);
    echo "   ✅ Firebase package loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n2. Testing FCM Service...\n";
try {
    $fcmService = app(\App\Services\FCMNotificationService::class);
    echo "   ✅ FCMNotificationService loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n3. Testing FCM Statistics...\n";
try {
    $stats = $fcmService->getStatistics();
    echo "   ✅ FCM Statistics:\n";
    echo "      - Total devices: " . $stats['total_devices'] . "\n";
    echo "      - Active devices: " . $stats['active_devices'] . "\n";
    echo "      - Recent notifications: " . $stats['recent_notifications'] . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
