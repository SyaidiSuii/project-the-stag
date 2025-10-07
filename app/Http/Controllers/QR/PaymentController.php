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
                    'order' => $order->id
                ]),
            ];

            try {
                // Create payment with ToyyibPay gateway integration
                $gatewayResult = $this->paymentService->createGatewayPayment($paymentData, $order->id);

                if (!$gatewayResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $gatewayResult['message']
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'order_id' => $order->confirmation_code,
                    'amount' => $cartTotal,
                    'redirect_url' => $gatewayResult['redirect_url'], // ToyyibPay URL
                    'bill_code' => $gatewayResult['bill_code']
                ]);
            } catch (\Exception $e) {
                \Log::error('QR Payment processing failed: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'exception' => $e,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed. Please try again.',
                ], 500);
            }
        } else {
            // Cash payment - mark as unpaid
            $order->update(['payment_status' => 'unpaid', 'order_status' => 'confirmed']);

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

        $order = Order::where('id', $orderId)
            ->where('table_qrcode_id', $session->id)
            ->first();

        if (!$order) {
            return redirect()->route('qr.menu', ['session' => $session->session_code])
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
