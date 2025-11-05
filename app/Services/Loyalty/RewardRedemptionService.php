<?php

namespace App\Services\Loyalty;

use App\Models\User;
use App\Models\Reward;
use App\Models\CustomerReward;
use App\Models\CustomerVoucher;
use App\Models\CustomerProfile;
use App\Events\Loyalty\RewardRedeemed; // PHASE 5: Event import
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 3.2: Reward Redemption Service
 *
 * Handles all reward redemption operations.
 * Coordinates between points deduction and reward issuance.
 *
 * Key Responsibilities:
 * - Validate redemption eligibility
 * - Process reward claims (deduct points + create customer reward)
 * - Handle different reward types (vouchers, tier upgrades, etc.)
 * - Track redemption history
 */
class RewardRedemptionService
{
    protected LoyaltyService $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Redeem a reward for a user
     *
     * @param User $user
     * @param Reward $reward
     * @return CustomerReward
     * @throws \Exception
     */
    public function redeemReward(User $user, Reward $reward): CustomerReward
    {
        // Validation
        $this->validateRedemption($user, $reward);

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

            // Deduct points
            $this->loyaltyService->deductPoints(
                $user,
                $reward->points_required,
                "Redeemed reward: {$reward->title}",
                'App\\Models\\Reward',
                $reward->id
            );

            // Create customer reward record
            $customerReward = CustomerReward::create([
                'customer_profile_id' => $customerProfile->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_required,
                'status' => 'active', // PHASE 2.3: Start as active (or pending if approval needed)
                'claimed_at' => now(),
                'expires_at' => $reward->expiry_days ? now()->addDays($reward->expiry_days) : null,
            ]);

            // FIX: Create CustomerVoucher record for voucher-type rewards
            // This ensures customer gets a proper voucher_code that can be applied to cart
            if ($reward->reward_type === 'voucher' && $reward->voucher_template_id) {
                // Generate unique voucher code
                $voucherCode = 'RWD-' . strtoupper(uniqid());

                // Create CustomerVoucher record with the actual voucher code
                $customerVoucher = CustomerVoucher::create([
                    'customer_profile_id' => $customerProfile->id,
                    'voucher_template_id' => $reward->voucher_template_id,
                    'voucher_code' => $voucherCode,
                    'status' => 'active',
                    'source' => 'reward', // Mark as reward-issued voucher
                    'expiry_date' => $reward->expiry_days ? now()->addDays($reward->expiry_days)->toDateString() : null,
                    'redeemed_at' => null,
                ]);

                // Update CustomerReward with voucher_code for reference
                $customerReward->update([
                    'redemption_code' => $voucherCode
                ]);

                Log::info('Voucher-type reward: CustomerVoucher created', [
                    'customer_reward_id' => $customerReward->id,
                    'customer_voucher_id' => $customerVoucher->id,
                    'voucher_code' => $voucherCode,
                    'voucher_template_id' => $reward->voucher_template_id,
                ]);
            }

            // Handle bonus points type rewards (instant redemption)
            if ($reward->reward_type === 'points' && $reward->reward_value > 0) {
                $this->loyaltyService->awardPoints(
                    $user,
                    $reward->reward_value,
                    "Bonus points from reward: {$reward->title}",
                    'App\\Models\\Reward',
                    $reward->id
                );

                // Mark as redeemed immediately since benefit is instant
                $customerReward->update([
                    'status' => 'redeemed',
                    'redeemed_at' => now()
                ]);

                Log::info('Bonus points awarded from reward redemption - marked as redeemed', [
                    'customer_reward_id' => $customerReward->id,
                    'bonus_points' => $reward->reward_value,
                ]);
            }

            // Handle free product type rewards - create free product entitlement
            if ($reward->reward_type === 'product' && $reward->menu_item_id) {
                \App\Models\CustomerFreeProduct::create([
                    'customer_profile_id' => $customerProfile->id,
                    'reward_id' => $reward->id,
                    'customer_reward_id' => $customerReward->id,
                    'menu_item_id' => $reward->menu_item_id,
                    'status' => 'available',
                    'expires_at' => $reward->expiry_days ? now()->addDays($reward->expiry_days) : null,
                ]);

                Log::info('Free product entitlement created from reward redemption', [
                    'customer_reward_id' => $customerReward->id,
                    'menu_item_id' => $reward->menu_item_id,
                ]);
            }

            DB::commit();

            Log::info('Reward redeemed successfully', [
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'customer_reward_id' => $customerReward->id,
                'points_spent' => $reward->points_required,
            ]);

            // PHASE 5: Dispatch RewardRedeemed event
            event(new RewardRedeemed(
                $user,
                $reward,
                $customerReward,
                $reward->points_required
            ));

            return $customerReward;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reward redemption failed', [
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate if user can redeem a reward
     *
     * @param User $user
     * @param Reward $reward
     * @throws \Exception
     */
    protected function validateRedemption(User $user, Reward $reward): void
    {
        // Check if reward is active
        if (!$reward->is_active) {
            throw new \Exception('This reward is no longer available');
        }

        // Check if reward has expired
        if ($reward->expires_at && $reward->expires_at < now()) {
            throw new \Exception('This reward has expired');
        }

        // PHASE 7: Check tier requirements
        if ($reward->required_tier_id) {
            if (!$user->loyalty_tier_id) {
                throw new \Exception('This reward requires a loyalty tier membership');
            }

            // Get tier hierarchy order
            $userTierOrder = $user->loyaltyTier?->order ?? 0;
            $requiredTierOrder = $reward->requiredTier?->order ?? 0;

            if ($userTierOrder < $requiredTierOrder) {
                $requiredTierName = $reward->requiredTier?->name ?? 'higher tier';
                throw new \Exception("This reward is exclusive to {$requiredTierName} members and above");
            }
        }

        // Check if user has enough points
        if (!$this->loyaltyService->hasEnoughPoints($user, $reward->points_required)) {
            $currentBalance = $this->loyaltyService->getBalance($user);
            $needed = $reward->points_required - $currentBalance;
            throw new \Exception("You need {$needed} more points to redeem this reward");
        }

        // Check usage limit (per user)
        if ($reward->usage_limit) {
            $userRedemptions = $this->getUserRedemptionCount($user, $reward);
            if ($userRedemptions >= $reward->usage_limit) {
                throw new \Exception('You have reached the maximum redemption limit for this reward');
            }
        }

        // Check max redemptions (global)
        if ($reward->max_redemptions) {
            $totalRedemptions = $this->getTotalRedemptionCount($reward);
            if ($totalRedemptions >= $reward->max_redemptions) {
                throw new \Exception('This reward has reached its maximum redemption limit');
            }
        }
    }

    /**
     * Get number of times user has redeemed a specific reward
     *
     * @param User $user
     * @param Reward $reward
     * @return int
     */
    public function getUserRedemptionCount(User $user, Reward $reward): int
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return 0;
        }

