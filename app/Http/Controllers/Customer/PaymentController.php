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
        
        // Basic validation
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_details' => 'required|array',
            'payment_details.method' => 'required|string|in:card,wallet,cash',
            'payment_details.email' => 'nullable|email',
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

            // Create the Order
            $order = Order::create([
                'user_id' => $user ? $user->id : null, // Handle guest users
                'guest_name' => $user ? $user->name : null,
                'guest_phone' => $user ? $user->phone : null,
                'total_amount' => $totalAmount,
                'order_status' => 'pending', // Changed to confirmed since payment is processed
                'payment_status' => 'paid', // Set as paid since payment is being processed
                'order_type' => 'takeaway', // Fixed: use valid enum value
                'order_source' => 'web', // Fixed: use valid enum value
                'order_time' => now(),
                'confirmation_code' => Order::generateConfirmationCode(), // Generate confirmation code
                // Add other necessary fields like table_session_id if applicable
            ]);

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

            // Handle different payment methods
            $paymentMethod = $paymentDetails['method'];
            
            if (in_array($paymentMethod, ['card', 'wallet'])) {
                // Online payment - use gateway
                $paymentData = [
                    'payment_method' => $paymentMethod,
                    'amount' => $totalAmount,
                    'currency' => 'MYR',
                    'customer_name' => $user ? $user->name : 'Guest',
                    'customer_email' => $paymentDetails['email'] ?? ($user ? $user->email : ''),
                    'customer_phone' => $user ? $user->phone_number : '',
                ];

                $gatewayResult = $this->paymentService->createGatewayPayment($paymentData, $order->id);
                
                if (!$gatewayResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $gatewayResult['message']
                    ], 400);
                }

                // Clear user's cart if logged in
                if ($user) {
                    UserCart::where('user_id', $user->id)->delete();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'redirect_url' => $gatewayResult['redirect_url'],
                    'payment_method' => 'gateway'
                ]);

            } else {
                // Manual payment (cash at restaurant)
                $paymentData = [
                    'payment_method' => $paymentMethod,
                    'amount' => $totalAmount,
                    'currency' => 'MYR',
                    'payment_status' => 'pending',
                    'gateway' => 'manual',
                ];

                $payment = $this->paymentService->savePaymentData($paymentData, $order->id);
            }

            // Clear user's cart if logged in
            if ($user) {
                UserCart::where('user_id', $user->id)->delete();
            }

            DB::commit();

            // Generate a unique order ID for display
            $displayOrderId = 'STG-' . $order->created_at->format('Ymd') . '-' . $order->id;

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $displayOrderId
            ]);

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
            $payment = \App\Models\Payment::findOrFail($paymentId);
            
            // Get latest bill status from Toyyibpay
            $toyyibpayService = new \App\Services\ToyyibpayService();
            $billStatus = $toyyibpayService->getBillStatus($payment->bill_code);
            
            if ($billStatus['success']) {
                // Update payment status based on current status
                $this->paymentService->updatePaymentStatus(
                    $payment->transaction_id, 
                    $billStatus['status'],
                    $billStatus['response']
                );
                
                if ($billStatus['status'] === 'completed') {
                    return redirect()->route('customer.orders.index')
                        ->with('success', 'Payment completed successfully!');
                } else {
                    return redirect()->route('customer.orders.index')
                        ->with('info', 'Payment is still processing. Please wait for confirmation.');
                }
            }
            
            return redirect()->route('customer.orders.index')
                ->with('warning', 'Unable to verify payment status. Please contact support if payment was made.');
                
        } catch (\Exception $e) {
            logger()->error('Payment return error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('customer.orders.index')
                ->with('error', 'An error occurred while processing your return. Please contact support.');
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
