<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'table_id',
        'reservation_id',
        'order_type',
        'order_source',
        'order_status',
        'order_time',
        'table_number',
        'total_amount',
        'payment_status',
        'special_instructions',
        'estimated_completion_time',
        'actual_completion_time',
        'is_rush_order',
        'confirmation_code',
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'estimated_completion_time' => 'datetime',
        'actual_completion_time' => 'datetime',
        'special_instructions' => 'array',
        'is_rush_order' => 'boolean',
    ];

    // 🔗 Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(TableReservation::class, 'reservation_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order eta associated with the order.
     */
    public function eta()
    {
        return $this->hasOne(OrderEtas::class);
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class);
    }
}
