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
    public static function logTransaction($customerProfileId, $type, $pointsChange, $description, $referenceId = null, $referenceType = null)
    {
        $customerProfile = CustomerProfile::find($customerProfileId);
        
        if (!$customerProfile) {
            return false;
        }

        // Calculate new balance
        $newBalance = $customerProfile->loyalty_points + $pointsChange;
        
        // Update customer profile points
        $customerProfile->update(['loyalty_points' => $newBalance]);

        // Create transaction record
        return self::create([
            'customer_profile_id' => $customerProfileId,
            'transaction_type' => $type,
            'points_change' => $pointsChange,
            'description' => $description,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'balance_after' => $newBalance,
            'created_at' => now()
        ]);
    }
}