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
        'points_required',
        'voucher_template_id',
        'expiry_days',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
        'expiry_days' => 'integer'
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