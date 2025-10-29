<?php

namespace App\Http\Controllers\QR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableQrcode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PaymentService;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show payment page for QR orders
     */
    public function showPayment(Request $request)
    {
        $sessionCode = $request->get('session');

        $session = TableQrcode::where('session_code', $sessionCode)
            ->with(['table'])
            ->first();

        if (!$session || !$session->isActive()) {
            return redirect()->route('qr.error')->with('error', 'Session expired.');
        }

        // Get cart data
        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);

        if (empty($cart)) {
            return redirect()->route('qr.cart', ['session' => $session->session_code])
                ->with('error', 'Your cart is empty.');
        }

        $cartTotal = $this->calculateCartTotal($cart);

        // Prepare order data for payment view
        $orderData = [
            'table_number' => $session->table->table_number,
            'total_amount' => $cartTotal,
            'items' => array_values(array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                    'image' => $item['image'] ?? null
                ];
            }, $cart)),
        ];

        return view('qr.payment', compact('session', 'orderData'));
    }

    /**
     * Process payment for QR orders
     */
    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_code' => 'required|exists:table_qrcodes,session_code',
                'payment_method' => 'required|string|in:card,wallet,cash',
                'receipt_email' => 'nullable|email',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('QR Payment validation error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error occurred.',
            ], 422);
        }

        $session = TableQrcode::where('session_code', $validated['session_code'])
            ->with(['table'])
            ->where('status', 'active')
            ->first();

        if (!$session || !$session->isActive()) {
            return response()->json(['success' => false, 'message' => 'Session expired'], 400);
        }

        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        $cartTotal = $this->calculateCartTotal($cart);

        // Create order
        $order = Order::create([
            'user_id' => null, // QR orders don't have user_id
            'table_id' => $session->table_id,
            'table_qrcode_id' => $session->id,
            'order_type' => 'dine_in', // QR table orders are dine_in orders
            'order_source' => 'qr_scan',
            'order_status' => 'pending',
            'order_time' => now(),
            'table_number' => $session->table->table_number,
            'total_amount' => $cartTotal,
            'payment_status' => 'unpaid',
            'session_token' => Str::random(32),
            'confirmation_code' => 'ORD' . strtoupper(Str::random(6)),
        ]);

        // Create order items
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
                'status' => 'pending',
            ]);
        }

        // Clear cart
        session()->forget($cartKey);

        // Handle payment method
        $paymentMethod = $validated['payment_method'];

        if (in_array($paymentMethod, ['card', 'wallet'])) {
            // Online payment - use gateway
            // Store order data in session and delete the order temporarily
            // Order will be recreated only if payment succeeds

            // Store order data before deleting
            session([
                'pending_qr_order_' . $session->session_code => [
                    'order_id' => $order->id,
                    'order_confirmation_code' => $order->confirmation_code,
                    'order_data' => $order->toArray(),
                    'cart_items' => $order->orderItems->map(function($item) {
                        return [
                            'menu_item_id' => $item->menu_item_id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                            'special_note' => $item->special_note,
                        ];
                    })->toArray(),
                ]
            ]);

            // Delete the order items first (foreign key constraint)
            $order->orderItems()->delete();

            // Delete the order (will be recreated on successful payment)
            $orderId = $order->id;
            $orderConfirmationCode = $order->confirmation_code;
            $order->delete();

            \Log::info('QR Order temporarily deleted for online payment', [
                'order_id' => $orderId,
                'session' => $session->session_code
            ]);

            $paymentData = [
                'payment_method' => $paymentMethod,
                'amount' => $cartTotal,
                'currency' => 'MYR',
                'customer_name' => 'Table Customer',
                'customer_email' => !empty($validated['receipt_email']) ? $validated['receipt_email'] : 'qr-order@thestag.com',
                'customer_phone' => '60123456789', // Default phone for QR orders
                'gateway' => 'toyyibpay',
                'payment_status' => 'pending',
                'return_url' => route('qr.payment.confirmation', [
                    'session' => $session->session_code,
                    'order' => 'pending' // Placeholder since order doesn't exist yet
                ]),
            ];

            try {
                // Create payment with ToyyibPay gateway integration (without order_id)
                $gatewayResult = $this->paymentService->createGatewayPayment($paymentData, null);

                if (!$gatewayResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $gatewayResult['message']
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'order_id' => $orderConfirmationCode,
                    'amount' => $cartTotal,
                    'redirect_url' => $gatewayResult['redirect_url'], // ToyyibPay URL
                    'bill_code' => $gatewayResult['bill_code']
                ]);
            } catch (\Exception $e) {
                \Log::error('QR Payment processing failed: ' . $e->getMessage(), [
                    'order_id' => $orderId,
                    'exception' => $e,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed. Please try again.',
                ], 500);
            }
        } else {
            // Cash payment - mark as unpaid
            $order->update(['payment_status' => 'unpaid', 'order_status' => 'pending']);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully! Please pay at the restaurant.',
                'order_id' => $order->confirmation_code,
                'amount' => $cartTotal,
                'redirect_url' => route('qr.payment.confirmation', [
                    'session' => $session->session_code,
                    'order' => $order->id
                ])
            ]);
        }
    }

    /**
     * Show confirmation page for QR orders
     */
    public function showConfirmation(Request $request)
    {
        $sessionCode = $request->get('session');
        $orderId = $request->get('order');

        $session = TableQrcode::where('session_code', $sessionCode)
            ->with(['table'])
            ->first();

        if (!$session || !$session->isActive()) {
            return redirect()->route('qr.error')->with('error', 'Session expired.');
        }

        // Check if this is a pending online payment order
        if ($orderId === 'pending') {
            // Check if payment was successful and recreate order from session
            $orderSessionKey = 'pending_qr_order_' . $session->session_code;
            $pendingOrderData = session($orderSessionKey);

            if (!$pendingOrderData) {
                return redirect()->route('qr.guest.menu', ['session' => $session->session_code])
                    ->with('error', 'Payment session expired. Please try again.');
            }

            // Check payment status via payment gateway
            // For now, we assume if user reaches here, payment was successful
            // TODO: Verify payment status with ToyyibPay API

            // Recreate the order
            $orderData = $pendingOrderData['order_data'];
            $order = Order::create([
                'user_id' => $orderData['user_id'],
                'table_id' => $orderData['table_id'],
                'table_qrcode_id' => $session->id,
                'order_type' => $orderData['order_type'],
                'order_source' => $orderData['order_source'] ?? 'qr_scan',
                'order_status' => 'pending', // Will be updated by payment callback
                'order_time' => $orderData['order_time'] ?? now(),
                'table_number' => $orderData['table_number'] ?? $session->table->table_number,
                'payment_status' => 'paid', // Assuming payment successful
                'total_amount' => $orderData['total_amount'],
                'guest_name' => $orderData['guest_name'] ?? null,
                'guest_phone' => $orderData['guest_phone'] ?? null,
                'session_token' => $orderData['session_token'],
                'confirmation_code' => $orderData['confirmation_code'],
                'special_instructions' => $orderData['special_instructions'] ?? null,
            ]);

            // Recreate order items
            foreach ($pendingOrderData['cart_items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $itemData['menu_item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['total_price'],
                    'special_note' => $itemData['special_note'] ?? null,
                    'status' => 'pending',
                ]);
            }

            // Clear session data
            session()->forget($orderSessionKey);

            \Log::info('QR Order recreated after successful payment', [
                'order_id' => $order->id,
                'session' => $session->session_code
            ]);

            // Redirect to confirmation with actual order ID
            return redirect()->route('qr.payment.confirmation', [
                'session' => $session->session_code,
                'order' => $order->id
            ]);
        }

        $order = Order::where('id', $orderId)
            ->where('table_qrcode_id', $session->id)
            ->first();

        if (!$order) {
            return redirect()->route('qr.guest.menu', ['session' => $session->session_code])
                ->with('error', 'Order not found.');
        }

        return view('qr.confirmation', compact('session', 'order'));
    }

    /**
     * Calculate cart total
     */
    private function calculateCartTotal($cart)
    {
        $total = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        // Round to 2 decimal places for currency
        return round($total, 2);
    }
}
