<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User; // Assuming you might need to create a guest user or link to an existing one
use App\Models\UserCart;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('customer.payment.index');
    }

    public function placeOrder(Request $request)
    {
        // Debug log
        logger()->info('Payment submission received', ['data' => $request->all()]);

        // Basic validation - allow id to be string or integer
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.payment_method' => 'nullable|string|in:online,counter',
            'is_from_cart' => 'nullable|boolean',
            'payment_details' => 'required|array',
            'payment_details.method' => 'required|string|in:online,counter',
            'payment_details.order_type' => 'nullable|string|in:dine_in,takeaway',
            'payment_details.email' => 'nullable|email',
            'payment_details.name' => 'nullable|string',
            'payment_details.phone' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = $validated['cart'];
            $paymentDetails = $validated['payment_details'];
            $totalAmount = 0;

            // Calculate total amount from the backend to prevent tampering
            foreach ($cartItems as $item) {
                $menuItem = \App\Models\MenuItem::find($item['id']);
                $totalAmount += $menuItem->price * $item['quantity'];
            }

            // Determine payment method and set initial status
            $paymentMethod = $paymentDetails['method'];
            $isCounterPayment = ($paymentMethod === 'counter');

            // For counter payment, create order immediately
            // For online payment, only create payment record first (order created after successful payment)
            if ($paymentMethod === 'counter') {
                // Prepare order data
                $orderData = [
                    'user_id' => $user ? $user->id : null,
                    'guest_name' => $user ? $user->name : ($paymentDetails['name'] ?? 'Guest'),
                    'guest_phone' => $user ? $user->phone : ($paymentDetails['phone'] ?? null),
                    'guest_email' => $user ? $user->email : ($paymentDetails['email'] ?? null),
                    'total_amount' => $totalAmount,
                    'order_status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_method' => $paymentMethod,
                    'order_type' => $paymentDetails['order_type'] ?? 'takeaway',
                    'order_source' => 'web',
                    'order_time' => now(),
                    'confirmation_code' => Order::generateConfirmationCode(),
                ];

                logger()->info('Creating order with data:', $orderData);

                // Create the Order
                $order = Order::create($orderData);

                // Create OrderItems
                foreach ($cartItems as $item) {
                    $menuItem = \App\Models\MenuItem::find($item['id']);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $menuItem->price,
                        'total_price' => $menuItem->price * $item['quantity'],
                        'special_note' => $item['notes'] ?? null,
                        'item_status' => 'pending',
                    ]);
                }

                // Auto-create ETA based on order items
                $order->load('items.menuItem');
                if ($order->items->count() > 0) {
                    $order->autoCreateETA();
                }

                // Create counter payment record
                $paymentData = [
                    'payment_method' => 'counter',
                    'amount' => $totalAmount,
                    'currency' => 'MYR',
                    'payment_status' => 'pending',
                    'gateway' => 'manual',
                ];

                $payment = $this->paymentService->savePaymentData($paymentData, $order->id);

                // Only clear user's cart if this order came from cart checkout
                $isFromCart = $validated['is_from_cart'] ?? false;
                if ($user && $isFromCart) {
                    UserCart::where('user_id', $user->id)->delete();
                }

                DB::commit();

                // Generate a unique order ID for display
                $displayOrderId = 'STG-' . $order->created_at->format('Ymd') . '-' . $order->id;

                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $displayOrderId,
                    'amount' => $totalAmount
                ]);

            } else {
                // Online payment - DO NOT create order yet, only create gateway payment
                // Order will be created in paymentCallback after successful payment

                // Store order data in session for later use after successful payment
                session([
                    'pending_order_data' => [
                        'user_id' => $user ? $user->id : null,
                        'guest_name' => $user ? $user->name : ($paymentDetails['name'] ?? 'Guest'),
                        'guest_phone' => $user ? $user->phone : ($paymentDetails['phone'] ?? null),
                        'guest_email' => $user ? $user->email : ($paymentDetails['email'] ?? null),
                        'total_amount' => $totalAmount,
                        'order_status' => 'pending',
                        'payment_status' => 'paid',
                        'payment_method' => $paymentMethod,
                        'order_type' => $paymentDetails['order_type'] ?? 'takeaway',
                        'order_source' => 'web',
                        'order_time' => now(),
                        'confirmation_code' => Order::generateConfirmationCode(),
                        'cart_items' => $cartItems,
                        'is_from_cart' => $validated['is_from_cart'] ?? false,
                    ]
                ]);

                // Create gateway payment with temporary order reference
                $paymentData = [
                    'payment_method' => 'online',
                    'amount' => $totalAmount,
                    'currency' => 'MYR',
                    'customer_name' => $user ? $user->name : 'Guest',
                    'customer_email' => $paymentDetails['email'] ?? ($user ? $user->email : ''),
                    'customer_phone' => $user ? $user->phone_number : '',
                ];

                // Create payment without order_id (will be linked later)
                $gatewayResult = $this->paymentService->createGatewayPaymentWithoutOrder($paymentData);

                if (!$gatewayResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $gatewayResult['message']
                    ], 400);
                }

                // Store payment ID in session to link with order later
                session(['pending_payment_id' => $gatewayResult['payment_id']]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'redirect_url' => $gatewayResult['redirect_url'],
                    'payment_method' => 'gateway'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception with full details
            logger()->error('Order placement failed: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while placing your order. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle payment return from gateway (customer redirected back)
     */
    public function paymentReturn($paymentId)
    {
        try {
            logger()->info('Payment return accessed', ['payment_id' => $paymentId]);

            $payment = \App\Models\Payment::findOrFail($paymentId);

            logger()->info('Payment found', [
                'payment_id' => $payment->id,
                'bill_code' => $payment->bill_code,
                'current_status' => $payment->payment_status,
                'has_order' => $payment->order_id ? 'yes' : 'no'
            ]);

            // Get latest bill status from Toyyibpay
            $toyyibpayService = new \App\Services\ToyyibpayService();
            $billStatus = $toyyibpayService->getBillStatus($payment->bill_code);

            logger()->info('Bill status retrieved', [
                'bill_code' => $payment->bill_code,
                'status_result' => $billStatus
            ]);

            if ($billStatus['success']) {
                // Update payment status based on current status
                $updateResult = $this->paymentService->updatePaymentStatus(
                    $payment->transaction_id,
                    $billStatus['status'],
                    $billStatus['response']
                );

                logger()->info('Payment status updated', [
                    'transaction_id' => $payment->transaction_id,
                    'new_status' => $billStatus['status'],
                    'update_result' => $updateResult ? 'success' : 'failed'
                ]);

                if ($billStatus['status'] === 'success') {
                    // Payment successful - order should exist (created in callback)
                    return redirect()->route('customer.orders.index')
                        ->with('success', 'Payment completed successfully! Your order is being prepared.');
                } elseif ($billStatus['status'] === 'failed' || $billStatus['status'] === 'pending') {
                    // Payment failed or cancelled - clean up

                    // Delete order if it exists (shouldn't for new flow, but keep for safety)
                    if ($payment->order) {
                        $payment->order->update(['order_status' => 'cancelled']);
                        $payment->order->delete(); // Soft delete
                    }

                    // Delete payment record
                    $payment->delete(); // Soft delete

                    // Clear session data if exists
                    session()->forget(['pending_order_data', 'pending_payment_id']);

                    return redirect()->route('customer.orders.index')
                        ->with('info', 'Payment was not completed. No order was placed. You can order again from the menu.');
                } else {
                    // For any other status
                    return redirect()->route('customer.orders.index')
                        ->with('info', 'Payment is still processing. Please wait for confirmation.');
                }
            }

            logger()->warning('Bill status check failed', ['bill_status' => $billStatus]);

            // If we can't verify status, check if payment is still pending
            if ($payment->payment_status === 'pending') {
                // Delete order if it exists
                if ($payment->order) {
                    $payment->order->update(['order_status' => 'cancelled']);
                    $payment->order->delete();
                }

                // Delete payment
                $payment->delete();

                // Clear session data
                session()->forget(['pending_order_data', 'pending_payment_id']);

                return redirect()->route('customer.orders.index')
                    ->with('info', 'Payment was not completed. No order was placed. You can order again from the menu.');
            }

            return redirect()->route('customer.orders.index')
                ->with('warning', 'Unable to verify payment status. Please contact support if payment was made.');

        } catch (\Exception $e) {
            logger()->error('Payment return error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up session data on error
            session()->forget(['pending_order_data', 'pending_payment_id']);

            // Likely the payment was cancelled/failed
            return redirect()->route('customer.orders.index')
                ->with('info', 'Payment was not completed. No order was placed. You can order again from the menu.');
        }
    }

    /**
     * Handle payment callback from gateway (webhook)
     */
    public function paymentCallback(Request $request)
    {
        try {
            $callbackData = $request->all();
            
            logger()->info('Payment callback received', ['data' => $callbackData]);
            
            $result = $this->paymentService->handleGatewayCallback($callbackData);
            
            if ($result['success']) {
                return response('OK', 200);
            }
            
            return response('FAILED', 400);
            
        } catch (\Exception $e) {
            logger()->error('Payment callback error', [
                'data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response('ERROR', 500);
        }
    }
}
