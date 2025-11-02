<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'description',
        'source_type', // PHASE 2.1: Added for unified structure
        'discount_type',
        'discount_value',
        'applicable_menu_item_ids', // For free_item type: which items can be free
        'minimum_spend',
        'max_discount',
        'spending_requirement', // PHASE 2.1: From VoucherCollection
        'terms_conditions',
        'expiry_days',
        'valid_until', // PHASE 2.1: From VoucherCollection
        'is_active', // PHASE 2.1: From VoucherCollection
        'max_uses_per_user', // PHASE 2.1: Added for usage limits
        'total_uses_limit' // PHASE 2.1: Added for usage limits
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'applicable_menu_item_ids' => 'array', // JSON array of menu item IDs
        'minimum_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'spending_requirement' => 'decimal:2', // PHASE 2.1
        'expiry_days' => 'integer',
        'valid_until' => 'date', // PHASE 2.1
        'is_active' => 'boolean', // PHASE 2.1
        'max_uses_per_user' => 'integer', // PHASE 2.1
        'total_uses_limit' => 'integer' // PHASE 2.1
    ];

    public function rewards()
    {
        return $this->hasMany(Reward::class, 'voucher_template_id');
    }

    // PHASE 2.1: Query Scopes for Unified Voucher Structure

    /**
     * Scope: Get only reward-based vouchers
     */
    public function scopeRewardVouchers($query)
    {
        return $query->where('source_type', 'reward');
    }

    /**
     * Scope: Get only collection-based vouchers (replaces VoucherCollection)
     */
    public function scopeCollectionVouchers($query)
    {
        return $query->where('source_type', 'collection');
    }

    /**
     * Scope: Get promotion vouchers
     */
    public function scopePromotionVouchers($query)
    {
        return $query->where('source_type', 'promotion');
    }

    /**
     * Scope: Get active vouchers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get valid (non-expired) vouchers
     */
    public function scopeValid($query)
    {
        return $query->where(function($query) {
            $query->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now()->toDateString());
        });
    }

    /**
     * Check if voucher is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < now()->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Check if voucher has reached usage limit
     */
    public function hasReachedLimit(): bool
    {
        if ($this->total_uses_limit === null) {
            return false;
        }

        // Count total redemptions across all customers
        $totalUses = CustomerVoucher::where('voucher_template_id', $this->id)
            ->whereIn('status', ['used', 'redeemed'])
            ->count();

        return $totalUses >= $this->total_uses_limit;
    }

    /**
     * Get applicable menu items for free_item type vouchers
     */
    public function applicableMenuItems()
    {
        if ($this->discount_type !== 'free_item' || empty($this->applicable_menu_item_ids)) {
            return collect();
        }

        return MenuItem::whereIn('id', $this->applicable_menu_item_ids)
            ->where('is_available', true)
            ->get();
    }

    /**
     * Check if a menu item is applicable for this free item voucher
     */
    public function isMenuItemApplicable(int $menuItemId): bool
    {
        if ($this->discount_type !== 'free_item') {
            return false;
        }

        return in_array($menuItemId, $this->applicable_menu_item_ids ?? []);
    }
}