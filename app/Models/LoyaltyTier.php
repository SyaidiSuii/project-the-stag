<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyTier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'order', // PHASE 7: Tier hierarchy order
        'minimum_spending',
        'points_threshold', // PHASE 7: Points required for tier
        'points_multiplier', // PHASE 7: Earning multiplier
        'color',
        'icon',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'minimum_spending' => 'decimal:2',
        'points_threshold' => 'integer', // PHASE 7
        'points_multiplier' => 'decimal:2', // PHASE 7
        'order' => 'integer', // PHASE 7
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}