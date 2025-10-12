<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'user_id',
        'order_id',
        'discount_amount',
        'order_subtotal',
        'order_total',
        'promo_code',
        'session_id',
        'ip_address',
        'used_at'
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'order_subtotal' => 'decimal:2',
        'order_total' => 'decimal:2',
        'used_at' => 'datetime'
    ];

    /**
     * Get the promotion that was used
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Get the user who used the promotion
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order this promotion was used in
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for a specific promotion
     */
    public function scopeForPromotion($query, $promotionId)
    {
        return $query->where('promotion_id', $promotionId);
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('used_at', [$startDate, $endDate]);
    }
}
