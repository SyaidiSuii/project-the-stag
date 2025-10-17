<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\SaleAnalytics;
use Illuminate\Support\Facades\DB;

echo "🗑️  RESET ANALYTICS SYSTEM\n";
echo str_repeat("=", 50) . "\n\n";

// Step 1: Show current data
echo "📊 CURRENT DATA:\n";
$currentRecords = SaleAnalytics::all();
echo "Total Records: " . $currentRecords->count() . "\n";

if ($currentRecords->count() > 0) {
    echo "\nCurrent analytics:\n";
    foreach ($currentRecords as $record) {
        echo "  - {$record->date}: RM {$record->total_sales} ({$record->total_orders} orders)\n";
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Step 2: Check orders
echo "📦 CHECKING ORDERS:\n";
$paidOrders = Order::whereIn('order_status', ['completed', 'served'])
    ->where('payment_status', 'paid')
    ->get();

echo "Total Qualified Orders: " . $paidOrders->count() . "\n";
echo "Total Amount: RM " . $paidOrders->sum('total_amount') . "\n\n";

if ($paidOrders->count() > 0) {
    echo "Orders by date:\n";
    $ordersByDate = $paidOrders->groupBy(function($order) {
        return $order->created_at->format('Y-m-d');
    });

    foreach ($ordersByDate as $date => $orders) {
        echo "  - {$date}: {$orders->count()} orders, RM " . $orders->sum('total_amount') . "\n";
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Step 3: Ask for confirmation
echo "⚠️  WARNING: This will DELETE all analytics data!\n\n";
echo "Do you want to continue? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirm = trim(strtolower($line));

if ($confirm !== 'yes') {
    echo "\n❌ Cancelled. No changes made.\n";
    exit;
}

echo "\n";

// Step 4: Truncate table
echo "🗑️  Deleting all analytics data...\n";
DB::table('sale_analytics')->truncate();
echo "✅ All analytics data deleted!\n\n";

// Step 5: Verify empty
echo "📊 Verifying deletion...\n";
$count = SaleAnalytics::count();
echo "Records remaining: {$count}\n";

if ($count === 0) {
    echo "✅ Table is now empty!\n\n";
} else {
    echo "⚠️  Warning: Some records still exist!\n\n";
}

echo str_repeat("-", 50) . "\n\n";

// Step 6: Offer to regenerate
echo "Do you want to regenerate analytics now? (yes/no): ";
$line = fgets($handle);
$regenerate = trim(strtolower($line));

if ($regenerate === 'yes') {
    echo "\n🔄 Regenerating analytics...\n\n";

    // Run the artisan command
    passthru('php artisan analytics:generate');

    echo "\n📊 Final Check:\n";
    $newRecords = SaleAnalytics::all();
    echo "Total Records: " . $newRecords->count() . "\n";

    if ($newRecords->count() > 0) {
        echo "\nNew analytics:\n";
        foreach ($newRecords as $record) {
            echo "  - {$record->date}: RM {$record->total_sales} ({$record->total_orders} orders)\n";
        }
    }
}

fclose($handle);

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ RESET COMPLETE!\n";
