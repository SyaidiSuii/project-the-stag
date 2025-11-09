<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;

echo "🎯 Testing Order 49 (VISIBLE on customer page)\n";
echo "==============================================\n\n";

$order = Order::find(49);

if (!$order) {
    echo "❌ Order 49 not found\n";
    exit(1);
}

echo "Order Details:\n";
echo "  ID: {$order->id}\n";
echo "  Confirmation Code: {$order->confirmation_code}\n";
echo "  User: {$order->user_id}\n";
echo "  Current Status: {$order->order_status}\n\n";

echo "⚠️  IMPORTANT: Open customer orders page NOW:\n";
echo "   http://localhost/customer/orders\n";
echo "   Login as user {$order->user_id}\n";
echo "   Find order: {$order->confirmation_code}\n\n";

echo "Press ENTER when ready...";
fgets(STDIN);

// Change status
$oldStatus = $order->order_status;
$newStatus = 'preparing'; // confirmed → preparing

echo "\n🔄 Changing status...\n";
echo "  From: {$oldStatus}\n";
echo "  To: {$newStatus}\n\n";

$order->order_status = $newStatus;
$order->save();

echo "✅ Order updated in database\n\n";

echo "📡 Broadcasting Pusher event NOW...\n";
event(new OrderStatusUpdatedEvent($order, $oldStatus, 'admin'));

echo "✅ Event broadcasted!\n\n";

echo "👀 CHECK BROWSER CONSOLE NOW:\n";
echo "   You should see:\n";
echo "   1. 'Pusher: Order status update received'\n";
echo "   2. {\n";
echo "        order_id: {$order->id},\n";
echo "        confirmation_code: '{$order->confirmation_code}',\n";
echo "        new_status: '{$newStatus}',\n";
echo "        old_status: '{$oldStatus}'\n";
echo "      }\n";
echo "   3. 'Updating order card status badge: {$oldStatus} → {$newStatus}'\n";
echo "   4. Toast notification appears\n";
echo "   5. Status badge changes from blue (confirmed) to orange (preparing)\n\n";

echo "❌ If nothing happens:\n";
echo "   - Check console for errors\n";
echo "   - Hard refresh: Ctrl+Shift+R\n";
echo "   - Make sure you're logged in as user {$order->user_id}\n";
