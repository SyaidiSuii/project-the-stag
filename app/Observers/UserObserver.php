<?php

namespace App\Observers;

use App\Models\User;
use App\Models\LoyaltyTransaction;

/**
 * PHASE 1.5: User Observer for Audit Logging
 *
 * Automatically logs ALL points_balance changes to loyalty_transactions table.
 * This provides complete audit trail for points earning, spending, and adjustments.
 *
 * Industry Standard: Banking systems MUST log every balance change for compliance.
 */
class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * Triggered whenever a User model is updated via save() or update().
     * Checks if points_balance has changed and logs it.
     */
    public function updated(User $user): void
    {
        // Check if points_balance was modified
        if ($user->isDirty('points_balance')) {
            $this->logPointsChange($user);
        }
    }

    /**
     * Log points balance change to loyalty_transactions
     */
    protected function logPointsChange(User $user): void
    {
        // Get old and new values
        $oldBalance = $user->getOriginal('points_balance') ?? 0;
        $newBalance = $user->points_balance ?? 0;
        $pointsChange = $newBalance - $oldBalance;

        // Determine transaction type
        $transactionType = $pointsChange > 0 ? 'earn_points' : 'redeem_points';

        // Get customer profile ID (required for loyalty_transactions)
        $customerProfile = $user->customerProfile;

        if (!$customerProfile) {
            // If no customer profile exists, create one
            $customerProfile = $user->customerProfile()->create([
                'name' => $user->name,
                'visit_count' => 0,
                'total_spent' => 0.00,
            ]);
        }

        // Create loyalty transaction record
        LoyaltyTransaction::create([
            'customer_profile_id' => $customerProfile->id,
            'transaction_type' => $transactionType,
            'points_change' => $pointsChange,
            'description' => $this->generateDescription($pointsChange),
            'balance_after' => $newBalance,
            'reference_id' => null, // Can be set by services if available
            'reference_type' => null,
        ]);
    }

    /**
     * Generate default description for automatic logging
     */
    protected function generateDescription(int $pointsChange): string
    {
        if ($pointsChange > 0) {
            return "Earned {$pointsChange} points";
        } else {
            return "Spent " . abs($pointsChange) . " points";
        }
    }
}
