<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use App\Models\TableReservation;
use App\Services\ToyyibpayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    protected $toyyibpayService;

    public function __construct(ToyyibpayService $toyyibpayService)
    {
        $this->toyyibpayService = $toyyibpayService;
    }

    /**
     * Save payment data to database for customer order
     *
     * @param array $paymentData
     * @param int $orderId
     * @param int|null $reservationId
     * @return Payment
     * @throws \Exception
     */
    public function savePaymentData(array $paymentData, int $orderId, int $reservationId = null): Payment
    {
        DB::beginTransaction();

        try {
            // Get the order to ensure it exists
            $order = Order::findOrFail($orderId);

            // Generate unique transaction ID if not provided
            $transactionId = $paymentData['transaction_id'] ?? 'TXN_' . strtoupper(Str::random(10)) . '_' . time();

            // Create payment record
            $payment = Payment::create([
                'order_id' => $orderId,
                'gateway' => $paymentData['gateway'] ?? 'toyyibpay',
                'payment_method' => $paymentData['payment_method'],
                'currency' => $paymentData['currency'] ?? 'MYR',
                'amount' => $paymentData['amount'],
                'bill_code' => $paymentData['bill_code'] ?? null,
                'transaction_id' => $transactionId,
                'payment_status' => $paymentData['payment_status'] ?? 'pending',
                'payment_gateway_response' => $paymentData['gateway_response'] ?? null,
                'paid_at' => $paymentData['payment_status'] === 'completed' ? now() : null,
            ]);

            // Update order payment status (map payment status to order status)
            $orderPaymentStatus = match ($payment->payment_status) {
                'success' => 'paid',
                'failed' => 'unpaid',
                'refunded' => 'refunded',
                default => 'unpaid' // pending, processing -> unpaid
            };

            $order->update([
                'payment_status' => $orderPaymentStatus
            ]);

            // Update reservation status if exists
            if ($reservationId) {
                $reservation = TableReservation::find($reservationId);
                if ($reservation) {
                    $reservation->update([
                        'status' => $payment->payment_status === 'completed' ? 'confirmed' : 'pending'
                    ]);
                }
            }

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update payment status after gateway callback
     *
     * @param string $transactionId
     * @param string $status
     * @param array|null $gatewayResponse
     * @param int|null $reservationId
     * @return Payment|null
     */
    public function updatePaymentStatus(string $transactionId, string $status, array $gatewayResponse = null, int $reservationId = null): ?Payment
    {
        DB::beginTransaction();

        try {
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                return null;
            }

            $payment->update([
                'payment_status' => $status,
                'payment_gateway_response' => $gatewayResponse,
                'paid_at' => $status === 'success' ? now() : $payment->paid_at,
            ]);

            // Update related order
            if ($payment->order) {
                $orderPaymentStatus = match ($status) {
                    'success' => 'paid',
                    'failed' => 'unpaid',
                    'refunded' => 'refunded',
                    default => 'unpaid' // pending, processing -> unpaid
                };

                // Only update payment_status, keep order_status as is (pending)
                $payment->order->update([
                    'payment_status' => $orderPaymentStatus
                ]);
            }

            // Update related reservation if needed
            if ($reservationId) {
                $reservation = TableReservation::find($reservationId);
                if ($reservation) {
                    $reservation->update([
                        'status' => $status === 'completed' ? 'confirmed' : 'pending'
                    ]);
                }
            }

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process refund for a payment
     *
     * @param int $paymentId
     * @param string $reason
     * @param float|null $refundAmount
     * @return Payment
     */
    public function processRefund(int $paymentId, string $reason, float $refundAmount = null): Payment
    {
        DB::beginTransaction();

        try {
            $payment = Payment::findOrFail($paymentId);

            if ($payment->payment_status !== 'completed') {
                throw new \Exception('Cannot refund payment that is not completed');
            }

            $payment->update([
                'payment_status' => 'refunded',
                'refunded_at' => now(),
                'refund_reason' => $reason,
                'amount' => $refundAmount ?? $payment->amount,
            ]);

            // Update order status
            if ($payment->order) {
                $payment->order->update([
                    'payment_status' => 'refunded',
                    'order_status' => 'cancelled'
                ]);
            }

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get payment by order ID
     *
     * @param int $orderId
     * @return Payment|null
     */
    public function getPaymentByOrderId(int $orderId): ?Payment
    {
        return Payment::where('order_id', $orderId)->first();
    }

    /**
     * Get payment by transaction ID
     *
     * @param string $transactionId
     * @return Payment|null
     */
    public function getPaymentByTransactionId(string $transactionId): ?Payment
    {
        return Payment::where('transaction_id', $transactionId)->first();
    }

    /**
     * Create payment with gateway integration (Toyyibpay)
     *
     * @param array $paymentData
     * @param int $orderId
     * @param int|null $reservationId
     * @return array
     */
    public function createGatewayPayment(array $paymentData, int $orderId, int $reservationId = null): array
    {
        DB::beginTransaction();

        try {
            // Get the order
            $order = Order::findOrFail($orderId);

            // Generate unique transaction ID
            $transactionId = 'TXN_' . strtoupper(Str::random(10)) . '_' . time();

            // Create payment record first
            $payment = Payment::create([
                'order_id' => $orderId,
                'gateway' => 'toyyibpay',
                'payment_method' => $paymentData['payment_method'],
                'currency' => $paymentData['currency'] ?? 'MYR',
                'amount' => $paymentData['amount'],
                'transaction_id' => $transactionId,
                'payment_status' => 'pending',
            ]);

            // Prepare bill data for Toyyibpay
            $billData = [
                'bill_name' => "Order #{$order->confirmation_code}",
                'description' => "Payment for The Stag Restaurant - Order #{$order->confirmation_code}",
                'amount' => $paymentData['amount'],
                'reference_no' => $transactionId,
                'return_url' => $paymentData['return_url'] ?? route('payment.return', ['payment' => $payment->id]),
                'callback_url' => route('payment.callback'),
                'customer_name' => $paymentData['customer_name'] ?? ($order->user->name ?? 'Guest'),
                'customer_email' => $paymentData['customer_email'] ?: ($order->user->email ?? 'qr-order@thestag.com'),
                'customer_phone' => $paymentData['customer_phone'] ?: ($order->user->phone_number ?? '60123456789'),
            ];

            // Create bill at Toyyibpay
            $billResult = $this->toyyibpayService->createBill($billData);

            if (!$billResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to create payment bill: ' . $billResult['error']
                ];
            }

            // Update payment with bill code
            $payment->update([
                'bill_code' => $billResult['bill_code'],
                'payment_gateway_response' => $billResult['response'],
            ]);

            // Update order status
            $order->update([
                'payment_status' => 'unpaid' // Waiting for payment
            ]);

            // Update reservation if exists
            if ($reservationId) {
                $reservation = TableReservation::find($reservationId);
                if ($reservation) {
                    $reservation->update([
                        'status' => 'pending'
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'redirect_url' => $billResult['bill_url'],
                'bill_code' => $billResult['bill_code']
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create payment with gateway integration without order (for online payments)
     * Order will be created after successful payment
     *
     * @param array $paymentData
     * @return array
     */
    public function createGatewayPaymentWithoutOrder(array $paymentData): array
    {
        DB::beginTransaction();

        try {
            // Generate unique transaction ID
            $transactionId = 'TXN_' . strtoupper(Str::random(10)) . '_' . time();

            // Create payment record without order_id
            $payment = Payment::create([
                'order_id' => null, // Will be updated after successful payment
                'gateway' => 'toyyibpay',
                'payment_method' => $paymentData['payment_method'],
                'currency' => $paymentData['currency'] ?? 'MYR',
                'amount' => $paymentData['amount'],
                'transaction_id' => $transactionId,
                'payment_status' => 'pending',
            ]);

            // Prepare bill data for Toyyibpay
            $billData = [
                'bill_name' => "The Stag Restaurant Order",
                'description' => "Payment for The Stag Restaurant Order",
                'amount' => $paymentData['amount'],
                'reference_no' => $transactionId,
                'return_url' => $paymentData['return_url'] ?? route('payment.return', ['payment' => $payment->id]),
                'callback_url' => route('payment.callback'),
                'customer_name' => $paymentData['customer_name'] ?? 'Guest',
                'customer_email' => $paymentData['customer_email'] ?: 'order@thestag.com',
                'customer_phone' => $paymentData['customer_phone'] ?: '60123456789',
            ];

            // Create bill at Toyyibpay
            $billResult = $this->toyyibpayService->createBill($billData);

            if (!$billResult['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to create payment bill: ' . $billResult['error']
                ];
            }

            // Update payment with bill code
            $payment->update([
                'bill_code' => $billResult['bill_code'],
                'payment_gateway_response' => $billResult['response'],
            ]);

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'payment_id' => $payment->id,
                'redirect_url' => $billResult['bill_url'],
                'bill_code' => $billResult['bill_code']
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle payment callback from Toyyibpay
     *
     * @param array $callbackData
     * @return array
     */
    public function handleGatewayCallback(array $callbackData): array
    {
        DB::beginTransaction();

        try {
            // Verify callback
            if (!$this->toyyibpayService->verifyCallback($callbackData)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Invalid callback signature'
                ];
            }

            $billCode = $callbackData['billcode'];
            $statusId = $callbackData['status_id'];

            // Find payment by bill code
            $payment = Payment::where('bill_code', $billCode)->first();

            if (!$payment) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Payment record not found'
                ];
            }

            // Map status
            $paymentStatus = match ($statusId) {
                '1' => 'success',
                '2' => 'pending',
                '3' => 'failed',
                default => 'failed'
            };

            // If payment successful and no order exists yet, create the order from session data
            if ($paymentStatus === 'success' && !$payment->order_id) {
                // Get pending order data from session using transaction_id as key
                $pendingOrderData = session('pending_order_data');

                if ($pendingOrderData) {
                    // Create the order
                    $order = Order::create([
                        'user_id' => $pendingOrderData['user_id'],
                        'guest_name' => $pendingOrderData['guest_name'],
                        'guest_phone' => $pendingOrderData['guest_phone'],
                        'guest_email' => $pendingOrderData['guest_email'],
                        'total_amount' => $pendingOrderData['total_amount'],
                        'order_status' => $pendingOrderData['order_status'],
                        'payment_status' => 'paid', // Paid since payment was successful
                        'payment_method' => $pendingOrderData['payment_method'],
                        'order_type' => $pendingOrderData['order_type'],
                        'order_source' => $pendingOrderData['order_source'],
                        'order_time' => $pendingOrderData['order_time'],
                        'confirmation_code' => $pendingOrderData['confirmation_code'],
                    ]);

                    // Create OrderItems
                    foreach ($pendingOrderData['cart_items'] as $item) {
                        $menuItem = \App\Models\MenuItem::find($item['id']);
                        \App\Models\OrderItem::create([
                            'order_id' => $order->id,
                            'menu_item_id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $menuItem->price,
                            'total_price' => $menuItem->price * $item['quantity'],
                            'special_note' => $item['notes'] ?? null,
                            'item_status' => 'pending',
                        ]);
                    }

                    // Auto-create ETA
                    $order->load('items.menuItem');
                    if ($order->items->count() > 0) {
                        $order->autoCreateETA();
                    }

                    // Link payment to order
                    $payment->update(['order_id' => $order->id]);

                    // Award points to user if authenticated (1 point per RM1 spent, with tier multiplier)
                    if ($pendingOrderData['user_id']) {
                        $user = \App\Models\User::find($pendingOrderData['user_id']);
                        if ($user) {
                            $basePoints = floor($pendingOrderData['total_amount']); // 1 point per RM1
                            $user->addPointsWithMultiplier($basePoints, 'Order #' . $order->confirmation_code);
                        }
                    }

                    // Clear user cart if this was from cart checkout
                    if ($pendingOrderData['is_from_cart'] && $pendingOrderData['user_id']) {
                        \App\Models\UserCart::where('user_id', $pendingOrderData['user_id'])->delete();
                    }

                    // Clear session data
                    session()->forget(['pending_order_data', 'pending_payment_id']);
                }
            }

            // Update payment status
            $this->updatePaymentStatus($payment->transaction_id, $paymentStatus, $callbackData);

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'status' => $paymentStatus
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Callback processing failed: ' . $e->getMessage()
            ];
        }
    }
}
