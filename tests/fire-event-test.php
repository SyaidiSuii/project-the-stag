<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\SaleAnalytics;
use App\Events\OrderPaidEvent;

echo "🔥 Testing OrderPaidEvent for Order #3...\n\n";

// Get order #3 (RM 26.00)
$order = Order::find(3);

if (!$order) {
    echo "❌ Order #3 not found!\n";
    exit;
}

echo "Order Details:\n";
echo "  ID: {$order->id}\n";
echo "  Amount: RM {$order->total_amount}\n";
echo "  Payment Status: {$order->payment_status}\n\n";

// Load relationships
$order->load('user', 'items');

// Fire event
echo "🔥 Firing OrderPaidEvent...\n";
event(new OrderPaidEvent($order));
echo "✅ Event dispatched!\n\n";

// Wait a bit for listener to process
sleep(1);

// Check analytics
echo "📊 Checking analytics table...\n";
$analytics = SaleAnalytics::whereDate('date', today())->first();

if ($analytics) {
    echo "  Date: {$analytics->date}\n";
    echo "  Total Sales: RM {$analytics->total_sales}\n";
    echo "  Total Orders: {$analytics->total_orders}\n";
    echo "  Average: RM {$analytics->average_order_value}\n";
} else {
    echo "  No analytics record for today!\n";
}

echo "\n✅ Test complete!\n";
