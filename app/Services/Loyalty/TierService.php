<?php

namespace App\Services\Loyalty;

use App\Models\User;
use App\Models\LoyaltyTier;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 7: Tier Service
 *
 * Handles loyalty tier management and automatic upgrades.
 *
 * Key Responsibilities:
 * - Calculate user's eligible tier based on points/spending
 * - Automatic tier upgrades
 * - Tier benefits calculation (points multiplier)
 * - Tier-exclusive rewards filtering
 *
 * Industry Pattern: Tiered loyalty systems (Marriott Bonvoy, Starbucks Rewards)
 * automatically upgrade customers and apply tier-specific benefits.
 */
class TierService
{
    /**
     * Calculate and update user's tier based on points and spending
     *
     * @param User $user
     * @return array ['upgraded' => bool, 'old_tier' => LoyaltyTier|null, 'new_tier' => LoyaltyTier|null]
     */
    public function calculateAndUpdateTier(User $user): array
    {
        $currentTier = $user->loyaltyTier;
        $eligibleTier = $this->calculateEligibleTier($user);

        // No change needed
        if ($currentTier && $eligibleTier && $currentTier->id === $eligibleTier->id) {
            return [
                'upgraded' => false,
                'old_tier' => $currentTier,
                'new_tier' => $currentTier,
            ];
        }

        // Update tier
        $user->update(['loyalty_tier_id' => $eligibleTier?->id]);

        // Check if this is an upgrade
        $isUpgrade = $this->isUpgrade($currentTier, $eligibleTier);

        if ($isUpgrade) {
            Log::info('User tier upgraded', [
                'user_id' => $user->id,
                'old_tier' => $currentTier?->name,
                'new_tier' => $eligibleTier?->name,
            ]);
        }

        return [
            'upgraded' => $isUpgrade,
            'old_tier' => $currentTier,
            'new_tier' => $eligibleTier,
        ];
    }

    /**
     * Calculate eligible tier for user based on points and spending
     *
     * @param User $user
     * @return LoyaltyTier|null
     */
    public function calculateEligibleTier(User $user): ?LoyaltyTier
    {
        $points = $user->points_balance ?? 0;
        $spending = $user->customerProfile->total_spent ?? 0;

        // Get all active tiers ordered by min_points descending
        $tiers = LoyaltyTier::active()
            ->ordered()
            ->orderBy('min_points', 'desc')
            ->get();

        // Find highest tier user qualifies for
        foreach ($tiers as $tier) {
            $meetsPoints = $points >= $tier->min_points;
            $meetsSpending = !$tier->min_spending || $spending >= $tier->min_spending;

            if ($meetsPoints && $meetsSpending) {
                return $tier;
            }
        }

        return null; // No tier qualification
    }

    /**
     * Check if new tier is higher than old tier
     *
     * @param LoyaltyTier|null $oldTier
     * @param LoyaltyTier|null $newTier
     * @return bool
     */
    protected function isUpgrade(?LoyaltyTier $oldTier, ?LoyaltyTier $newTier): bool
    {
        if (!$oldTier && $newTier) {
            return true; // First tier
        }

        if (!$newTier) {
            return false; // Downgrade or no tier
        }

        return $newTier->min_points > $oldTier->min_points;
    }

    /**
     * Get points multiplier for user's tier
     *
     * @param User $user
     * @return float Default 1.0 if no tier or no multiplier
     */
    public function getPointsMultiplier(User $user): float
    {
        $tier = $user->loyaltyTier;

        if (!$tier) {
            return 1.0;
        }

        return $tier->points_multiplier ?? 1.0;
    }

    /**
     * Calculate tier progress for user
     *
     * @param User $user
     * @return array
     */
    public function getTierProgress(User $user): array
    {
        $currentTier = $user->loyaltyTier;
        $nextTier = $this->getNextTier($user);

        $points = $user->points_balance ?? 0;
        $spending = $user->customerProfile->total_spent ?? 0;

        if (!$nextTier) {
            return [
                'current_tier' => $currentTier,
                'next_tier' => null,
                'points_needed' => 0,
                'spending_needed' => 0,
                'points_progress' => 100,
                'spending_progress' => 100,
                'at_max_tier' => true,
            ];
        }

        $pointsNeeded = max(0, $nextTier->min_points - $points);
        $spendingNeeded = max(0, ($nextTier->min_spending ?? 0) - $spending);

        $pointsProgress = $nextTier->min_points > 0
            ? min(100, ($points / $nextTier->min_points) * 100)
            : 100;

        $spendingProgress = $nextTier->min_spending > 0
            ? min(100, ($spending / $nextTier->min_spending) * 100)
            : 100;

        return [
            'current_tier' => $currentTier,
            'next_tier' => $nextTier,
            'points_needed' => $pointsNeeded,
            'spending_needed' => $spendingNeeded,
            'points_progress' => round($pointsProgress, 2),
            'spending_progress' => round($spendingProgress, 2),
            'at_max_tier' => false,
        ];
    }

    /**
     * Get next tier above user's current tier
     *
     * @param User $user
     * @return LoyaltyTier|null
     */
    protected function getNextTier(User $user): ?LoyaltyTier
    {
        $currentTier = $user->loyaltyTier;
        $currentMinPoints = $currentTier->min_points ?? 0;

        return LoyaltyTier::active()
            ->where('min_points', '>', $currentMinPoints)
            ->orderBy('min_points', 'asc')
            ->first();
    }

    /**
     * Get tier benefits description
     *
     * @param LoyaltyTier $tier
     * @return array
     */
    public function getTierBenefits(LoyaltyTier $tier): array
    {
        $benefits = [];

        // Points multiplier
        if ($tier->points_multiplier && $tier->points_multiplier > 1.0) {
            $benefits[] = [
                'type' => 'points_multiplier',
                'value' => $tier->points_multiplier,
                'description' => "Earn {$tier->points_multiplier}x points on all purchases",
            ];
        }

        // Exclusive rewards
        $exclusiveRewardsCount = \App\Models\Reward::where('required_tier_id', $tier->id)
            ->where('is_active', true)
            ->count();

        if ($exclusiveRewardsCount > 0) {
            $benefits[] = [
                'type' => 'exclusive_rewards',
                'value' => $exclusiveRewardsCount,
                'description' => "Access to {$exclusiveRewardsCount} exclusive rewards",
            ];
        }

        // Custom benefits from tier's benefits field
        if ($tier->benefits) {
            $benefits[] = [
                'type' => 'custom',
                'value' => null,
                'description' => $tier->benefits,
            ];
        }

        return $benefits;
    }

    /**
     * Check all users and upgrade tiers where applicable
     *
     * @return int Number of users upgraded
     */
    public function batchUpdateTiers(): int
    {
        $upgradedCount = 0;

        User::whereNotNull('points_balance')
            ->with('loyaltyTier', 'customerProfile')
            ->chunk(100, function ($users) use (&$upgradedCount) {
                foreach ($users as $user) {
                    $result = $this->calculateAndUpdateTier($user);
                    if ($result['upgraded']) {
                        $upgradedCount++;
                    }
                }
            });

        Log::info('Batch tier update completed', ['upgraded_count' => $upgradedCount]);

        return $upgradedCount;
    }
}
