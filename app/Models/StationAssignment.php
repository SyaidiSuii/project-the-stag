<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'station_id',
        'order_item_id',
        'assignment_priority',
        'assigned_at',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'assignment_priority' => 'integer',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the station
     */
    public function station()
    {
        return $this->belongsTo(KitchenStation::class, 'station_id');
    }

    /**
     * Get the order item
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * Scope: Pending/Active assignments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'started']);
    }

    /**
     * Scope: Completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
