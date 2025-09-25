<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TableReservation;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display the payment page for a booking order.
     */
    public function index($orderId)
    {
        $user = Auth::user();
        
        // Get the order with related data
        $order = Order::with(['items.menuItem', 'table', 'reservation'])
            ->where('id', $orderId)
            ->where('user_id', $user ? $user->id : null)
            ->first();

        if (!$order) {
            return redirect()->route('customer.orders.index')
                ->with('error', 'Order not found or access denied.');
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.orders.index')
                ->with('info', 'This order has already been paid.');
        }

        // Prepare order data for the payment view (same format as cart payment)
        $orderData = [
            'id' => $order->id,
            'confirmation_code' => $order->confirmation_code,
            'table_number' => $order->table_number,
            'total_amount' => $order->total_amount,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->menu_item_id,
                    'name' => $item->menuItem->name ?? 'Unknown Item',
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total_price,
                    'image' => $item->menuItem->image ?? null,
                    'notes' => $item->special_note ?? '',
                ];
            })->toArray(),
            'booking_type' => 'dine_in',
            'reservation_code' => $order->reservation ? $order->reservation->confirmation_code : null,
            'is_vvip' => $order->table && $order->table->table_type === 'vip' ? true : false,
        ];

        // Return the dedicated booking payment view
        return view('customer.booking.payment', compact('orderData'));
    }

    /**
     * Process payment for a booking order.
     */
    public function processPayment(Request $request, $orderId)
    {
        $validated = $request->validate([
            'payment_details' => 'required|array',
            'payment_details.method' => 'required|string|in:card,wallet,cash',
            'payment_details.email' => 'nullable|email',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            
            // Get the order
            $order = Order::where('id', $orderId)
                ->where('user_id', $user ? $user->id : null)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or access denied.'
                ], 404);
            }

            // Check if already paid
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'This order has already been paid.'
                ], 400);
            }

            // Handle different payment methods
            $paymentMethod = $validated['payment_details']['method'];
            $reservationId = $order->reservation ? $order->reservation->id : null;
            
            if (in_array($paymentMethod, ['card', 'wallet'])) {
                // Online payment - use gateway
                $paymentData = [
                    'payment_method' => $paymentMethod,
                    'amount' => $order->total_amount,
                    'currency' => 'MYR',
                    'customer_name' => $user ? $user->name : 'Guest',
                    'customer_email' => $validated['payment_details']['email'] ?? ($user ? $user->email : ''),
                    'customer_phone' => $user ? $user->phone_number : '',
                ];

                $gatewayResult = $this->paymentService->createGatewayPayment($paymentData, $order->id, $reservationId);
                
                if (!$gatewayResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $gatewayResult['message']
                    ], 400);
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
                    'amount' => $order->total_amount,
                    'currency' => 'MYR',
                    'payment_status' => 'pending',
                    'gateway' => 'manual',
                ];
                
                $payment = $this->paymentService->savePaymentData($paymentData, $order->id, $reservationId);

                // Update payment status to completed for manual process
                $this->paymentService->updatePaymentStatus($payment->transaction_id, 'completed', null, $reservationId);
            }

            // Update payment status
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed', // Move to confirmed after payment
            ]);

            // Update reservation status if exists
            if ($order->reservation) {
                $order->reservation->update([
                    'status' => 'confirmed'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!',
                'order_id' => $order->confirmation_code ?? 'ORD-' . $order->id,
                'redirect_url' => route('customer.orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            logger()->error('Booking payment processing failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => $user ? $user->id : null,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}