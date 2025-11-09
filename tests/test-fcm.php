<?php

/**
 * FCM Testing Script - The Stag SmartDine
 * Run: php test-fcm.php
 */

echo "=== FCM Integration Test ===\n\n";

// Test 1: Check if Firebase package loaded
echo "1. Checking Firebase package...\n";
try {
    $firebase = app(\Kreait\Firebase\Contract\Messaging::class);
    echo "   ✅ Firebase package loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check FCM service
echo "\n2. Checking FCMNotificationService...\n";
try {
    $fcmService = app(\App\Services\FCMNotificationService::class);
    echo "   ✅ FCMNotificationService loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check FCM Statistics
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

// Test 4: Check database tables
echo "\n4. Checking database tables...\n";
try {
    $devices = \App\Models\UserFcmDevice::count();
    echo "   ✅ user_fcm_devices table: $devices records\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

try {
    $notifications = \App\Models\PushNotification::count();
    echo "   ✅ push_notifications table: $notifications records\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 5: Check if service account file exists
echo "\n5. Checking Firebase service account file...\n";
$serviceAccountPath = config('services.fcm.service_account_path');
if ($serviceAccountPath && file_exists(base_path($serviceAccountPath))) {
    echo "   ✅ Service account file found at: " . $serviceAccountPath . "\n";
} else {
    echo "   ❌ Service account file not found at: " . $serviceAccountPath . "\n";
}

// Test 6: Test manual notification (no actual send)
echo "\n6. Testing notification method...\n";
try {
    // Check if we have any registered devices
    $devices = \App\Models\UserFcmDevice::where('is_active', true)->get();
    if ($devices->isEmpty()) {
        echo "   ⚠️  No active devices registered\n";
        echo "   💡 Register a device first using POST /api/fcm/register\n";
    } else {
        echo "   ✅ Found " . $devices->count() . " active device(s)\n";
        echo "   💡 You can test manual notification with: POST /api/fcm/send\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 7: Test automatic notification triggers
echo "\n7. Testing automatic notification triggers...\n";
try {
    $order = \App\Models\Order::first();
    if ($order) {
        echo "   ✅ Found order #" . $order->order_number . " (ID: " . $order->id . ")\n";
        echo "   💡 Test automatic notification with:\n";
        echo "      \$order = App\\Models\\Order::find(" . $order->id . ");\n";
        echo "      \$order->update(['status' => 'preparing']);\n";
        echo "      event(new App\\Events\\OrderStatusUpdatedEvent(\$order, 'pending', 'admin'));\n";
    } else {
        echo "   ⚠️  No orders found in database\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

try {
    $reservation = \App\Models\TableReservation::first();
    if ($reservation) {
        echo "   ✅ Found reservation ID: " . $reservation->id . "\n";
        echo "   💡 Test automatic notification with:\n";
        echo "      event(new App\\Events\\TableBookingCreatedEvent(" . $reservation->id . "));\n";
    } else {
        echo "   ⚠️  No reservations found in database\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n\n";

// Summary
echo "NEXT STEPS:\n";
echo "1. ✅ Ensure Firebase service account file is placed correctly\n";
echo "2. ✅ Check .env has correct Firebase configuration\n";
echo "3. ✅ Register a device using: POST /api/fcm/register\n";
echo "4. ✅ Test manual notification using: POST /api/fcm/send\n";
echo "5. ✅ Test automatic notifications using the commands above\n";
echo "6. ✅ Monitor logs: tail -f storage/logs/laravel.log | grep -i fcm\n\n";
