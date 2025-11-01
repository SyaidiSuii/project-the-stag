<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\IdGeneratorService;

class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'date_of_birth',
        'address',
        // 'loyalty_points', // REMOVED: Use users.points_balance instead (Phase 1.1)
        'photo',
        'preferred_contact',
        'dietary_preferences',
        'last_visit',
        'total_spent',
        'visit_count',
        'customer_id',
        'loyalty_tier_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'dietary_preferences' => 'array',
        'last_visit' => 'datetime',
        'total_spent' => 'decimal:2',
        // 'loyalty_points' => 'integer', // REMOVED: Use users.points_balance instead (Phase 1.1)
        'visit_count' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['display_id'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customerProfile) {
            // Auto-generate customer_id if not provided
            if (empty($customerProfile->customer_id)) {
                $idGenerator = app(IdGeneratorService::class);
                $customerProfile->customer_id = $idGenerator->generateCustomerId();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerRewards()
    {
        return $this->hasMany(CustomerReward::class);
    }

    public function customerVouchers()
    {
        return $this->hasMany(CustomerVoucher::class);
    }

    public function loyaltyTier()
    {
        return $this->belongsTo(LoyaltyTier::class);
    }

    /**
     * Update the customer's total spending and loyalty tier based on paid orders
     */
    public function updateTotalSpendingAndTier()
    {
        // Calculate total spending from paid orders
        $totalSpending = \App\Models\Order::where('user_id', $this->user_id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Update total_spent
        $this->update(['total_spent' => $totalSpending]);

        // Find the appropriate tier
        $tier = \App\Models\LoyaltyTier::active()
            ->where('minimum_spending', '<=', $totalSpending)
            ->orderBy('minimum_spending', 'desc')
            ->first();

        // Update loyalty tier if changed
        if ($tier && $this->loyalty_tier_id !== $tier->id) {
            $this->update(['loyalty_tier_id' => $tier->id]);
        }

        return $tier;
    }

    /**
     * Update the customer's loyalty tier based on their total spending
     * @deprecated Use updateTotalSpendingAndTier() instead
     */
    public function updateLoyaltyTier()
    {
        return $this->updateTotalSpendingAndTier();
    }

    /**
     * Add spending to customer profile
     * @param float $amount
     */
    public function addSpending(float $amount)
    {
        $currentSpent = $this->total_spent ?? 0;
        $this->update(['total_spent' => $currentSpent + $amount]);

        // Also update loyalty tier if spending increased
        $this->updateTotalSpendingAndTier();
    }

    /**
     * Get display ID attribute
     *
     * @return string|null
     */
    public function getDisplayIdAttribute()
    {
        return $this->customer_id;
    }
}