        return CustomerReward::where('customer_profile_id', $customerProfile->id)
            ->where('reward_id', $reward->id)
            ->whereIn('status', ['active', 'redeemed']) // Don't count cancelled/expired
            ->count();
    }

    /**
     * Get total redemption count for a reward
     *
     * @param Reward $reward
     * @return int
     */
    public function getTotalRedemptionCount(Reward $reward): int
    {
        return CustomerReward::where('reward_id', $reward->id)
            ->whereIn('status', ['active', 'redeemed'])
            ->count();
    }

    /**
     * Mark a customer reward as redeemed (used by staff)
     *
     * @param CustomerReward $customerReward
     * @return CustomerReward
     * @throws \Exception
     */
    public function markAsRedeemed(CustomerReward $customerReward): CustomerReward
    {
        if ($customerReward->status !== 'active') {
            throw new \Exception("Cannot redeem reward with status: {$customerReward->status}");
        }

        // Check if expired
        if ($customerReward->expires_at && $customerReward->expires_at < now()) {
            $customerReward->markAsExpired();
            throw new \Exception('This reward has expired');
        }

        $customerReward->markAsRedeemed();

        Log::info('Customer reward marked as redeemed', [
            'customer_reward_id' => $customerReward->id,
            'reward_id' => $customerReward->reward_id,
        ]);

        return $customerReward;
    }

    /**
     * Cancel a customer reward
     *
     * @param CustomerReward $customerReward
     * @param bool $refundPoints
     * @return CustomerReward
     * @throws \Exception
     */
    public function cancelRedemption(CustomerReward $customerReward, bool $refundPoints = true): CustomerReward
    {
        if ($customerReward->status === 'redeemed') {
            throw new \Exception('Cannot cancel a reward that has already been redeemed');
        }

        DB::beginTransaction();
        try {
            // Refund points if requested
            if ($refundPoints && $customerReward->points_spent > 0) {
                $user = $customerReward->customerProfile->user;
                if ($user) {
                    $this->loyaltyService->awardPoints(
                        $user,
                        $customerReward->points_spent,
                        "Refund for cancelled reward: {$customerReward->reward->title}",
                        'App\\Models\\CustomerReward',
                        $customerReward->id
                    );
                }
            }

            // Mark as cancelled
            $customerReward->markAsCancelled();

            DB::commit();

            Log::info('Customer reward cancelled', [
                'customer_reward_id' => $customerReward->id,
                'refund_points' => $refundPoints,
                'points_refunded' => $refundPoints ? $customerReward->points_spent : 0,
            ]);

            return $customerReward;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user's active rewards
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveRewards(User $user)
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return collect();
        }

        return CustomerReward::with('reward')
            ->where('customer_profile_id', $customerProfile->id)
            ->active()
            ->get();
    }

    /**
     * Get user's redemption history
     *
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRedemptionHistory(User $user, int $limit = 10)
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return collect();
        }

        return CustomerReward::with('reward')
            ->where('customer_profile_id', $customerProfile->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Expire old rewards (for scheduled task)
     *
     * @return int Number of rewards expired
     */
    public function expireOldRewards(): int
    {
        $expiredCount = 0;

        CustomerReward::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->chunk(100, function ($rewards) use (&$expiredCount) {
                foreach ($rewards as $reward) {
                    $reward->markAsExpired();
                    $expiredCount++;
                }
            });

        Log::info('Expired old rewards', ['count' => $expiredCount]);

        return $expiredCount;
    }

    /**
     * PHASE 7: Get available rewards for user based on tier
     *
     * Returns rewards that the user is eligible to redeem based on:
     * - Active status
     * - Tier requirements
     * - Point balance
     * - Usage limits
     *
     * Industry Pattern: Tiered rewards catalogs (Amazon Prime tiers,
     * airline status levels, hotel loyalty programs)
     *
     * @param User $user
     * @param bool $onlyAffordable Filter to only rewards user has enough points for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableRewards(User $user, bool $onlyAffordable = false)
    {
        $userTierOrder = $user->loyaltyTier?->order ?? 0;
        $userPoints = $this->loyaltyService->getBalance($user);

        $query = Reward::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) use ($userTierOrder) {
                // Include rewards with no tier requirement OR tier requirement user meets
                $q->whereNull('required_tier_id')
                  ->orWhereHas('requiredTier', function ($tierQuery) use ($userTierOrder) {
                      $tierQuery->where('order', '<=', $userTierOrder);
                  });
            });

        if ($onlyAffordable) {
            $query->where('points_required', '<=', $userPoints);
        }

        // Filter out rewards that have reached global max redemptions
        $query->where(function ($q) {
            $q->whereNull('max_redemptions')
              ->orWhereRaw('(SELECT COUNT(*) FROM customer_rewards WHERE reward_id = rewards.id AND status IN (?, ?)) < max_redemptions', ['active', 'redeemed']);
        });

        $rewards = $query->with('requiredTier')->get();

        // Further filter by per-user usage limits
        $customerProfile = $user->customerProfile;
        if ($customerProfile) {
            $rewards = $rewards->filter(function ($reward) use ($user) {
                if ($reward->usage_limit) {
                    $userRedemptions = $this->getUserRedemptionCount($user, $reward);
                    return $userRedemptions < $reward->usage_limit;
                }
                return true;
            });
        }

        return $rewards;
    }

    /**
     * PHASE 7: Get tier-exclusive rewards
     *
     * Returns rewards that are exclusive to specific tiers.
     * Useful for showcasing premium benefits.
     *
     * @param int|null $tierId Filter by specific tier (null = all tier-exclusive)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTierExclusiveRewards(?int $tierId = null)
    {
        $query = Reward::where('is_active', true)
            ->whereNotNull('required_tier_id');

        if ($tierId) {
            $query->where('required_tier_id', $tierId);
        }

        return $query->with('requiredTier')
            ->orderBy('points_required')
            ->get();
    }
}
