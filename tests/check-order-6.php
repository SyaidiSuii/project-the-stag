<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\SaleAnalytics;
use App\Events\OrderPaidEvent;

echo "🔍 CHECKING ORDER #6...\n\n";

$order = Order::find(6);

if (!$order) {
    echo "❌ Order #6 not found!\n";
    exit;
}

echo "Order Details:\n";
echo "  ID: {$order->id}\n";
echo "  Amount: RM {$order->total_amount}\n";
echo "  Payment Status: {$order->payment_status}\n";
echo "  Order Status: {$order->order_status}\n";
echo "  Created: {$order->created_at}\n\n";

echo "📊 Current Analytics:\n";
$analytics = SaleAnalytics::whereDate('date', today())->first();
echo "  Total Sales: RM {$analytics->total_sales}\n";
echo "  Total Orders: {$analytics->total_orders}\n\n";

// Fire event for order #6
echo "🔥 Firing event for Order #6...\n";
$order->load('user', 'items');

$analyticsData = [
    'total_revenue' => (float) $analytics->total_sales,
    'total_orders' => (int) $analytics->total_orders,
    'avg_order_value' => (float) $analytics->average_order_value,
];

event(new OrderPaidEvent($order, $analyticsData));
echo "✅ Event fired!\n\n";

// Wait a bit
sleep(1);

// Check analytics again
echo "📊 Analytics After Event:\n";
$analytics = SaleAnalytics::whereDate('date', today())->first();
echo "  Total Sales: RM {$analytics->total_sales}\n";
echo "  Total Orders: {$analytics->total_orders}\n";
echo "  Average: RM {$analytics->average_order_value}\n\n";

echo "✅ Done!\n";
