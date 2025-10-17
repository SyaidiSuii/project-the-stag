<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\SaleAnalytics;

echo "=== CHECKING ANALYTICS SYSTEM ===\n\n";

// Check paid orders
echo "📊 PAID ORDERS:\n";
$paidOrders = Order::where('payment_status', 'paid')->get(['id', 'total_amount', 'created_at']);
echo "Total Paid Orders: " . $paidOrders->count() . "\n";

if ($paidOrders->count() > 0) {
    echo "\nRecent paid orders:\n";
    foreach ($paidOrders->take(5) as $order) {
        echo "  - Order #{$order->id}: RM {$order->total_amount} (Created: {$order->created_at})\n";
    }

    $totalAmount = $paidOrders->sum('total_amount');
    echo "\nTotal Amount from Paid Orders: RM {$totalAmount}\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Check sale_analytics table
echo "📈 SALE ANALYTICS TABLE:\n";
$analytics = SaleAnalytics::all(['date', 'total_sales', 'total_orders']);
echo "Total Records: " . $analytics->count() . "\n";

if ($analytics->count() > 0) {
    echo "\nAnalytics records:\n";
    foreach ($analytics as $record) {
        echo "  - Date: {$record->date} | Sales: RM {$record->total_sales} | Orders: {$record->total_orders}\n";
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Check today's analytics
echo "📅 TODAY'S ANALYTICS:\n";
$today = SaleAnalytics::whereDate('date', today())->first();
if ($today) {
    echo "Date: {$today->date}\n";
    echo "Total Sales: RM {$today->total_sales}\n";
    echo "Total Orders: {$today->total_orders}\n";
    echo "Average Order: RM {$today->average_order_value}\n";
} else {
    echo "No analytics record for today yet.\n";
}

echo "\n=== END ===\n";
