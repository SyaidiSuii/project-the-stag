<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusPointChallenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'condition',
        'condition_type',
        'min_requirement',
        'bonus_points',
        'max_claims_per_user',
        'max_claims_total',
        'current_claims',
        'end_date',
        'status'
    ];

    protected $casts = [
        'bonus_points' => 'integer',
        'min_requirement' => 'integer',
        'max_claims_per_user' => 'integer',
        'max_claims_total' => 'integer',
        'current_claims' => 'integer',
        'end_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        return $query->where(function($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
        });
    }

    /**
     * Get all user claims for this challenge
     */
    public function userClaims()
    {
        return $this->hasMany(UserBonusChallengeCall::class);
    }

    /**
     * Check if user has already claimed this challenge
     */
    public function hasBeenClaimedBy(User $user): bool
    {
        return $this->userClaims()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get number of times user has claimed this challenge
     */
    public function getClaimCountByUser(User $user): int
    {
        return $this->userClaims()
            ->where('user_id', $user->id)
            ->count();
    }

    /**
     * Check if challenge has reached total claim limit
     */
    public function hasReachedTotalLimit(): bool
    {
        if ($this->max_claims_total === 0) {
            return false; // Unlimited
        }
        return $this->current_claims >= $this->max_claims_total;
    }

    /**
     * Check if user has reached their personal claim limit
     */
    public function hasUserReachedLimit(User $user): bool
    {
        if ($this->max_claims_per_user === 0) {
            return false; // Unlimited
        }
        return $this->getClaimCountByUser($user) >= $this->max_claims_per_user;
    }

    /**
     * Check if user meets the requirement to claim this challenge
     */
    public function doesUserMeetRequirement(User $user): bool
    {
        switch ($this->condition_type) {
            case 'orders':
                $orderCount = $user->orders()->where('payment_status', 'paid')->count();
                return $orderCount >= $this->min_requirement;

            case 'spending':
                $totalSpending = $user->orders()->where('payment_status', 'paid')->sum('total_amount');
                return $totalSpending >= $this->min_requirement;

            case 'visits':
                // Assuming visits are tracked via orders or check-ins
                $visitCount = $user->orders()->where('payment_status', 'paid')->distinct('created_at')->count('DATE(created_at)');
                return $visitCount >= $this->min_requirement;

            case 'checkin_streak':
                return ($user->checkin_streak ?? 0) >= $this->min_requirement;

            case 'referrals':
                // Assuming referrals are tracked via a referrals relationship
                $referralCount = $user->referrals()->count() ?? 0;
                return $referralCount >= $this->min_requirement;

            case 'custom':
            default:
                // For custom conditions, assume requirement is met
                // Admin should manually verify
                return true;
        }
    }

    /**
     * Check if user is eligible to claim this challenge
     */
    public function isEligibleFor(User $user): array
    {
        // Check if challenge is active
        if ($this->status !== 'active') {
            return ['eligible' => false, 'reason' => 'Challenge is no longer active'];
        }

        // Check if expired
        if ($this->end_date && $this->end_date < now()) {
            return ['eligible' => false, 'reason' => 'Challenge has expired'];
        }

        // Check if total limit reached
        if ($this->hasReachedTotalLimit()) {
            return ['eligible' => false, 'reason' => 'Challenge claim limit reached'];
        }

        // Check if user reached their limit
        if ($this->hasUserReachedLimit($user)) {
            return ['eligible' => false, 'reason' => 'You have already claimed this challenge the maximum number of times'];
        }

        // Check if user meets requirement
        if (!$this->doesUserMeetRequirement($user)) {
            return ['eligible' => false, 'reason' => 'You have not met the requirement for this challenge'];
        }

        return ['eligible' => true];
    }
}