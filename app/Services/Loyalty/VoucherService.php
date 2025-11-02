<?php

namespace App\Services\Loyalty;

use App\Models\User;
use App\Models\CustomerVoucher;
use App\Models\VoucherTemplate;
use App\Models\CustomerProfile;
use App\Events\Loyalty\VoucherIssued; // PHASE 5: Event import
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 3.3: Voucher Service
 *
 * Handles all voucher-related operations.
 * Manages voucher collection, validation, and usage.
 *
 * Key Responsibilities:
 * - Issue vouchers to customers
 * - Validate voucher eligibility
 * - Apply vouchers to orders
 * - Track voucher usage
 */
class VoucherService
{
    /**
     * Issue a voucher to a customer
     *
     * @param User $user
     * @param VoucherTemplate $template
     * @param string $source Source of voucher (reward, collection, promotion, manual)
     * @return CustomerVoucher
     * @throws \Exception
     */
    public function issueVoucher(User $user, VoucherTemplate $template, string $source = 'manual'): CustomerVoucher
    {
        // Validation
        $this->validateVoucherIssuance($user, $template);

        DB::beginTransaction();
        try {
            // Get or create customer profile
            $customerProfile = $user->customerProfile;
            if (!$customerProfile) {
                $customerProfile = $user->customerProfile()->create([
                    'name' => $user->name,
                    'visit_count' => 0,
                    'total_spent' => 0.00,
                ]);
            }

            // Calculate expiry date
            $expiryDate = $this->calculateExpiryDate($template);

            // Create customer voucher
            $voucher = CustomerVoucher::create([
                'customer_profile_id' => $customerProfile->id,
                'voucher_template_id' => $template->id,
                'source' => $source,
                'status' => 'active',
                'expiry_date' => $expiryDate,
            ]);

            DB::commit();

            Log::info('Voucher issued', [
                'user_id' => $user->id,
                'voucher_id' => $voucher->id,
                'template_id' => $template->id,
                'source' => $source,
            ]);

            // PHASE 5: Dispatch VoucherIssued event
            event(new VoucherIssued(
                $user,
                $voucher,
                $template,
                $source
            ));

            return $voucher;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to issue voucher', [
                'user_id' => $user->id,
                'template_id' => $template->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate if voucher can be issued to user
     *
     * @param User $user
     * @param VoucherTemplate $template
     * @throws \Exception
     */
    protected function validateVoucherIssuance(User $user, VoucherTemplate $template): void
    {
        // Check if template is active
        if (!$template->is_active) {
            throw new \Exception('This voucher is no longer available');
        }

        // Check if template has expired
        if ($template->valid_until && $template->valid_until < now()->toDateString()) {
            throw new \Exception('This voucher offer has expired');
        }

        // Check if template has reached total usage limit
        if ($template->hasReachedLimit()) {
            throw new \Exception('This voucher has reached its usage limit');
        }

        // Check per-user limit
        if ($template->max_uses_per_user) {
            $userUsageCount = $this->getUserVoucherCount($user, $template);
            if ($userUsageCount >= $template->max_uses_per_user) {
                throw new \Exception('You have already collected this voucher the maximum number of times');
            }
        }

        // Check spending requirement for collection vouchers
        if ($template->source_type === 'collection' && $template->spending_requirement) {
            $totalSpent = $this->getUserTotalSpending($user);
            if ($totalSpent < $template->spending_requirement) {
                $amountNeeded = $template->spending_requirement - $totalSpent;
                throw new \Exception("You need to spend RM" . number_format($amountNeeded, 2) . " more to collect this voucher");
            }
        }
    }

    /**
     * Calculate expiry date for voucher
     *
     * @param VoucherTemplate $template
     * @return \Carbon\Carbon|null
     */
    protected function calculateExpiryDate(VoucherTemplate $template): ?\Carbon\Carbon
    {
        // Use valid_until if set
        if ($template->valid_until) {
            return \Carbon\Carbon::parse($template->valid_until);
        }

        // Otherwise use expiry_days from now
        if ($template->expiry_days) {
            return now()->addDays($template->expiry_days);
        }

        // No expiry
        return null;
    }

    /**
     * Get count of vouchers user has from a template
     *
     * @param User $user
     * @param VoucherTemplate $template
     * @return int
     */
    protected function getUserVoucherCount(User $user, VoucherTemplate $template): int
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return 0;
        }

        return CustomerVoucher::where('customer_profile_id', $customerProfile->id)
            ->where('voucher_template_id', $template->id)
            ->whereIn('status', ['active', 'used', 'redeemed']) // Count active and used
            ->count();
    }

