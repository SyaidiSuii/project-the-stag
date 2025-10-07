<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'reward_type',
        'reward_value',
        'minimum_order',
        'points_required',
        'voucher_template_id',
        'expiry_days',
        'usage_limit',
        'max_redemptions',
        'redemption_method',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
        'expiry_days' => 'integer',
        'reward_value' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'usage_limit' => 'integer',
        'max_redemptions' => 'integer'
    ];

    public function voucherTemplate()
    {
        return $this->belongsTo(VoucherTemplate::class);
    }

    public function customerRewards()
    {
        return $this->hasMany(CustomerReward::class);
    }
}