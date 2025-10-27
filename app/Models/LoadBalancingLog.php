<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadBalancingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'station_id',
        'action_type',
        'old_load',
        'new_load',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'old_load' => 'integer',
        'new_load' => 'integer',
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
     * Scope: Overload alerts
     */
    public function scopeOverloadAlerts($query)
    {
        return $query->where('action_type', 'overload_alert');
    }

    /**
     * Scope: Today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
