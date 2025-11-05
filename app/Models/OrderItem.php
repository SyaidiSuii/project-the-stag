<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'promotion_id',
        'customer_reward_id', // Links free items to their reward redemption
        'is_combo_item',
        'combo_group_id',
        'quantity',
        'unit_price',
        'original_price',
        'discount_amount',
        'total_price',
        'special_note',
        'promotion_snapshot',
        'item_status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_combo_item' => 'boolean',
        'promotion_snapshot' => 'array',
    ];

    // 🔗 Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function customizations()
    {
        return $this->hasMany(MenuCustomization::class);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if item has promotion discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute(): string
    {
        if (!$this->hasDiscount()) {
            return '-';
        }

        return 'RM ' . number_format($this->discount_amount, 2);
    }

    /**
     * Get savings amount
     */
    public function getSavingsAttribute(): float
    {
        return $this->discount_amount * $this->quantity;
    }

    /**
     * Get items in same combo group
     */
    public function comboGroupItems()
    {
        if (!$this->combo_group_id) {
            return collect([]);
        }

        return static::where('order_id', $this->order_id)
            ->where('combo_group_id', $this->combo_group_id)
            ->get();
    }

    /**
     * Check if part of combo
     */
    public function isPartOfCombo(): bool
    {
        return $this->is_combo_item && !empty($this->combo_group_id);
    }
}
