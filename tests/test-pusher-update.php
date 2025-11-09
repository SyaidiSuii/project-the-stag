<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;

echo "🧪 Testing Pusher Real-Time Updates\n";
echo "===================================\n\n";

// Find a testable order
$order = Order::where('order_status', '!=', 'cancelled')
    ->where('order_status', '!=', 'completed')
    ->first();

if (!$order) {
    echo "❌ No testable orders found\n";
    echo "Please create an order first.\n";
    exit(1);
}

echo "Found Order:\n";
echo "  - ID: {$order->id}\n";
echo "  - Confirmation Code: " . ($order->confirmation_code ?? "ORD-{$order->id}") . "\n";
echo "  - Current Status: {$order->order_status}\n\n";

// Determine next status
$statusFlow = [
    'pending' => 'confirmed',
    'confirmed' => 'preparing',
    'preparing' => 'ready',
    'ready' => 'served',
    'served' => 'completed'
];

$oldStatus = $order->order_status;
$newStatus = $statusFlow[$oldStatus] ?? 'confirmed';

echo "Status Change:\n";
echo "  - From: {$oldStatus}\n";
echo "  - To: {$newStatus}\n\n";

// Update order status
$order->order_status = $newStatus;
$order->save();

echo "✓ Order status updated in database\n\n";

// Broadcast event
event(new OrderStatusUpdatedEvent($order, $oldStatus, 'test-script'));

echo "✓ OrderStatusUpdatedEvent broadcasted!\n\n";
echo "📡 Pusher should send this to 'kitchen-display' channel\n";
echo "🌐 Open browser to:\n";
echo "   - Customer order show: http://localhost/customer/orders/{$order->id}\n";
echo "   - Customer orders list: http://localhost/customer/orders\n\n";
echo "👀 Check browser console for:\n";
echo "   - 'Pusher: Order status update received'\n";
echo "   - Toast notification should appear\n";
echo "   - Status badge should update (index page) or page reload (show page)\n\n";

echo "✅ Test complete!\n";
