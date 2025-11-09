<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitchenStation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'station_type',
        'station_type_id',
        'max_capacity',
        'current_load',
        'is_active',
        'operating_hours',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'operating_hours' => 'array',
        'max_capacity' => 'integer',
        'current_load' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the station type
     */
    public function stationType()
    {
        return $this->belongsTo(StationType::class, 'station_type_id');
    }

    /**
     * Get kitchen loads for this station
     */
    public function kitchenLoads()
    {
        return $this->hasMany(KitchenLoad::class, 'station_id');
    }

    /**
     * Get active/pending kitchen loads (TODAY ONLY)
     */
    public function activeLoads()
    {
        return $this->kitchenLoads()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('created_at', today());
    }

    /**
     * Get station assignments
     */
    public function stationAssignments()
    {
        return $this->hasMany(StationAssignment::class, 'station_id');
    }

    /**
     * Alias for consistency
     */
    public function assignments()
    {
        return $this->stationAssignments();
    }

    /**
     * Get pending assignments (TODAY ONLY)
     */
    public function pendingAssignments()
    {
        return $this->stationAssignments()
            ->whereIn('status', ['assigned', 'started'])
            ->whereHas('order', function ($q) {
                $q->whereDate('order_time', today());
            });
    }

    /**
     * Get load balancing logs for this station
     */
    public function logs()
    {
        return $this->hasMany(LoadBalancingLog::class, 'station_id');
    }

    /**
     * Get load percentage (TODAY ONLY - calculated from today's active loads)
     */
    public function getLoadPercentageAttribute()
    {
        if ($this->max_capacity == 0) {
            return 0;
        }
        
        // Calculate today's load from active kitchen loads instead of current_load field
        $todayLoad = $this->kitchenLoads()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('created_at', today())
            ->sum('load_points');
        
        return round(($todayLoad / $this->max_capacity) * 100, 2);
    }

    /**
     * Get today's current load (calculated from active loads)
     */
    public function getTodayLoadAttribute()
    {
        return $this->kitchenLoads()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('created_at', today())
            ->sum('load_points');
    }

    /**
     * Check if station is overloaded (>= 85%) based on today's load
     */
    public function isOverloaded()
    {
        return $this->load_percentage >= 85;
    }

    /**
     * Check if station is approaching capacity (>= 70%) based on today's load
     */
    public function isApproachingCapacity()
    {
        return $this->load_percentage >= 70 && $this->load_percentage < 85;
    }

    /**
     * Get average completion time for this station (in minutes)
     */
    public function getAverageCompletionTime()
    {
        $completedLoads = $this->kitchenLoads()
            ->where('status', 'completed')
            ->whereNotNull('actual_completion_time')
            ->whereDate('created_at', today())
            ->get();

        if ($completedLoads->isEmpty()) {
            return 15; // Default 15 minutes
        }

        $totalMinutes = $completedLoads->sum(function ($load) {
            return $load->created_at->diffInMinutes($load->actual_completion_time);
        });

        return round($totalMinutes / $completedLoads->count());
    }

    /**
     * Scope: Only active stations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
