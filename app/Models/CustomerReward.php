<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * PHASE 2.3: Standard Status Lifecycle
 *
 * Status Flow:
 * pending → active → redeemed
 *     ↓        ↓        ↓
 * cancelled  expired  expired
 *
 * - pending: Just claimed, awaiting activation/verification
 * - active: Ready to use, not yet redeemed
 * - redeemed: Successfully used by customer
 * - expired: Passed expiry date without redemption
 * - cancelled: Cancelled by admin or system
 */
class CustomerReward extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     *
     * NOTE: Redemption code generation removed as it's no longer needed.
     * All reward types (voucher, product, points) have their own tracking:
     * - Voucher → CustomerVoucher table
     * - Product → CustomerFreeProduct table
     * - Points → Instant credit (no redemption needed)
     */
    protected static function boot()
    {
        parent::boot();

        // Removed: Auto-generate redemption code
        // Reason: Redundant - each reward type has dedicated tracking
    }

    protected $fillable = [
        'customer_profile_id',
        'reward_id',
        'points_spent',
        // 'redemption_code', // REMOVED: No longer needed - each reward type has dedicated tracking
        'status',
        'claimed_at',
        'expires_at',
        // 'expiry_date', // REMOVED: Legacy field (Phase 1.4) - Use expires_at instead
        'redeemed_at'
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
        // 'expiry_date' => 'date', // REMOVED: Legacy field (Phase 1.4)
        'points_spent' => 'integer'
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Alias for reward relationship (for backward compatibility with views)
     * Some views use "exchangePoint" naming
     */
    public function exchangePoint()
    {
        return $this->reward();
    }

    /**
     * Get exchangePoint as an attribute (accessor)
     * This allows $customerReward->exchangePoint to work like a relationship
     */
    public function getExchangePointAttribute()
    {
        return $this->reward;
    }

    // PHASE 2.3: Standardized Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeRedeemed($query)
    {
        return $query->where('status', 'redeemed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expires_at', '<', now());
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // PHASE 2.3: Status Management Methods
    public function markAsActive()
    {
        $this->update(['status' => 'active']);
    }

    public function markAsRedeemed()
    {
        $this->update([
            'status' => 'redeemed',
            'redeemed_at' => now()
        ]);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }
}