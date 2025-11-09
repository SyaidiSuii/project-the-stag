<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

echo "Checking Order 58\n";
echo "==================\n\n";

$order = Order::find(58);

if ($order) {
    echo "Order 58 Details:\n";
    echo "  ID: {$order->id}\n";
    echo "  confirmation_code: " . ($order->confirmation_code ?? 'NULL') . "\n";
    echo "  data-id in HTML: " . ($order->confirmation_code ?? 'ORD-' . $order->id) . "\n";
    echo "  Current status: {$order->order_status}\n\n";

    echo "What Pusher event sends:\n";
    echo "  order_id: {$order->id}\n";
    echo "  confirmation_code: " . ($order->confirmation_code ?? 'NULL') . "\n\n";

    echo "What JavaScript checks:\n";
    echo "  1. cardId === confirmation_code → ";
    if ($order->confirmation_code) {
        echo "'{$order->confirmation_code}'\n";
    } else {
        echo "NULL (SKIP)\n";
    }
    echo "  2. cardId === 'ORD-{$order->id}' → 'ORD-58'\n";
    echo "  3. cardId === '{$order->id}' → '58'\n\n";

    $dataId = $order->confirmation_code ?? 'ORD-' . $order->id;
    echo "Actual data-id value: {$dataId}\n\n";

    if (!$order->confirmation_code) {
        echo "✅ SHOULD MATCH on check #2 (ORD-58)\n";
    } else {
        echo "✅ SHOULD MATCH on check #1 ({$order->confirmation_code})\n";
    }
} else {
    echo "Order 58 not found\n";
}
