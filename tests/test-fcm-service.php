<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FCM Service Test ===\n\n";

echo "1. Testing FCMNotificationService...\n";
try {
    $fcmService = app(\App\Services\FCMNotificationService::class);
    echo "   ✅ FCMNotificationService loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n2. Getting statistics...\n";
try {
    $stats = $fcmService->getStatistics();
    echo "   ✅ Statistics retrieved:\n";
    echo "      - Total devices: " . $stats['total_devices'] . "\n";
    echo "      - Active devices: " . $stats['active_devices'] . "\n";
    echo "      - Recent notifications: " . $stats['recent_notifications'] . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FCM Service Test Complete ===\n";
