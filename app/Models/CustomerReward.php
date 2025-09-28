<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_profile_id',
        'reward_id',
        'status',
        'expiry_date',
        'redeemed_at'
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'expiry_date' => 'date'
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}