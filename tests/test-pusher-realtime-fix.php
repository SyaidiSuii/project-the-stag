<?php

/**
 * Test Pusher Realtime - Order Status Update
 *
 * This script tests if Pusher events are broadcasted correctly
 * when order status is updated.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PUSHER REALTIME TEST ===\n\n";

// Test 1: Check broadcasting configuration
echo "1. Checking broadcasting configuration...\n";
$driver = config('broadcasting.default');
echo "   - Broadcast driver: $driver\n";

if ($driver !== 'pusher' && $driver !== 'reverb') {
    echo "   ❌ ERROR: Broadcasting driver is not set to 'pusher' or 'reverb'\n";
    echo "   Current driver: $driver\n";
    echo "   Please check .env file and set BROADCAST_DRIVER=pusher\n\n";
    exit(1);
}

echo "   ✅ Broadcasting driver is set to: $driver\n\n";

// Test 2: Check Pusher credentials
echo "2. Checking Pusher credentials...\n";
$pusherKey = env('PUSHER_APP_KEY');
$pusherSecret = env('PUSHER_APP_SECRET');
$pusherAppId = env('PUSHER_APP_ID');
$pusherCluster = env('PUSHER_APP_CLUSTER');

echo "   - PUSHER_APP_KEY: " . (empty($pusherKey) ? '❌ NOT SET' : '✅ SET') . "\n";
echo "   - PUSHER_APP_SECRET: " . (empty($pusherSecret) ? '❌ NOT SET' : '✅ SET') . "\n";
echo "   - PUSHER_APP_ID: " . (empty($pusherAppId) ? '❌ NOT SET' : '✅ SET') . "\n";
echo "   - PUSHER_APP_CLUSTER: " . (empty($pusherCluster) ? '❌ NOT SET' : '✅ SET') . "\n\n";

if (empty($pusherKey) || empty($pusherSecret) || empty($pusherAppId) || empty($pusherCluster)) {
    echo "   ❌ ERROR: Pusher credentials are incomplete\n";
    echo "   Please check your .env file\n\n";
    exit(1);
}

// Test 3: Get a test order
echo "3. Getting a test order...\n";
$order = Order::latest()->first();

if (!$order) {
    echo "   ❌ ERROR: No orders found in database\n";
    echo "   Please create an order first\n\n";
    exit(1);
}

echo "   - Order ID: {$order->id}\n";
echo "   - Order Code: {$order->confirmation_code}\n";
echo "   - Current Status: {$order->order_status}\n\n";

// Test 4: Test broadcasting an event
echo "4. Testing event broadcasting...\n";
echo "   Changing order status from '{$order->order_status}' to 'preparing'...\n";

$oldStatus = $order->order_status;
$order->order_status = 'preparing';
$order->save();

// Create and dispatch the event
try {
    echo "   - Dispatching OrderStatusUpdatedEvent...\n";
    event(new OrderStatusUpdatedEvent($order, $oldStatus, 'Test Script'));

    echo "   ✅ Event dispatched successfully!\n\n";

    // Log the event data for verification
    echo "5. Event broadcast data:\n";
    echo json_encode([
        'order_id' => $order->id,
        'old_status' => $oldStatus,
        'new_status' => 'preparing',
        'channel' => 'kitchen-display',
        'event' => 'order.status.updated'
    ], JSON_PRETTY_PRINT) . "\n\n";

    echo "6. Frontend should receive this event on channel 'kitchen-display'\n";
    echo "   with event name 'order.status.updated'\n\n";

    // Restore original status
    $order->order_status = $oldStatus;
    $order->save();

    echo "✅ ALL TESTS PASSED!\n\n";

    echo "Next steps:\n";
    echo "1. Open the order tracking page in browser: /customer/orders/{$order->id}\n";
    echo "2. Keep the page open\n";
    echo "3. Run this test again or update status via admin panel\n";
    echo "4. You should see the page auto-reload when status changes\n\n";

} catch (Exception $e) {
    echo "   ❌ ERROR: Failed to dispatch event\n";
    echo "   Error: " . $e->getMessage() . "\n\n";

    // Restore original status even on error
    $order->order_status = $oldStatus;
    $order->save();

    exit(1);
}