    /**
     * Get user's total spending
     *
     * @param User $user
     * @return float
     */
    protected function getUserTotalSpending(User $user): float
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return 0.0;
        }

        return (float) $customerProfile->total_spent;
    }

    /**
     * Apply voucher to an order
     *
     * @param CustomerVoucher $voucher
     * @param float $orderAmount
     * @param int $orderId
     * @return float Discount amount
     * @throws \Exception
     */
    public function applyVoucher(CustomerVoucher $voucher, float $orderAmount, int $orderId): float
    {
        // Validate voucher can be used
        if (!$voucher->isValid($orderAmount)) {
            throw new \Exception('This voucher is not valid for this order');
        }

        // Calculate discount
        $discount = $voucher->calculateDiscount($orderAmount);

        // Mark voucher as used
        DB::beginTransaction();
        try {
            $voucher->markAsUsed();
            $voucher->order_id = $orderId;
            $voucher->save();

            DB::commit();

            Log::info('Voucher applied to order', [
                'voucher_id' => $voucher->id,
                'order_id' => $orderId,
                'discount' => $discount,
            ]);

            return $discount;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user's active vouchers
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveVouchers(User $user)
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return collect();
        }

        return CustomerVoucher::with('voucherTemplate')
            ->where('customer_profile_id', $customerProfile->id)
            ->active()
            ->get();
    }

    /**
     * Get user's voucher history
     *
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVoucherHistory(User $user, int $limit = 10)
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return collect();
        }

        return CustomerVoucher::with('voucherTemplate', 'order')
            ->where('customer_profile_id', $customerProfile->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Find voucher by code
     *
     * @param string $code
     * @return CustomerVoucher|null
     */
    public function findByCode(string $code): ?CustomerVoucher
    {
        return CustomerVoucher::with('voucherTemplate')
            ->where('voucher_code', strtoupper($code))
            ->first();
    }

    /**
     * Validate voucher code
     *
     * @param string $code
     * @param float $orderAmount
     * @return array ['valid' => bool, 'voucher' => CustomerVoucher|null, 'discount' => float, 'message' => string]
     */
    public function validateVoucherCode(string $code, float $orderAmount): array
    {
        $voucher = $this->findByCode($code);

        if (!$voucher) {
            return [
                'valid' => false,
                'voucher' => null,
                'discount' => 0,
                'message' => 'Voucher code not found',
            ];
        }

        if (!$voucher->isValid($orderAmount)) {
            $status = $voucher->status;
            $message = match ($status) {
                'used', 'redeemed' => 'This voucher has already been used',
                'expired' => 'This voucher has expired',
                'cancelled' => 'This voucher has been cancelled',
                default => 'This voucher is not valid',
            };

            // Check minimum order
            if ($status === 'active' && $voucher->voucherTemplate) {
                $minOrder = $voucher->voucherTemplate->minimum_spend ?? 0;
                if ($orderAmount < $minOrder) {
                    $message = "Minimum order amount of RM" . number_format($minOrder, 2) . " required";
                }
            }

            return [
                'valid' => false,
                'voucher' => $voucher,
                'discount' => 0,
                'message' => $message,
            ];
        }

        $discount = $voucher->calculateDiscount($orderAmount);

        return [
            'valid' => true,
            'voucher' => $voucher,
            'discount' => $discount,
            'message' => 'Voucher applied successfully',
        ];
    }

    /**
     * Cancel a voucher
     *
     * @param CustomerVoucher $voucher
     * @return CustomerVoucher
     * @throws \Exception
     */
    public function cancelVoucher(CustomerVoucher $voucher): CustomerVoucher
    {
        if (in_array($voucher->status, ['used', 'redeemed'])) {
            throw new \Exception('Cannot cancel a voucher that has been used');
        }

        $voucher->markAsCancelled();

        Log::info('Voucher cancelled', [
            'voucher_id' => $voucher->id,
        ]);

        return $voucher;
    }

    /**
     * Expire old vouchers (for scheduled task)
     *
     * @return int Number of vouchers expired
     */
    public function expireOldVouchers(): int
    {
        $expiredCount = 0;

        CustomerVoucher::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->chunk(100, function ($vouchers) use (&$expiredCount) {
                foreach ($vouchers as $voucher) {
                    $voucher->update(['status' => 'expired']);
                    $expiredCount++;
                }
            });

        Log::info('Expired old vouchers', ['count' => $expiredCount]);

        return $expiredCount;
    }

    /**
     * Bulk issue vouchers (for promotions)
     *
     * @param array $userIds
     * @param VoucherTemplate $template
     * @param string $source
     * @return int Number of vouchers issued
     */
    public function bulkIssueVouchers(array $userIds, VoucherTemplate $template, string $source = 'promotion'): int
    {
        $issuedCount = 0;

        foreach ($userIds as $userId) {
            try {
                $user = User::find($userId);
                if ($user) {
                    $this->issueVoucher($user, $template, $source);
                    $issuedCount++;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to issue voucher in bulk', [
                    'user_id' => $userId,
                    'template_id' => $template->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue with next user
            }
        }

        Log::info('Bulk vouchers issued', [
            'template_id' => $template->id,
            'total_users' => count($userIds),
            'issued_count' => $issuedCount,
        ]);

        return $issuedCount;
    }
}
