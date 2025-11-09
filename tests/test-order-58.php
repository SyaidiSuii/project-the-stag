<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;

echo "Testing Order 58 with Updated Event\n";
echo "=====================================\n\n";

$order = Order::find(58);

if (!$order) {
    echo "❌ Order 58 not found\n";
    exit(1);
}

echo "Order Details:\n";
echo "  ID: {$order->id}\n";
echo "  Confirmation: {$order->confirmation_code}\n";
echo "  Current Status: {$order->order_status}\n\n";

// Cycle status
$statusFlow = [
    'pending' => 'confirmed',
    'confirmed' => 'preparing',
    'preparing' => 'ready',
    'ready' => 'served',
    'served' => 'completed',
    'completed' => 'pending',
];

$oldStatus = $order->order_status;
$newStatus = $statusFlow[$oldStatus] ?? 'confirmed';

echo "Status Change:\n";
echo "  From: {$oldStatus}\n";
echo "  To: {$newStatus}\n\n";

$order->order_status = $newStatus;
$order->save();

echo "Broadcasting event with BOTH order_id AND confirmation_code...\n";
event(new OrderStatusUpdatedEvent($order, $oldStatus, 'test-script'));

echo "\n✅ Event fired!\n\n";

echo "📡 Check Customer Orders Page:\n";
echo "   http://localhost/customer/orders\n\n";

echo "👀 In Browser Console, you should see:\n";
echo "   Pusher: Order status update received {\n";
echo "     order_id: 58,\n";
echo "     confirmation_code: '{$order->confirmation_code}',  ← THIS IS KEY!\n";
echo "     new_status: '{$newStatus}',\n";
echo "     old_status: '{$oldStatus}',\n";
echo "     ...\n";
echo "   }\n\n";

echo "   Then:\n";
echo "   'Updating order card status badge: {$oldStatus} → {$newStatus}'\n";
echo "   Status badge should change!\n";
