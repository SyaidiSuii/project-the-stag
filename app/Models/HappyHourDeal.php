<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class HappyHourDeal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'discount_percentage',
        'start_time',
        'end_time',
        'days_of_week',
        'is_active'
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'days_of_week' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get menu items associated with this happy hour deal
     */
    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'happy_hour_deal_items')
            ->withTimestamps();
    }

    /**
     * Scope to get only active deals
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the deal is currently active based on time and day
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i:s');

        // Check if today is included in days_of_week
        if (!in_array($currentDay, $this->days_of_week)) {
            return false;
        }

        // Check if current time is within the happy hour range
        return $currentTime >= $this->start_time && $currentTime <= $this->end_time;
    }

    /**
     * Get the time remaining until deal starts or ends
     */
    public function getTimeStatus(): array
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $currentDay = strtolower($now->format('l'));

        if (!in_array($currentDay, $this->days_of_week)) {
            return [
                'status' => 'not_today',
                'message' => 'Available on ' . implode(', ', array_map('ucfirst', $this->days_of_week))
            ];
        }

        if ($currentTime < $this->start_time) {
            $start = Carbon::createFromFormat('H:i:s', $this->start_time);
            $diff = $now->diffInMinutes($start);
            return [
                'status' => 'upcoming',
                'message' => "Starts in {$diff} minutes",
                'minutes_remaining' => $diff
            ];
        }

        if ($currentTime <= $this->end_time) {
            $end = Carbon::createFromFormat('H:i:s', $this->end_time);
            $diff = $now->diffInMinutes($end);
            return [
                'status' => 'active',
                'message' => "Ends in {$diff} minutes",
                'minutes_remaining' => $diff
            ];
        }

        return [
            'status' => 'ended',
            'message' => 'Deal has ended for today'
        ];
    }

    /**
     * Calculate discount amount for a given price
     */
    public function calculateDiscount(float $price): float
    {
        if (!$this->isCurrentlyActive()) {
            return 0;
        }

        return round(($price * $this->discount_percentage) / 100, 2);
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        $start = Carbon::createFromFormat('H:i:s', $this->start_time)->format('g:i A');
        $end = Carbon::createFromFormat('H:i:s', $this->end_time)->format('g:i A');
        return "{$start} - {$end}";
    }

    /**
     * Get formatted days
     */
    public function getFormattedDaysAttribute(): string
    {
        if (count($this->days_of_week) === 7) {
            return 'Every day';
        }

        return implode(', ', array_map('ucfirst', $this->days_of_week));
    }
}
