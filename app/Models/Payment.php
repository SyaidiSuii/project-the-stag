<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'order_id',
        'gateway',
        'payment_method',
        'currency',
        'amount',
        'bill_code',
        'transaction_id',
        'payment_status',
        'payment_gateway_response',
        'paid_at',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'payment_gateway_response' => 'array', // auto decode JSON
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // 🔗 Relationship ke Orders
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🎯 Helper Method
    public function isSuccessful(): bool
    {
        return $this->payment_status === 'success';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }
}
