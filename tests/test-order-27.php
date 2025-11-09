<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;

echo "Testing Order 27 (matches test page)\n";
echo "======================================\n\n";

$order = Order::find(27);

if (!$order) {
    echo "❌ Order 27 not found\n";
    exit(1);
}

echo "Order Details:\n";
echo "  ID: {$order->id}\n";
echo "  Confirmation: " . ($order->confirmation_code ?? "ORD-27") . "\n";
echo "  Current Status: {$order->order_status}\n\n";

// Cycle through statuses
$statusFlow = [
    'completed' => 'pending',
    'pending' => 'confirmed',
    'confirmed' => 'preparing',
    'preparing' => 'ready',
    'ready' => 'served',
    'served' => 'completed',
];

$oldStatus = $order->order_status;
$newStatus = $statusFlow[$oldStatus] ?? 'confirmed';

echo "Status Change:\n";
echo "  From: {$oldStatus}\n";
echo "  To: {$newStatus}\n\n";

$order->order_status = $newStatus;
$order->save();

event(new OrderStatusUpdatedEvent($order, $oldStatus, 'admin'));

echo "✅ Event fired!\n";
echo "🌐 Check test page: http://localhost/test-pusher-page.html\n";
echo "   Status badge should change from '{$oldStatus}' to '{$newStatus}'\n";
