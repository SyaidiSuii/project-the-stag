<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusPointChallenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'condition',
        'bonus_points',
        'end_date',
        'status'
    ];

    protected $casts = [
        'bonus_points' => 'integer',
        'end_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        return $query->where(function($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
        });
    }
}