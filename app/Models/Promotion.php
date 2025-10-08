<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'promo_code',
        'discount_type',
        'discount_value',
        'minimum_order_value',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_order_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function userPromotions(): HasMany
    {
        return $this->hasMany(UserPromotion::class);
    }

    // Promotion types
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    // Promotion triggers
    public const TRIGGER_LOW_STOCK = 'low_stock';
    public const TRIGGER_HAPPY_HOUR = 'happy_hour';
    public const TRIGGER_HIGH_WASTE = 'high_waste';
    public const TRIGGER_CHEF_SPECIAL = 'chef_special';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where(function($query) {
            $query->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
        });
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at < now()) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            return ($amount * $this->discount_value) / 100;
        } else {
            return min($this->discount_value, $amount);
        }
    }
}