<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'date_of_birth',
        'address',
        'loyalty_points',
        'photo',
        'preferred_contact',
        'dietary_preferences',
        'last_visit',
        'total_spent',
        'visit_count',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'dietary_preferences' => 'array',
        'last_visit' => 'datetime',
        'total_spent' => 'decimal:2',
        'loyalty_points' => 'integer',
        'visit_count' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerRewards()
    {
        return $this->hasMany(CustomerReward::class);
    }

    public function loyaltyTier()
    {
        return $this->belongsTo(LoyaltyTier::class);
    }

    /**
     * Update the customer's loyalty tier based on their total spending
     */
    public function updateLoyaltyTier()
    {
        // Calculate total spending from paid orders
        $totalSpending = \App\Models\Order::where('user_id', $this->user_id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Find the appropriate tier
        $tier = \App\Models\LoyaltyTier::active()
            ->where('minimum_spending', '<=', $totalSpending)
            ->orderBy('minimum_spending', 'desc')
            ->first();

        if ($tier && $this->loyalty_tier_id !== $tier->id) {
            $this->update(['loyalty_tier_id' => $tier->id]);
        }

        return $tier;
    }
}
