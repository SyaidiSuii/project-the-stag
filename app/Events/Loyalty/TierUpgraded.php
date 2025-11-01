<?php

namespace App\Events\Loyalty;

use App\Models\User;
use App\Models\LoyaltyTier;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PHASE 7: Tier Upgraded Event
 *
 * Dispatched when a customer is upgraded to a higher loyalty tier.
 * Triggers:
 * - Congratulations email
 * - Push notification
 * - Unlock tier-exclusive rewards
 * - Award upgrade bonus points (if applicable)
 *
 * Industry Pattern: Marriott Bonvoy, Starbucks Rewards send
 * congratulations when customers reach new tier levels.
 */
class TierUpgraded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public ?LoyaltyTier $oldTier;
    public LoyaltyTier $newTier;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        ?LoyaltyTier $oldTier,
        LoyaltyTier $newTier
    ) {
        $this->user = $user;
        $this->oldTier = $oldTier;
        $this->newTier = $newTier;
    }

    /**
     * Check if this is first tier (from no tier)
     */
    public function isFirstTier(): bool
    {
        return $this->oldTier === null;
    }

    /**
     * Get tier upgrade level (number of tiers jumped)
     */
    public function getUpgradeLevel(): int
    {
        if ($this->isFirstTier()) {
            return 1;
        }

        return abs(($this->newTier->order ?? 0) - ($this->oldTier->order ?? 0));
    }

    /**
     * Get new tier benefits
     */
    public function getNewBenefits(): array
    {
        $benefits = [];

        if ($this->newTier->points_multiplier && $this->newTier->points_multiplier > 1.0) {
            $benefits[] = "Earn {$this->newTier->points_multiplier}x points on purchases";
        }

        if ($this->newTier->benefits) {
            $benefits[] = $this->newTier->benefits;
        }

        return $benefits;
    }
}
