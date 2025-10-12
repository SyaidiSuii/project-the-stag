<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use SoftDeletes;

    // Promotion Type Constants
    const TYPE_COMBO_DEAL = 'combo_deal';
    const TYPE_ITEM_DISCOUNT = 'item_discount';
    const TYPE_BUY_X_FREE_Y = 'buy_x_free_y';
    const TYPE_PROMO_CODE = 'promo_code';
    const TYPE_SEASONAL = 'seasonal';
    const TYPE_BUNDLE = 'bundle';

    protected $fillable = [
        'name',
        'promotion_type',
        'promo_config',
        'image_path',
        'badge_text',
        'banner_color',
        'promo_code',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'minimum_order_value',
        'start_date',
        'end_date',
        'applicable_days',
        'applicable_start_time',
        'applicable_end_time',
        'terms_conditions',
        'usage_limit_per_customer',
        'total_usage_limit',
        'current_usage_count',
        'display_order',
        'is_active',
        'is_featured'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'minimum_order_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'promo_config' => 'array',
        'applicable_days' => 'array',
        'current_usage_count' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'total_usage_limit' => 'integer',
        'display_order' => 'integer'
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get menu items associated with this promotion (for combos, discounts, etc.)
     */
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'promotion_items')
            ->withPivot([
                'quantity',
                'is_free',
                'is_required',
                'is_customizable',
                'custom_price',
                'item_options',
                'sort_order'
            ])
            ->withTimestamps()
            ->orderBy('promotion_items.sort_order');
    }

    /**
     * Get categories associated with this promotion (for category-wide discounts)
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_categories')
            ->withPivot(['discount_percentage', 'discount_amount'])
            ->withTimestamps();
    }

    /**
     * Get usage logs for this promotion
     */
    public function usageLogs(): HasMany
    {
        return $this->hasMany(PromotionUsageLog::class);
    }

    /**
     * Get user promotions (legacy support)
     */
    public function userPromotions(): HasMany
    {
        return $this->hasMany(UserPromotion::class);
    }

    // Discount types (legacy support)
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    // ==================== SCOPES ====================

    /**
     * Scope to get only active promotions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get valid promotions (active + within date range)
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function($query) {
                $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            });
    }

    /**
     * Scope to get featured promotions
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by promotion type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('promotion_type', $type);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc');
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Check if promotion is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check date range
        if ($this->start_date && $this->start_date > now()) {
            return false;
        }

        if ($this->end_date && $this->end_date < now()) {
            return false;
        }

        // Check day of week (if applicable)
        if ($this->applicable_days && !empty($this->applicable_days)) {
            $currentDay = strtolower(now()->format('l')); // monday, tuesday, etc.
            if (!in_array($currentDay, $this->applicable_days)) {
                return false;
            }
        }

        // Check time range (if applicable)
        if ($this->applicable_start_time && $this->applicable_end_time) {
            $currentTime = now()->format('H:i:s');
            if ($currentTime < $this->applicable_start_time || $currentTime > $this->applicable_end_time) {
                return false;
            }
        }

        // Check usage limits
        if ($this->total_usage_limit && $this->current_usage_count >= $this->total_usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this promotion
     */
    public function canBeUsedBy($userId = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // If no user ID and no guest usage, cannot use
        if (!$userId) {
            return true; // Allow guest usage for now
        }

        // Check per-user usage limit
        if ($this->usage_limit_per_customer) {
            $userUsageCount = $this->usageLogs()
                ->where('user_id', $userId)
                ->count();

            if ($userUsageCount >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get remaining uses for this promotion
     */
    public function getRemainingUses(): ?int
    {
        if (!$this->total_usage_limit) {
            return null; // Unlimited
        }

        return max(0, $this->total_usage_limit - $this->current_usage_count);
    }

    // ==================== DISCOUNT CALCULATION ====================

    /**
     * Calculate discount for given amount
     */
    public function calculateDiscount($amount): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            $discount = ($amount * $this->discount_value) / 100;
        } else {
            $discount = min($this->discount_value, $amount);
        }

        // Apply max discount cap if set
        if ($this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    /**
     * Log promotion usage
     */
    public function logUsage($orderId, $userId, $discountAmount, $subtotal, $total, $sessionId = null, $ipAddress = null)
    {
        // Increment usage count
        $this->increment('current_usage_count');

        // Create usage log
        return $this->usageLogs()->create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'discount_amount' => $discountAmount,
            'order_subtotal' => $subtotal,
            'order_total' => $total,
            'promo_code' => $this->promo_code,
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'used_at' => now()
        ]);
    }

    // ==================== ACCESSORS & HELPERS ====================

    /**
     * Get the full URL for the promotion image
     */
    public function getImageUrlAttribute(): ?string
    {
        // If image_path exists and file exists, return the URL
        if ($this->image_path) {
            // Check if path already contains 'storage/' or is full path
            if (strpos($this->image_path, 'storage/') === 0) {
                $filePath = public_path($this->image_path);
            } else {
                $filePath = public_path('storage/' . $this->image_path);
            }

            if (file_exists($filePath)) {
                // Return asset URL
                if (strpos($this->image_path, 'storage/') === 0) {
                    return asset($this->image_path);
                }
                return asset('storage/' . $this->image_path);
            }
        }

        // Return null to indicate no image (view will use default banner)
        return null;
    }

    /**
     * Check if promotion has uploaded image
     */
    public function hasImage(): bool
    {
        return $this->image_url !== null;
    }

    /**
     * Get formatted discount text
     */
    public function getDiscountTextAttribute(): string
    {
        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            return number_format($this->discount_value, 0) . '% OFF';
        } else {
            return 'RM ' . number_format($this->discount_value, 2) . ' OFF';
        }
    }

    /**
     * Get type label for display
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->promotion_type) {
            self::TYPE_COMBO_DEAL => 'Combo Deal',
            self::TYPE_ITEM_DISCOUNT => 'Item Discount',
            self::TYPE_BUY_X_FREE_Y => 'Buy X Free Y',
            self::TYPE_PROMO_CODE => 'Promo Code',
            self::TYPE_SEASONAL => 'Seasonal Offer',
            self::TYPE_BUNDLE => 'Bundle Deal',
            default => 'Promotion'
        };
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if (!$this->is_active) {
            return 'badge-secondary';
        }

        if (!$this->isValid()) {
            return 'badge-warning';
        }

        if ($this->is_featured) {
            return 'badge-success';
        }

        return 'badge-primary';
    }

    /**
     * Get days text for display
     */
    public function getDaysTextAttribute(): string
    {
        if (!$this->applicable_days || empty($this->applicable_days)) {
            return 'Every day';
        }

        if (count($this->applicable_days) === 7) {
            return 'Every day';
        }

        return implode(', ', array_map('ucfirst', $this->applicable_days));
    }

    /**
     * Get time range text
     */
    public function getTimeRangeTextAttribute(): ?string
    {
        if (!$this->applicable_start_time || !$this->applicable_end_time) {
            return null;
        }

        $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->applicable_start_time)->format('g:i A');
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->applicable_end_time)->format('g:i A');

        return "{$start} - {$end}";
    }

    /**
     * Check if promotion has usage limit
     */
    public function hasUsageLimit(): bool
    {
        return $this->total_usage_limit !== null && $this->total_usage_limit > 0;
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): ?int
    {
        if (!$this->hasUsageLimit()) {
            return null;
        }

        return round(($this->current_usage_count / $this->total_usage_limit) * 100);
    }

    /**
     * Get type accessor (alias for promotion_type)
     * This allows using $promotion->type in views for cleaner code
     */
    public function getTypeAttribute(): ?string
    {
        return $this->promotion_type;
    }
}