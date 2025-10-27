<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'voucher_template_id',
        'source',
        'voucher_code',
        'status',
        'expiry_date',
        'redeemed_at',
        'order_id',
        'used_at'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'redeemed_at' => 'datetime',
        'used_at' => 'datetime'
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function voucherTemplate()
    {
        return $this->belongsTo(VoucherTemplate::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($query) {
                        $query->whereNull('expiry_date')
                              ->orWhere('expiry_date', '>=', now());
                    });
    }

    public function scopeRedeemed($query)
    {
        return $query->where('status', 'redeemed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expiry_date', '<', now());
    }

    // Generate unique voucher code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voucher) {
            if (empty($voucher->voucher_code)) {
                $voucher->voucher_code = self::generateUniqueCode();
            }
            if (empty($voucher->status)) {
                $voucher->status = 'active';
            }
        });
    }

    private static function generateUniqueCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
        } while (self::where('voucher_code', $code)->exists());

        return $code;
    }

    // Check if voucher is valid
    public function isValid($orderAmount = 0)
    {
        // Check status
        if ($this->status !== 'active') {
            return false;
        }

        // Check expiry
        if ($this->expiry_date && $this->expiry_date < now()) {
            $this->update(['status' => 'expired']);
            return false;
        }

        // Check minimum order amount if voucher template has minimum requirement
        if ($this->voucherTemplate && $this->voucherTemplate->minimum_order && $orderAmount < $this->voucherTemplate->minimum_order) {
            return false;
        }

        return true;
    }

    // Calculate discount amount
    public function calculateDiscount($orderAmount)
    {
        if (!$this->isValid($orderAmount) || !$this->voucherTemplate) {
            return 0;
        }

        if ($this->voucherTemplate->discount_type === 'percentage') {
            return ($orderAmount * $this->voucherTemplate->discount_value) / 100;
        } else {
            return min($this->voucherTemplate->discount_value, $orderAmount);
        }
    }

    // Mark voucher as redeemed
    public function markAsRedeemed()
    {
        $this->update([
            'status' => 'redeemed',
            'redeemed_at' => now()
        ]);
    }
}