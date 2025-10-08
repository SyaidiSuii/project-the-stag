<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'spending_requirement',
        'voucher_type',
        'voucher_value',
        'valid_until',
        'status'
    ];

    protected $casts = [
        'spending_requirement' => 'decimal:2',
        'voucher_value' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        return $query->where(function($query) {
            $query->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now()->toDateString());
        });
    }
}