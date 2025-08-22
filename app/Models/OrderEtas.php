<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderEtas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'initial_estimate',
        'current_estimate',
        'actual_completion_time',
        'delay_reason',
        'is_delayed',
        'delay_duration',
        'customer_notified',
        'last_updated',
    ];

    protected $casts = [
        'is_delayed' => 'boolean',
        'customer_notified' => 'boolean',
        'last_updated' => 'datetime',
    ];

    // 🔗 Relationship
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
