<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenLoad extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'order_id',
        'load_points',
        'estimated_completion_time',
        'actual_completion_time',
        'status',
    ];

    protected $casts = [
        'load_points' => 'decimal:2',
        'estimated_completion_time' => 'datetime',
        'actual_completion_time' => 'datetime',
    ];

    /**
     * Get the station for this load
     */
    public function station()
    {
        return $this->belongsTo(KitchenStation::class, 'station_id');
    }

    /**
     * Get the order for this load
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Scope: Active loads (pending or in progress)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Scope: Completed loads
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the duration in minutes
     */
    public function getDurationAttribute()
    {
        if (!$this->actual_completion_time) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->actual_completion_time);
    }
}
