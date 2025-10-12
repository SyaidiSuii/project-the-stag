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
        'minimum_spending',
        'color',
        'icon',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'minimum_spending' => 'decimal:2',
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