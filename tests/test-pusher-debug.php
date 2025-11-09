<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;
use Illuminate\Support\Facades\Log;

echo "🔍 Pusher Debug Test\n";
echo "====================\n\n";

// Check Pusher config
echo "1. Checking Pusher Configuration:\n";
echo "   BROADCAST_DRIVER: " . config('broadcasting.default') . "\n";
echo "   PUSHER_APP_KEY: " . config('broadcasting.connections.pusher.key') . "\n";
echo "   PUSHER_CLUSTER: " . config('broadcasting.connections.pusher.options.cluster') . "\n\n";

// Find test order
$order = Order::where('order_status', '!=', 'cancelled')
    ->where('order_status', '!=', 'completed')
    ->first();

if (!$order) {
    echo "❌ No testable order found\n";
    exit(1);
}

echo "2. Test Order Found:\n";
echo "   Order ID: {$order->id}\n";
echo "   Confirmation Code: " . ($order->confirmation_code ?? "ORD-{$order->id}") . "\n";
echo "   Current Status: {$order->order_status}\n";
echo "   User ID: {$order->user_id}\n\n";

// Change status
$statusFlow = [
    'pending' => 'confirmed',
    'confirmed' => 'preparing',
    'preparing' => 'ready',
    'ready' => 'served',
    'served' => 'completed'
];

$oldStatus = $order->order_status;
$newStatus = $statusFlow[$oldStatus] ?? 'confirmed';

echo "3. Broadcasting Event:\n";
echo "   From: {$oldStatus}\n";
echo "   To: {$newStatus}\n\n";

// Update order
$order->order_status = $newStatus;
$order->save();

// Fire event
$event = new OrderStatusUpdatedEvent($order, $oldStatus, 'debug-script');

echo "4. Event Details:\n";
echo "   Event Class: " . get_class($event) . "\n";
echo "   Channel: kitchen-display\n";
echo "   Event Name: order.status.updated\n";
echo "   Payload:\n";
echo "     - order_id: {$event->orderId}\n";
echo "     - old_status: {$event->oldStatus}\n";
echo "     - new_status: {$event->newStatus}\n";
echo "     - updated_by: {$event->updatedBy}\n\n";

// Broadcast event
event($event);

echo "5. ✅ Event Fired!\n\n";

echo "6. Now Check Browser:\n";
echo "   Open: http://localhost/customer/orders/{$order->id}\n";
echo "   OR:   http://localhost/customer/orders\n\n";

echo "7. Open Browser Console (F12) - You should see:\n";
echo "   ✓ 'Pusher: Listening for order status updates on kitchen-display channel'\n";
echo "   ✓ 'Pusher: Order status update received {order_id: {$order->id}, ...}'\n";
echo "   ✓ Toast notification should appear\n\n";

echo "8. If NO Pusher event received, check:\n";
echo "   - Is Pusher.js script loaded? (Check Network tab)\n";
echo "   - Any console errors?\n";
echo "   - Is WebSocket connected? (Network tab → WS filter)\n\n";

echo "✅ Test complete! Check browser now.\n";
