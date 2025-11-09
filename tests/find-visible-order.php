<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

echo "Finding visible orders for user 2...\n";
echo "====================================\n\n";

$orders = Order::where('user_id', 2)
    ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

if ($orders->isEmpty()) {
    echo "No visible orders found.\n";
    echo "All orders for user 2 are completed/cancelled.\n";
    exit(0);
}

echo "Found " . $orders->count() . " visible order(s):\n\n";

foreach ($orders as $order) {
    echo "Order ID: {$order->id}\n";
    echo "  Confirmation: {$order->confirmation_code}\n";
    echo "  Status: {$order->order_status}\n";
    echo "  User: {$order->user_id}\n";
    echo "  ----------------------------------------\n";
}

echo "\nTest dengan order ID " . $orders->first()->id . "? (Y/n): ";
