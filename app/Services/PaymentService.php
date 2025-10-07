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
            $orderPaymentStatus = match($payment->payment_status) {
                'completed' => 'paid',
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
                'paid_at' => $status === 'completed' ? now() : $payment->paid_at,
            ]);

            // Update related order
            if ($payment->order) {
                $orderPaymentStatus = match($status) {
                    'completed' => 'paid',
                    'failed' => 'unpaid',
                    'refunded' => 'refunded',
                    default => 'unpaid' // pending, processing -> unpaid
                };
                
                $payment->order->update([
                    'payment_status' => $orderPaymentStatus,
                    'order_status' => $status === 'completed' ? 'confirmed' : $payment->order->order_status
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
     * Handle payment callback from Toyyibpay
     *
     * @param array $callbackData
     * @return array
     */
    public function handleGatewayCallback(array $callbackData): array
    {
        try {
            // Verify callback
            if (!$this->toyyibpayService->verifyCallback($callbackData)) {
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
                return [
                    'success' => false,
                    'message' => 'Payment record not found'
                ];
            }

            // Map status
            $paymentStatus = match($statusId) {
                '1' => 'completed',
                '2' => 'pending',
                '3' => 'failed',
                default => 'failed'
            };

            // Update payment status
            $this->updatePaymentStatus($payment->transaction_id, $paymentStatus, $callbackData);

            return [
                'success' => true,
                'payment' => $payment,
                'status' => $paymentStatus
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Callback processing failed: ' . $e->getMessage()
            ];
        }
    }
}