<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'minimum_spend',
        'max_discount',
        'terms_conditions',
        'expiry_days'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'expiry_days' => 'integer'
    ];

    public function rewards()
    {
        return $this->hasMany(Reward::class, 'voucher_template_id');
    }
}