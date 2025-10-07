<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibpayService
{
    private $secretKey;
    private $categoryCode;
    private $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.toyyibpay.secret_key');
        $this->categoryCode = config('services.toyyibpay.category_code');
        $this->baseUrl = config('services.toyyibpay.base_url');
    }

    /**
     * Create bill at Toyyibpay
     *
     * @param array $billData
     * @return array
     */
    public function createBill(array $billData): array
    {
        try {
            $data = [
                'userSecretKey' => $this->secretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => $billData['bill_name'],
                'billDescription' => $billData['description'],
                'billPriceSetting' => 1, // Fixed price
                'billPayorInfo' => 1, // Require payer info
                'billAmount' => $billData['amount'] * 100, // Convert to cents
                'billReturnUrl' => $billData['return_url'],
                'billCallbackUrl' => $billData['callback_url'],
                'billExternalReferenceNo' => $billData['reference_no'],
                'billTo' => $billData['customer_name'] ?? '',
                'billEmail' => $billData['customer_email'] ?? '',
                'billPhone' => $billData['customer_phone'] ?? '',
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => '0', // All channels
                'billContentEmail' => 'Thank you for your payment to The Stag Restaurant!',
                'billChargeToCustomer' => 1, // Customer pays the fee
            ];

            $response = Http::asForm()->post($this->baseUrl . '/createBill', $data);

            $result = $response->json();

            Log::info('Toyyibpay createBill response', [
                'status' => $response->status(),
                'response' => $result
            ]);

            if ($response->successful() && isset($result[0]['BillCode'])) {
                return [
                    'success' => true,
                    'bill_code' => $result[0]['BillCode'],
                    'bill_url' => "https://dev.toyyibpay.com/{$result[0]['BillCode']}",
                    'response' => $result[0]
                ];
            }

            return [
                'success' => false,
                'error' => $result[0]['msg'] ?? 'Unknown error occurred',
                'response' => $result
            ];
        } catch (\Exception $e) {
            Log::error('Toyyibpay createBill error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create payment bill: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get bill status from Toyyibpay
     *
     * @param string $billCode
     * @return array
     */
    public function getBillStatus(string $billCode): array
    {
        try {
            $data = [
                'billCode' => $billCode
            ];

            $response = Http::asForm()->post($this->baseUrl . '/getBillTransactions', $data);
            $result = $response->json();

            Log::info('Toyyibpay getBillStatus response', [
                'bill_code' => $billCode,
                'response' => $result
            ]);

            if ($response->successful() && !empty($result)) {
                $transaction = $result[0];
                return [
                    'success' => true,
                    'status' => $this->mapBillStatus($transaction['billpaymentStatus']),
                    'transaction_id' => $transaction['billExternalReferenceNo'] ?? null,
                    'amount' => ($transaction['billPaymentAmount'] ?? 0) / 100, // Convert from cents
                    'paid_at' => $transaction['billPaymentDate'] ?? null,
                    'response' => $transaction
                ];
            }

            return [
                'success' => false,
                'error' => 'No transaction found',
                'response' => $result
            ];
        } catch (\Exception $e) {
            Log::error('Toyyibpay getBillStatus error', [
                'bill_code' => $billCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to get bill status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify callback signature (for security)
     *
     * @param array $callbackData
     * @return bool
     */
    public function verifyCallback(array $callbackData): bool
    {
        // Implement signature verification if Toyyibpay provides it
        // For now, basic validation
        return isset($callbackData['billcode']) &&
            isset($callbackData['status_id']) &&
            isset($callbackData['order_id']);
    }

    /**
     * Map Toyyibpay bill status to our payment status
     *
     * @param string $billStatus
     * @return string
     */
    private function mapBillStatus(string $billStatus): string
    {
        return match ($billStatus) {
            '1' => 'completed', // Successful payment
            '2' => 'pending',   // Pending payment
            '3' => 'failed',    // Failed payment
            default => 'pending'
        };
    }

    /**
     * Cancel bill at Toyyibpay (if supported)
     *
     * @param string $billCode
     * @return array
     */
    public function cancelBill(string $billCode): array
    {
        // Implementation depends on Toyyibpay API support for cancellation
        return [
            'success' => false,
            'error' => 'Bill cancellation not implemented'
        ];
    }
}
