<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity; // PHASE 1.6: Activity logging
use Spatie\Activitylog\LogOptions;

class Reward extends Model
{
    use HasFactory, SoftDeletes, LogsActivity; // PHASE 1.6: Added activity logging

    protected $fillable = [
        // 'name', // REMOVED: Phase 2.4 - Redundant with 'title'
        'title',
        'description',
        'reward_type',
        'reward_value',
        'minimum_order',
        'points_required',
        'voucher_template_id',
        'menu_item_id', // For product type - which menu item is given free
        'required_tier_id', // PHASE 7: Tier-exclusive rewards
        'expiry_days',
        'usage_limit',
        'max_redemptions',
        'redemption_method',
        'terms_conditions',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
        'expiry_days' => 'integer',
        'reward_value' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'usage_limit' => 'integer',
        'max_redemptions' => 'integer',
        'expires_at' => 'datetime'
    ];

    /**
     * PHASE 2.4: Get name attribute (alias for title for backward compatibility)
     * The 'name' column was removed, but we keep this accessor for legacy code
     */
    public function getNameAttribute()
    {
        return $this->attributes['title'] ?? null;
    }

    /**
     * Check if user has reached redemption limit for this reward
     */
    public function getIsLimitReachedAttribute()
    {
        if (!$this->usage_limit) {
            return false;
        }

        $userRedemptions = $this->user_redemptions_count ?? 0;
        return $userRedemptions >= $this->usage_limit;
    }

    public function voucherTemplate()
    {
        return $this->belongsTo(VoucherTemplate::class);
    }

    /**
     * Menu item for "product" type rewards
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * PHASE 7: Tier-exclusive rewards relationship
     * Defines minimum loyalty tier required to redeem this reward
     */
    public function requiredTier()
    {
        return $this->belongsTo(LoyaltyTier::class, 'required_tier_id');
    }

    public function customerRewards()
    {
        return $this->hasMany(CustomerReward::class);
    }

    /**
     * PHASE 1.6: Configure activity logging
     * Logs all changes to rewards for admin audit trail
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'reward_type', 'points_required', 'is_active', 'reward_value', 'usage_limit', 'expires_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}