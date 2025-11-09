<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderActivityLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // Only created_at, no updated_at

    protected $fillable = [
        'order_id',
        'activity_type',
        'title',
        'message',
        'metadata',
        'triggered_by_user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Activity type constants
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_CRITICAL = 'critical';

    /**
     * Get the order that owns the activity log.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who triggered this activity.
     */
    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    /**
     * Scope to filter by activity type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope to get critical and error activities.
     */
    public function scopeProblems($query)
    {
        return $query->whereIn('activity_type', [self::TYPE_ERROR, self::TYPE_CRITICAL]);
    }

    /**
     * Helper method to create activity log.
     */
    public static function logActivity(
        int $orderId,
        string $type,
        string $title,
        string $message,
        ?array $metadata = null,
        ?int $triggeredByUserId = null
    ) {
        return self::create([
            'order_id' => $orderId,
            'activity_type' => $type,
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
            'triggered_by_user_id' => $triggeredByUserId,
        ]);
    }
}
