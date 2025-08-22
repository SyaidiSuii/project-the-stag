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
        'reservation_id',
        'user_id',
        'payment_method',
        'amount',
        'currency',
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

    // 🔗 Relationship ke Table Reservation
    public function reservation()
    {
        return $this->belongsTo(TableReservation::class);
    }

    // 🔗 Relationship ke Users (staff/customer)
    public function user()
    {
        return $this->belongsTo(User::class);
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
