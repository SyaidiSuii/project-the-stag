<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'special_notes',
        'last_checked_at',
        'unavailable_since',
        'applied_promo_code',
        'promo_discount_amount',
        'promotion_id',
        'promotion_group_id',
        'is_free_item',
        'customer_reward_id', // Links free items to their reward redemption
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'last_checked_at' => 'datetime',
        'unavailable_since' => 'datetime',
        'promo_discount_amount' => 'decimal:2',
        'is_free_item' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menuItem()
    {
        // Include soft deleted menu items for "Shopee-style" display
        return $this->belongsTo(MenuItem::class)->withTrashed();
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    // Helper methods
    public function getTotalPriceAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    public static function getCartTotal($userId)
    {
        return static::where('user_id', $userId)->get()->sum('total_price');
    }

    public static function getCartCount($userId)
    {
        return static::where('user_id', $userId)->sum('quantity');
    }

    /**
     * Check if menu item is available (exists and not soft deleted, and availability=true)
     */
    public function isMenuItemAvailable()
    {
        if (!$this->menuItem) {
            return false;
        }

        // Check if soft deleted
        if ($this->menuItem->trashed()) {
            return false;
        }

        // Check availability flag
        return $this->menuItem->availability === true;
    }

    /**
     * Get availability status with reason
     * Returns: ['available' => bool, 'reason' => string|null]
     */
    public function getAvailabilityStatus()
    {
        // Load relationship if not loaded
        if (!$this->relationLoaded('menuItem')) {
            $this->load('menuItem');
        }

        if (!$this->menuItem) {
            return [
                'available' => false,
                'reason' => 'deleted',
                'message' => 'Produk telah dikeluarkan oleh penjual'
            ];
        }

        if ($this->menuItem->trashed()) {
            return [
                'available' => false,
                'reason' => 'deleted',
                'message' => 'Produk telah dikeluarkan oleh penjual'
            ];
        }

        if (!$this->menuItem->availability) {
            return [
                'available' => false,
                'reason' => 'unavailable',
                'message' => 'Produk tidak tersedia buat masa ini'
            ];
        }

        return [
            'available' => true,
            'reason' => null,
            'message' => null
        ];
    }

    /**
     * Mark item as unavailable and record timestamp
     */
    public function markAsUnavailable()
    {
        if (!$this->unavailable_since) {
            $this->unavailable_since = now();
            $this->save();
        }
    }

    /**
     * Mark item as available again (clear unavailable timestamp)
     */
    public function markAsAvailable()
    {
        if ($this->unavailable_since) {
            $this->unavailable_since = null;
            $this->save();
        }
    }

    /**
     * Update last checked timestamp
     */
    public function updateLastChecked()
    {
        $this->last_checked_at = now();
        $this->save();
    }

    /**
     * Get cart total for AVAILABLE items only (excluding unavailable)
     */
    public static function getAvailableCartTotal($userId)
    {
        return static::where('user_id', $userId)
            ->with('menuItem')
            ->get()
            ->filter(function ($cartItem) {
                return $cartItem->isMenuItemAvailable();
            })
            ->sum('total_price');
    }

    /**
     * Get count of unavailable items in cart
     */
    public static function getUnavailableCount($userId)
    {
        return static::where('user_id', $userId)
            ->with('menuItem')
            ->get()
            ->filter(function ($cartItem) {
                return !$cartItem->isMenuItemAvailable();
            })
            ->count();
    }

    /**
     * Scope: Get items that have been unavailable for more than X days
     */
    public function scopeUnavailableForDays($query, $days = 7)
    {
        return $query->whereNotNull('unavailable_since')
            ->where('unavailable_since', '<=', now()->subDays($days));
    }
}
