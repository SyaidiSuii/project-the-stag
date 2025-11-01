<?php

namespace App\Events\Loyalty;

use App\Models\User;
use App\Models\Reward;
use App\Models\CustomerReward;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PHASE 5: Reward Redeemed Event
 *
 * Dispatched when a customer redeems a reward.
 * Triggers:
 * - Email notification with redemption code
 * - Push notification
 * - Analytics update
 * - Voucher issuance (if applicable)
 */
class RewardRedeemed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Reward $reward;
    public CustomerReward $customerReward;
    public int $pointsSpent;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        Reward $reward,
        CustomerReward $customerReward,
        int $pointsSpent
    ) {
        $this->user = $user;
        $this->reward = $reward;
        $this->customerReward = $customerReward;
        $this->pointsSpent = $pointsSpent;
    }

    /**
     * Check if reward includes a voucher
     */
    public function hasVoucher(): bool
    {
        return $this->reward->reward_type === 'voucher' && $this->reward->voucher_template_id !== null;
    }

    /**
     * Check if reward is a tier upgrade
     */
    public function isTierUpgrade(): bool
    {
        return $this->reward->reward_type === 'tier_upgrade';
    }

    /**
     * Get redemption code
     */
    public function getRedemptionCode(): string
    {
        return $this->customerReward->redemption_code ?? '';
    }
}
