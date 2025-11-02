<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * DEPRECATED: VoucherCollection Model
 *
 * @deprecated since Phase 2.1 - Use VoucherTemplate with source_type='collection' instead
 *
 * This model is kept for backward compatibility only. All new voucher implementations
 * should use the unified VoucherTemplate model which now supports both reward-based
 * and collection-based vouchers through the 'source_type' field.
 *
 * Migration Path:
 * - Existing voucher_collections data will be migrated to voucher_templates
 * - Use VoucherTemplate::where('source_type', 'collection') for collection vouchers
 * - This model will be removed in a future version
 *
 * @see VoucherTemplate
 */
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