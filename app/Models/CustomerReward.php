<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerReward extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customerReward) {
            // Auto-generate redemption code if not provided
            if (empty($customerReward->redemption_code)) {
                $customerReward->redemption_code = 'RWD-' . strtoupper(Str::random(8));
            }
        });
    }

    protected $fillable = [
        'customer_profile_id',
        'reward_id',
        'points_spent',
        'redemption_code',
        'status',
        'claimed_at',
        'expires_at',
        'expiry_date',
        'redeemed_at'
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'expiry_date' => 'date',
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
}