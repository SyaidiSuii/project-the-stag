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
}
