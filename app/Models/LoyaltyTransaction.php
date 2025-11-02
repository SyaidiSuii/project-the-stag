<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'customer_profile_id',
        'transaction_type',
        'points_change',
        'description',
        'reference_id',
        'reference_type',
        'balance_after',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'points_change' => 'integer',
        'balance_after' => 'integer'
    ];

    // Relationships
    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    // Scopes
    public function scopeEarnPoints($query)
    {
        return $query->where('transaction_type', 'earn_points');
    }

    public function scopeRedeemPoints($query)
    {
        return $query->where('transaction_type', 'redeem_points');
    }

    public function scopeExpirePoints($query)
    {
        return $query->where('transaction_type', 'expire_points');
    }

    public function scopeRedeemVoucher($query)
    {
        return $query->where('transaction_type', 'redeem_voucher');
    }

    // Helper methods
    /**
     * PHASE 1.5: Updated to use users.points_balance instead of customer_profiles.loyalty_points
     *
     * @deprecated This method is deprecated. Points are now tracked via UserObserver automatically.
     *             Use LoyaltyPointsService instead for manual point operations.
     */
    public static function logTransaction($customerProfileId, $type, $pointsChange, $description, $referenceId = null, $referenceType = null)
    {
        $customerProfile = CustomerProfile::find($customerProfileId);

        if (!$customerProfile) {
            return false;
        }

        // PHASE 1.5: Use users.points_balance instead of loyalty_points
        $user = $customerProfile->user;
        if (!$user) {
            return false;
        }

        // Calculate new balance
        $currentBalance = $user->points_balance ?? 0;
        $newBalance = $currentBalance + $pointsChange;

        // Update user points (this will trigger UserObserver)
        $user->update(['points_balance' => $newBalance]);

        // Note: Transaction record is now created automatically by UserObserver
        // This method kept for backward compatibility but will be removed in Phase 3

        return true;
    }
}