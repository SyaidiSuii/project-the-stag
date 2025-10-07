<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'voucher_id',
        'voucher_collection_id',  // Link to voucher template
        'voucher_code',           // Unique code untuk voucher ni
        'discount_type',          // percentage or fixed_amount
        'discount_value',         // nilai discount
        'minimum_order',          // minimum order amount
        'used_at',               // when voucher was used
        'used_order_id',         // which order used this voucher
        'expires_at',            // expiry date
        'status'                 // available, used, expired
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'minimum_order' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function voucherCollection()
    {
        return $this->belongsTo(VoucherCollection::class);
    }

    public function usedOrder()
    {
        return $this->belongsTo(Order::class, 'used_order_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where(function($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expires_at', '<', now());
    }

    // Generate unique voucher code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($userVoucher) {
            if (empty($userVoucher->voucher_code)) {
                $userVoucher->voucher_code = self::generateUniqueCode();
            }
            if (empty($userVoucher->status)) {
                $userVoucher->status = 'available';
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

    // Check if voucher is valid untuk digunakan
    public function isValid($orderAmount = 0)
    {
        // Check status
        if ($this->status !== 'available') {
            return false;
        }

        // Check expiry
        if ($this->expires_at && $this->expires_at < now()) {
            $this->update(['status' => 'expired']);
            return false;
        }

        // Check minimum order amount
        if ($this->minimum_order && $orderAmount < $this->minimum_order) {
            return false;
        }

        return true;
    }

    // Calculate discount amount
    public function calculateDiscount($orderAmount)
    {
        if (!$this->isValid($orderAmount)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($orderAmount * $this->discount_value) / 100;
        } else {
            return min($this->discount_value, $orderAmount);
        }
    }

    // Mark voucher as used
    public function markAsUsed($orderId)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'used_order_id' => $orderId
        ]);
    }
}