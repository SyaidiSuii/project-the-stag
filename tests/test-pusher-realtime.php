<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;

echo "🔴 LIVE PUSHER TEST - Order Card Update\n";
echo "==========================================\n\n";

// Find order that's NOT completed/cancelled
$order = Order::where('order_status', '!=', 'cancelled')
    ->where('order_status', '!=', 'completed')
    ->first();

if (!$order) {
    echo "❌ No testable order found\n";
    echo "Creating test order...\n";

    // Create test order
    $order = new Order();
    $order->user_id = 1;
    $order->order_type = 'dine_in';
    $order->order_status = 'pending';
    $order->payment_status = 'unpaid';
    $order->payment_method = 'cash';
    $order->total_amount = 50.00;
    $order->confirmation_code = 'TEST-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(time()), 0, 4));
    $order->save();

    echo "✅ Test order created: ID {$order->id}\n\n";
}

echo "📋 Order Details:\n";
echo "   ID: {$order->id}\n";
echo "   Confirmation Code: {$order->confirmation_code}\n";
echo "   User ID: {$order->user_id}\n";
echo "   Current Status: {$order->order_status}\n";
echo "   Card ID (data-id): {$order->confirmation_code}\n\n";

echo "🌐 OPEN THESE IN BROWSER NOW:\n";
echo "   Index Page: http://localhost/customer/orders\n";
echo "   Show Page:  http://localhost/customer/orders/{$order->id}\n\n";

echo "📱 In Browser Console (F12), you should see:\n";
echo "   'Pusher: Listening for order status updates on kitchen-display channel'\n\n";

echo "Press ENTER when ready to change status...";
fgets(STDIN);

// Change status
$statusFlow = [
    'pending' => 'confirmed',
    'confirmed' => 'preparing',
    'preparing' => 'ready',
    'ready' => 'served',
];

$oldStatus = $order->order_status;
$newStatus = $statusFlow[$oldStatus] ?? 'confirmed';

echo "\n🔄 Broadcasting Status Change:\n";
echo "   From: {$oldStatus}\n";
echo "   To: {$newStatus}\n\n";

// Update order
$order->order_status = $newStatus;
$order->save();

echo "✅ Order updated in database\n\n";

// Broadcast event
event(new OrderStatusUpdatedEvent($order, $oldStatus, 'test-script'));

echo "📡 Event broadcasted to Pusher!\n\n";

echo "👀 CHECK BROWSER NOW:\n\n";

echo "INDEX PAGE - You should see:\n";
echo "   ✓ Console: 'Pusher: Order status update received {order_id: {$order->id}, ...}'\n";
echo "   ✓ Console: 'Updating order card status badge: {$oldStatus} → {$newStatus}'\n";
echo "   ✓ Order card status badge changes color/text\n";
echo "   ✓ Pulse animation on badge\n";
echo "   ✓ Toast notification: 'Order status updated: {$newStatus}'\n";
echo "   ✓ NO page reload\n\n";

echo "SHOW PAGE - You should see:\n";
echo "   ✓ Console: 'Pusher event received: {order_id: {$order->id}, ...}'\n";
echo "   ✓ Console: 'Order status changed: {$oldStatus} → {$newStatus}'\n";
echo "   ✓ Toast notification appears\n";
echo "   ✓ Page reloads after 2 seconds\n\n";

echo "❌ IF NOTHING HAPPENS:\n";
echo "   1. Check browser console for errors (red text)\n";
echo "   2. Check Network tab → WS filter → WebSocket connected?\n";
echo "   3. Run: grep BROADCAST_DRIVER .env (should be 'pusher')\n";
echo "   4. Check: Is user logged in? (Pusher code wrapped in @auth)\n\n";

echo "Press ENTER to test again with different order...";
fgets(STDIN);
