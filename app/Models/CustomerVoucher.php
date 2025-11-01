<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * PHASE 2.3: Standard Status Lifecycle
 *
 * Status Flow:
 * active → used/redeemed → expired
 *    ↓           ↓
 * cancelled  cancelled
 *
 * - active: Voucher is ready to use
 * - used: Voucher has been applied to an order (alias for redeemed)
 * - redeemed: Voucher has been applied to an order (legacy name)
 * - expired: Passed expiry date without usage
 * - cancelled: Cancelled by admin or system
 *
 * Note: 'used' and 'redeemed' are functionally equivalent.
 * Both values kept for backward compatibility and semantic clarity.
 */
class CustomerVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'voucher_template_id',
        'source',
        'voucher_code',
        'status',
        'expiry_date',
        // 'redeemed_at', // REMOVED: Phase 2.4 - Redundant with 'used_at'
        'order_id',
        'used_at'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        // 'redeemed_at' => 'datetime', // REMOVED: Phase 2.4
        'used_at' => 'datetime'
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function voucherTemplate()
    {
        return $this->belongsTo(VoucherTemplate::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // PHASE 2.3: Standardized Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($query) {
                        $query->whereNull('expiry_date')
                              ->orWhere('expiry_date', '>=', now());
                    });
    }

    public function scopeRedeemed($query)
    {
        // PHASE 2.3: Include both 'redeemed' and 'used' statuses
        return $query->whereIn('status', ['redeemed', 'used']);
    }

    public function scopeUsed($query)
    {
        // PHASE 2.3: Alias for redeemed scope
        return $this->scopeRedeemed($query);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expiry_date', '<', now());
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Generate unique voucher code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            if (empty($voucher->voucher_code)) {
                $voucher->voucher_code = self::generateUniqueCode();
            }
            if (empty($voucher->status)) {
                $voucher->status = 'active';
            }
        });
    }

    private static function generateUniqueCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
        } while (self::where('voucher_code', $code)->exists());

        return $code;
    }

    // Check if voucher is valid
    public function isValid($orderAmount = 0)
    {
        // Check status
        if ($this->status !== 'active') {
            return false;
        }

        // Check expiry
        if ($this->expiry_date && $this->expiry_date < now()) {
            $this->update(['status' => 'expired']);
            return false;
        }

        // Check minimum order amount if voucher template has minimum requirement
        if ($this->voucherTemplate && $this->voucherTemplate->minimum_order && $orderAmount < $this->voucherTemplate->minimum_order) {
            return false;
        }

        return true;
    }

    // Calculate discount amount
    public function calculateDiscount($orderAmount)
    {
        if (!$this->isValid($orderAmount) || !$this->voucherTemplate) {
            return 0;
        }

        if ($this->voucherTemplate->discount_type === 'percentage') {
            return ($orderAmount * $this->voucherTemplate->discount_value) / 100;
        } else {
            return min($this->voucherTemplate->discount_value, $orderAmount);
        }
    }

    // PHASE 2.3/2.4: Mark voucher as redeemed/used
    public function markAsRedeemed()
    {
        // PHASE 2.4: redeemed_at removed, use used_at only
        $this->update([
            'status' => 'redeemed',
            'used_at' => now()
        ]);
    }

    public function markAsUsed()
    {
        // PHASE 2.3: Use 'used' status (more semantic than 'redeemed')
        // PHASE 2.4: redeemed_at removed, use used_at only
        $this->update([
            'status' => 'used',
            'used_at' => now()
        ]);
    }

    /**
     * PHASE 2.4: Accessor for backward compatibility
     * Legacy code may reference redeemed_at - return used_at instead
     */
    public function getRedeemedAtAttribute()
    {
        return $this->attributes['used_at'] ?? null;
    }

    public function markAsCancelled()
    {
        $this->update([
            'status' => 'cancelled'
        ]);
    }
}