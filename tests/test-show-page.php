<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;

echo "🎬 Testing Show Page Real-Time Updates\n";
echo "======================================\n\n";

$order = Order::find(48);

if (!$order) {
    echo "❌ Order 48 not found\n";
    exit(1);
}

echo "Order Details:\n";
echo "  ID: {$order->id}\n";
echo "  Confirmation: {$order->confirmation_code}\n";
echo "  Current Status: {$order->order_status}\n\n";

echo "📋 Test Steps:\n";
echo "  1. Open: http://localhost/customer/orders/48\n";
echo "  2. Open browser console (F12)\n";
echo "  3. Look for: 'Pusher listening for order 48'\n\n";

echo "⚠️  Make sure queue worker is RUNNING:\n";
echo "     php artisan queue:work\n\n";

echo "Press ENTER when ready to fire event...";
fgets(STDIN);

// Change status
$statusFlow = [
    'confirmed' => 'preparing',
    'preparing' => 'ready',
    'ready' => 'served',
    'served' => 'completed',
    'pending' => 'confirmed',
];

$oldStatus = $order->order_status;
$newStatus = $statusFlow[$oldStatus] ?? 'confirmed';

echo "\n🔄 Changing status: {$oldStatus} → {$newStatus}\n";

$order->order_status = $newStatus;
$order->save();

echo "📡 Broadcasting event...\n";
event(new OrderStatusUpdatedEvent($order, $oldStatus, 'admin'));

echo "✅ Event queued!\n\n";

echo "👀 CHECK BROWSER SHOW PAGE NOW:\n";
echo "   1. Console: 'Pusher event received: {order_id: 48, ...}'\n";
echo "   2. Console: 'Order status changed: {$oldStatus} → {$newStatus}'\n";
echo "   3. Toast notification: 'Order status updated: {$newStatus}'\n";
echo "   4. Page reloads after 2 seconds\n";
echo "   5. New status '{$newStatus}' displayed after reload\n\n";

echo "✅ Show page works if all 5 steps happen!\n";
