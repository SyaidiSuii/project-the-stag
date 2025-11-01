<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tracks free product entitlements from reward redemptions
 *
 * When a customer redeems a "product" type reward, they receive a free product credit
 * that can be used to add the item to cart with RM0.00 price.
 */
class CustomerFreeProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_profile_id',
        'reward_id',
        'customer_reward_id',
        'menu_item_id',
        'status',
        'expires_at',
        'used_at',
        'order_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Customer profile relationship
     */
    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    /**
     * Reward relationship
     */
    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Customer reward redemption relationship
     */
    public function customerReward()
    {
        return $this->belongsTo(CustomerReward::class);
    }

    /**
     * Menu item relationship
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Order relationship (when used)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if free product is still available for use
     */
    public function isAvailable()
    {
        if ($this->status !== 'available') {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        return true;
    }

    /**
     * Mark as used
     */
    public function markAsUsed($orderId = null)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'order_id' => $orderId,
        ]);
    }

    /**
     * Scope: Get available free products for a customer
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where(function($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Scope: Get free products for specific menu item
     */
    public function scopeForMenuItem($query, $menuItemId)
    {
        return $query->where('menu_item_id', $menuItemId);
    }
}
