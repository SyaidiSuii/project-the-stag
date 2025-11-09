<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$order = Order::find(27);

if ($order) {
    $dataId = $order->confirmation_code ?? 'ORD-' . $order->id;
    echo "Order 27 Details:\n";
    echo "  data-id attribute: {$dataId}\n";
    echo "  confirmation_code: " . ($order->confirmation_code ?? 'null') . "\n";
    echo "  id: {$order->id}\n\n";

    echo "Pusher event sends: order_id = {$order->id}\n";
    echo "JavaScript checks for:\n";
    echo "  - cardId === 'ORD-{$order->id}' (ORD-27)\n";
    echo "  - cardId === '{$order->id}' (27)\n\n";

    if ($order->confirmation_code) {
        echo "⚠️  MISMATCH DETECTED!\n";
        echo "Card has: {$order->confirmation_code}\n";
        echo "JavaScript checks for: ORD-27 or 27\n";
        echo "Result: NOT FOUND ❌\n\n";
        echo "SOLUTION: JavaScript needs to also check confirmation_code\n";
    } else {
        echo "✅ Match OK\n";
    }
} else {
    echo "Order 27 not found\n";
}
