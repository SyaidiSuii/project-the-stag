<?php

namespace App\Events\Loyalty;

use App\Models\User;
use App\Models\LoyaltyTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PHASE 5: Points Awarded Event
 *
 * Dispatched whenever a user earns loyalty points.
 * Allows decoupled listeners to handle side effects:
 * - Send email/push notifications
 * - Update analytics
 * - Check for tier upgrades
 * - Trigger achievement unlocks
 *
 * Industry Pattern: Event-driven architecture used by Stripe, Shopify
 * to decouple core business logic from side effects.
 */
class PointsAwarded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public int $pointsAwarded;
    public int $newBalance;
    public string $description;
    public ?LoyaltyTransaction $transaction;
    public ?string $referenceType;
    public ?int $referenceId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        int $pointsAwarded,
        int $newBalance,
        string $description,
        ?LoyaltyTransaction $transaction = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ) {
        $this->user = $user;
        $this->pointsAwarded = $pointsAwarded;
        $this->newBalance = $newBalance;
        $this->description = $description;
        $this->transaction = $transaction;
        $this->referenceType = $referenceType;
        $this->referenceId = $referenceId;
    }

    /**
     * Check if points were awarded for an order
     */
    public function isOrderPoints(): bool
    {
        return $this->referenceType === 'App\\Models\\Order';
    }

    /**
     * Check if points were awarded for check-in
     */
    public function isCheckInPoints(): bool
    {
        return str_contains(strtolower($this->description), 'check-in');
    }

    /**
     * Check if points were awarded for an achievement
     */
    public function isAchievementPoints(): bool
    {
        return $this->referenceType === 'App\\Models\\Achievement';
    }

    /**
     * Check if this is a milestone (100, 500, 1000 points)
     */
    public function isMilestone(): bool
    {
        return in_array($this->newBalance, [100, 250, 500, 1000, 2500, 5000, 10000]);
    }
}
