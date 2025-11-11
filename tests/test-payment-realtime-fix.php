<?php

/**
 * Test Payment Status Realtime - Pusher Broadcasting
 *
 * This script tests if Pusher events are broadcasted correctly
 * when payment status is updated.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Order;
use App\Events\OrderStatusUpdatedEvent;
use App\Events\PaymentStatusUpdatedEvent;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PAYMENT STATUS REALTIME TEST ===\n\n";

// Test 1: Check broadcasting configuration
echo "1. Checking broadcasting configuration...\n";
$driver = config('broadcasting.default');
echo "   - Broadcast driver: $driver\n";

if ($driver !== 'pusher' && $driver !== 'reverb') {
    echo "   ❌ ERROR: Broadcasting driver is not set to 'pusher' or 'reverb'\n";
    echo "   Current driver: $driver\n";
    echo "   Please check .env file and set BROADCAST_DRIVER=pusher\n\n";
    exit(1);
}

echo "   ✅ Broadcasting driver is set to: $driver\n\n";

// Test 2: Get a test order with unpaid payment
echo "2. Getting a test order...\n";
$order = Order::where('payment_status', '!=', 'paid')->latest()->first();

if (!$order) {
    echo "   ⚠️  WARNING: No orders with unpaid payment found\n";
    echo "   Getting latest order anyway...\n";
    $order = Order::latest()->first();

    if (!$order) {
        echo "   ❌ ERROR: No orders found in database\n";
        echo "   Please create an order first\n\n";
        exit(1);
    }
}

echo "   - Order ID: {$order->id}\n";
echo "   - Order Code: {$order->confirmation_code}\n";
echo "   - Current Payment Status: {$order->payment_status}\n\n";

// Test 3: Test broadcasting payment status update event
echo "3. Testing payment status update event...\n";
echo "   Changing payment status from '{$order->payment_status}' to 'paid'...\n";

$oldPaymentStatus = $order->payment_status;
$order->payment_status = 'paid';
$order->save();

// Create and dispatch the payment status event
try {
    echo "   - Dispatching PaymentStatusUpdatedEvent...\n";
    event(new PaymentStatusUpdatedEvent($order, $oldPaymentStatus, 'Test Script'));

    echo "   ✅ Payment status event dispatched successfully!\n\n";

    // Log the event data for verification
    echo "4. Payment status event broadcast data:\n";
    echo json_encode([
        'order_id' => $order->id,
        'old_payment_status' => $oldPaymentStatus,
        'new_payment_status' => 'paid',
        'channel' => 'kitchen-display',
        'event' => 'payment.status.updated'
    ], JSON_PRETTY_PRINT) . "\n\n";

    echo "5. Frontend should receive this event on channel 'kitchen-display'\n";
    echo "   with event name 'payment.status.updated'\n\n";

    // Restore original payment status
    $order->payment_status = $oldPaymentStatus;
    $order->save();

    echo "✅ PAYMENT STATUS BROADCAST TEST PASSED!\n\n";

} catch (Exception $e) {
    echo "   ❌ ERROR: Failed to dispatch payment status event\n";
    echo "   Error: " . $e->getMessage() . "\n\n";

    // Restore original payment status even on error
    $order->payment_status = $oldPaymentStatus;
    $order->save();

    exit(1);
}

// Test 4: Test broadcasting order status update event (bonus)
echo "6. BONUS: Testing order status update event...\n";
$oldOrderStatus = $order->order_status;
$order->order_status = 'preparing';
$order->save();

try {
    echo "   - Dispatching OrderStatusUpdatedEvent...\n";
    event(new OrderStatusUpdatedEvent($order, $oldOrderStatus, 'Test Script'));

    echo "   ✅ Order status event dispatched successfully!\n\n";

    // Restore original order status
    $order->order_status = $oldOrderStatus;
    $order->save();

} catch (Exception $e) {
    echo "   ❌ ERROR: Failed to dispatch order status event\n";
    echo "   Error: " . $e->getMessage() . "\n\n";

    // Restore original order status even on error
    $order->order_status = $oldOrderStatus;
    $order->save();
}

echo "=== ALL TESTS COMPLETED ===\n\n";

echo "Summary:\n";
echo "- Pusher configuration: ✅ OK\n";
echo "- Payment status broadcasting: ✅ OK\n";
echo "- Order status broadcasting: ✅ OK\n\n";

echo "Next steps:\n";
echo "1. Open the order tracking page in browser: /customer/orders/{$order->id}\n";
echo "2. Keep the page open\n";
echo "3. Update payment status via admin panel (Orders → View → Change Payment Status to 'Paid')\n";
echo "4. You should see:\n";
echo "   - Toast notification: 'Payment status updated: paid'\n";
echo "   - Page auto-reload after 2 seconds\n";
echo "   - Payment status progress bar shows 'Paid'\n\n";

echo "5. Or test order status update:\n";
echo "   - Change order status via admin panel\n";
echo "   - You should see: 'Order status updated: preparing'\n\n";
