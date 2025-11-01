<?php

namespace App\Services\Loyalty;

use App\Models\User;
use App\Models\LoyaltyTransaction;
use App\Models\CustomerProfile;
use App\Events\Loyalty\PointsAwarded; // PHASE 5: Event import
use App\Events\Loyalty\TierUpgraded; // PHASE 7: Tier event import
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 3.1: Loyalty Points Service
 *
 * Centralized service for all loyalty points operations.
 * Provides consistent interface for earning, spending, and tracking points.
 *
 * Key Responsibilities:
 * - Award points (orders, check-ins, achievements)
 * - Deduct points (reward redemptions)
 * - Calculate tier upgrades (PHASE 7: automatic via TierService)
 * - Automatic transaction logging via UserObserver
 */
class LoyaltyService
{
    /**
     * PHASE 7: TierService dependency injection
     */
    protected TierService $tierService;

    public function __construct(TierService $tierService)
    {
        $this->tierService = $tierService;
    }
    /**
     * Award points to a user
     *
     * @param User $user
     * @param int $points Amount of points to award
     * @param string $description Reason for awarding points
     * @param string|null $referenceType Model type (Order, Achievement, etc.)
     * @param int|null $referenceId Model ID
     * @return LoyaltyTransaction
     * @throws \Exception
     */
    public function awardPoints(
        User $user,
        int $points,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): LoyaltyTransaction {
        if ($points <= 0) {
            throw new \InvalidArgumentException('Points must be greater than 0');
        }

        DB::beginTransaction();
        try {
            // Update user points balance
            $oldBalance = $user->points_balance ?? 0;
            $newBalance = $oldBalance + $points;

            $user->points_balance = $newBalance;
            $user->save();

            // Get or create customer profile
            $customerProfile = $user->customerProfile;
            if (!$customerProfile) {
                $customerProfile = $user->customerProfile()->create([
                    'name' => $user->name,
                    'visit_count' => 0,
                    'total_spent' => 0.00,
                ]);
            }

            // Transaction will be auto-logged by UserObserver
            // But we need to update the description and reference
            $transaction = LoyaltyTransaction::where('customer_profile_id', $customerProfile->id)
                ->where('points_change', $points)
                ->where('balance_after', $newBalance)
                ->latest()
                ->first();

            if ($transaction) {
                $transaction->update([
                    'description' => $description,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                ]);
            }

            DB::commit();

            Log::info('Points awarded', [
                'user_id' => $user->id,
                'points' => $points,
                'new_balance' => $newBalance,
                'description' => $description,
            ]);

            // PHASE 5: Dispatch PointsAwarded event
            event(new PointsAwarded(
                $user,
                $points,
                $newBalance,
                $description,
                $transaction,
                $referenceType,
                $referenceId
            ));

            // PHASE 7: Check for tier upgrades
            $this->checkAndProcessTierUpgrade($user);

            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award points', [
                'user_id' => $user->id,
                'points' => $points,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Deduct points from a user
     *
     * @param User $user
     * @param int $points Amount of points to deduct
     * @param string $description Reason for deducting points
     * @param string|null $referenceType Model type (Reward, etc.)
     * @param int|null $referenceId Model ID
     * @return LoyaltyTransaction
     * @throws \Exception
     */
    public function deductPoints(
        User $user,
        int $points,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): LoyaltyTransaction {
        if ($points <= 0) {
            throw new \InvalidArgumentException('Points must be greater than 0');
        }

        $currentBalance = $user->points_balance ?? 0;
        if ($currentBalance < $points) {
            throw new \Exception("Insufficient points. Current balance: {$currentBalance}, Required: {$points}");
        }

        DB::beginTransaction();
        try {
            // Update user points balance
            $oldBalance = $user->points_balance ?? 0;
            $newBalance = $oldBalance - $points;

            $user->points_balance = $newBalance;
            $user->save();

            // Get customer profile
            $customerProfile = $user->customerProfile;
            if (!$customerProfile) {
                throw new \Exception('Customer profile not found');
            }

            // Transaction will be auto-logged by UserObserver
            // But we need to update the description and reference
            $transaction = LoyaltyTransaction::where('customer_profile_id', $customerProfile->id)
                ->where('points_change', -$points)
                ->where('balance_after', $newBalance)
                ->latest()
                ->first();

            if ($transaction) {
                $transaction->update([
                    'description' => $description,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                ]);
            }

            DB::commit();

            Log::info('Points deducted', [
                'user_id' => $user->id,
                'points' => $points,
                'new_balance' => $newBalance,
                'description' => $description,
            ]);

            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to deduct points', [
                'user_id' => $user->id,
                'points' => $points,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculate points earned from order amount
     *
     * @param float $orderAmount
     * @return int Points earned
     */
    public function calculatePointsFromOrder(float $orderAmount): int
    {
        // Default: 1 point per RM1 spent
        // TODO: Make this configurable via settings
        return (int) floor($orderAmount);
    }

    /**
     * Get user's current points balance
     *
     * @param User $user
     * @return int
     */
    public function getBalance(User $user): int
    {
        return $user->points_balance ?? 0;
    }

    /**
     * Get user's transaction history
     *
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionHistory(User $user, int $limit = 10)
    {
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return collect();
        }

        return LoyaltyTransaction::where('customer_profile_id', $customerProfile->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if user has enough points
     *
     * @param User $user
     * @param int $requiredPoints
     * @return bool
     */
    public function hasEnoughPoints(User $user, int $requiredPoints): bool
    {
        return $this->getBalance($user) >= $requiredPoints;
    }

    /**
     * Award check-in points
     *
     * @param User $user
     * @param int $points
     * @return LoyaltyTransaction
     */
    public function awardCheckInPoints(User $user, int $points): LoyaltyTransaction
    {
        return $this->awardPoints(
            $user,
            $points,
            "Daily check-in bonus: {$points} points",
            null,
            null
        );
    }

    /**
     * Award order completion points
     *
     * @param User $user
     * @param float $orderAmount
     * @param int $orderId
     * @return LoyaltyTransaction|null
     */
    public function awardOrderPoints(User $user, float $orderAmount, int $orderId): ?LoyaltyTransaction
    {
        $points = $this->calculatePointsFromOrder($orderAmount);

        if ($points <= 0) {
            return null;
        }

        return $this->awardPoints(
            $user,
            $points,
            "Order #$orderId completed - earned {$points} points from RM" . number_format($orderAmount, 2),
            'App\\Models\\Order',
            $orderId
        );
    }

    /**
     * Award achievement points
     *
     * @param User $user
     * @param int $achievementId
     * @param int $points
     * @param string $achievementName
     * @return LoyaltyTransaction
     */
    public function awardAchievementPoints(
        User $user,
        int $achievementId,
        int $points,
        string $achievementName
    ): LoyaltyTransaction {
        return $this->awardPoints(
            $user,
            $points,
            "Achievement unlocked: {$achievementName}",
            'App\\Models\\Achievement',
            $achievementId
        );
    }

    /**
     * Expire old points (for scheduled task)
     *
     * @param User $user
     * @param int $points
     * @param string $reason
     * @return LoyaltyTransaction
     */
    public function expirePoints(User $user, int $points, string $reason): LoyaltyTransaction
    {
        return $this->deductPoints(
            $user,
            $points,
            "Points expired: {$reason}",
            null,
            null
        );
    }

    /**
     * PHASE 7: Check for tier upgrades and dispatch event
     *
     * Automatically called after awarding points.
     * Checks if user qualifies for a higher tier and processes upgrade.
     *
     * Industry Pattern: Automatic tier upgrades are standard in loyalty programs
     * (Starbucks Gold, Marriott Platinum, etc.)
     *
     * @param User $user
     * @return void
     */
    protected function checkAndProcessTierUpgrade(User $user): void
    {
        try {
            $result = $this->tierService->calculateAndUpdateTier($user);

            if ($result['upgraded']) {
                Log::info('Customer tier upgraded', [
                    'user_id' => $user->id,
                    'old_tier' => $result['old_tier']?->name ?? 'None',
                    'new_tier' => $result['new_tier']->name,
                ]);

                // Dispatch TierUpgraded event
                event(new TierUpgraded(
                    $user,
                    $result['old_tier'],
                    $result['new_tier']
                ));
            }

        } catch (\Exception $e) {
            // Don't fail the entire points transaction if tier check fails
            Log::error('Failed to check tier upgrade', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * PHASE 7: Calculate points with tier multiplier
     *
     * Calculates points from order amount, applying tier-based multiplier.
     * This replaces the basic calculatePointsFromOrder method.
     *
     * @param User $user
     * @param float $orderAmount
     * @return int Points earned (with tier multiplier applied)
     */
    public function calculatePointsFromOrderWithTier(User $user, float $orderAmount): int
    {
        $basePoints = $this->calculatePointsFromOrder($orderAmount);
        $multiplier = $this->tierService->getPointsMultiplier($user);

        $totalPoints = (int) floor($basePoints * $multiplier);

        Log::info('Points calculated with tier multiplier', [
            'user_id' => $user->id,
            'tier' => $user->loyaltyTier?->name ?? 'None',
            'base_points' => $basePoints,
            'multiplier' => $multiplier,
            'total_points' => $totalPoints,
        ]);

        return $totalPoints;
    }
}
