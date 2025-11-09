<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Pusher Connection Diagnostic\n";
echo "================================\n\n";

// 1. Check config
echo "1. Laravel Broadcasting Config:\n";
echo "   BROADCAST_DRIVER: " . config('broadcasting.default') . "\n";
echo "   PUSHER_APP_KEY: " . config('broadcasting.connections.pusher.key') . "\n";
echo "   PUSHER_APP_SECRET: " . config('broadcasting.connections.pusher.secret') . "\n";
echo "   PUSHER_APP_ID: " . config('broadcasting.connections.pusher.app_id') . "\n";
echo "   PUSHER_CLUSTER: " . config('broadcasting.connections.pusher.options.cluster') . "\n\n";

// 2. Check if Pusher PHP SDK is working
echo "2. Testing Pusher PHP SDK:\n";

try {
    $pusher = new Pusher\Pusher(
        config('broadcasting.connections.pusher.key'),
        config('broadcasting.connections.pusher.secret'),
        config('broadcasting.connections.pusher.app_id'),
        config('broadcasting.connections.pusher.options')
    );

    echo "   ✅ Pusher SDK initialized\n\n";

    // 3. Try to trigger a test event
    echo "3. Sending test event directly to Pusher...\n";

    $data = [
        'message' => 'Direct test from PHP',
        'timestamp' => now()->toDateTimeString(),
    ];

    $result = $pusher->trigger('kitchen-display', 'test-event', $data);

    if ($result) {
        echo "   ✅ Event sent to Pusher successfully!\n";
        echo "   📡 Check test page: http://localhost/test-pusher-page.html\n";
        echo "   Event name: 'test-event' (not order.status.updated)\n\n";
    } else {
        echo "   ❌ Failed to send event\n\n";
    }

} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// 4. Check event listener
echo "4. Checking Event Listener Registration:\n";

$listeners = Event::getListeners('App\Events\OrderStatusUpdatedEvent');

if (empty($listeners)) {
    echo "   ❌ No listeners registered for OrderStatusUpdatedEvent\n";
} else {
    echo "   ✅ Found " . count($listeners) . " listener(s)\n";
}

echo "\n";

// 5. Check if event implements ShouldBroadcast
echo "5. Checking Event Broadcasting:\n";

$eventClass = new ReflectionClass('App\Events\OrderStatusUpdatedEvent');
$interfaces = $eventClass->getInterfaceNames();

if (in_array('Illuminate\Contracts\Broadcasting\ShouldBroadcast', $interfaces)) {
    echo "   ✅ Event implements ShouldBroadcast\n";
} else {
    echo "   ❌ Event does NOT implement ShouldBroadcast\n";
}

echo "\n";

// 6. Summary
echo "================================\n";
echo "DIAGNOSTIC SUMMARY:\n";
echo "================================\n\n";

echo "If test event was sent successfully:\n";
echo "  → Pusher credentials are correct\n";
echo "  → PHP can connect to Pusher\n\n";

echo "If browser doesn't receive events:\n";
echo "  → Check browser console for connection errors\n";
echo "  → Check Network tab → WS for WebSocket connection\n";
echo "  → Make sure page is listening to correct channel\n\n";

echo "Next steps:\n";
echo "  1. Open http://localhost/test-pusher-page.html\n";
echo "  2. Check if 'test-event' was received\n";
echo "  3. If yes, problem is with Laravel broadcasting\n";
echo "  4. If no, problem is with Pusher or browser\n";
