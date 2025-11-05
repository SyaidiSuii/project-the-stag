<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\RecommendationService;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'table_id',
        'table_qrcode_id',
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
        'payment_method',
        // QR Order specific fields
        'guest_name',
        'guest_email',
        'guest_phone',
        'session_token',
        // Unpaid order tracking
        'unpaid_alert_sent_at',
        'is_flagged_unpaid',
        // Voucher discount tracking
        'customer_voucher_id',
        'voucher_discount',
        'voucher_code',
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'estimated_completion_time' => 'datetime',
        'actual_completion_time' => 'datetime',
        'unpaid_alert_sent_at' => 'datetime',
        'special_instructions' => 'array',
        'is_rush_order' => 'boolean',
        'is_flagged_unpaid' => 'boolean',
    ];

    /**
     * Boot the model and add event listeners for AI model retraining
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->confirmation_code)) {
                $order->confirmation_code = self::generateConfirmationCode();
            }
        });

        // Trigger AI model retrain when order is completed/served
        static::updated(function ($order) {
            // Decrement kitchen station loads when order is completed or cancelled
            if (
                $order->isDirty('order_status') &&
                in_array($order->order_status, ['completed', 'cancelled', 'served'])
            ) {
                // Get the old status before it was updated
                $oldStatus = $order->getOriginal('order_status');

                // Only decrement if the old status was pending, preparing, or ready
                if (in_array($oldStatus, ['pending', 'confirmed', 'preparing', 'ready'])) {
                    $stationAssignments = $order->stationAssignments()->with(['station', 'orderItem'])->get();

                    foreach ($stationAssignments as $assignment) {
                        if ($assignment->station && in_array($assignment->status, ['assigned', 'started'])) {
                            // Calculate item quantity for this assignment
                            $itemQuantity = $assignment->orderItem ? $assignment->orderItem->quantity : 1;

                            // Decrement by actual item quantity
                            $newLoad = max(0, $assignment->station->current_load - $itemQuantity);
                            $assignment->station->update(['current_load' => $newLoad]);

                            // Update assignment status to completed
                            $assignment->update(['status' => 'completed']);
                        }
                    }
                }

                // Trigger AI retrain
                if (in_array($order->order_status, ['completed', 'served'])) {
                    try {
                        app(RecommendationService::class)->onOrderCompleted($order->id);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to trigger AI retrain on order completion', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });

        // Trigger retrain when order is deleted (affects training data)
        static::deleting(function ($order) {
            // Decrement kitchen station loads for this order
            $stationAssignments = $order->stationAssignments()->with(['station', 'orderItem'])->get();

            foreach ($stationAssignments as $assignment) {
                if ($assignment->station && in_array($assignment->status, ['assigned', 'started'])) {
                    // Calculate item quantity for this assignment
                    $itemQuantity = $assignment->orderItem ? $assignment->orderItem->quantity : 1;

                    // Decrement by actual item quantity
                    $newLoad = max(0, $assignment->station->current_load - $itemQuantity);
                    $assignment->station->update(['current_load' => $newLoad]);
                }
            }
        });

        static::deleted(function ($order) {
            try {
                app(RecommendationService::class)->onOrderCompleted($order->id);
            } catch (\Exception $e) {
                \Log::warning('Failed to trigger AI retrain on order deletion', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

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

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class);
    }

    public function etas()
    {
        return $this->hasMany(OrderEtas::class);
    }

    public function tableQrcode()
    {
        return $this->belongsTo(TableQrcode::class, 'table_qrcode_id');
    }

    public function reviews()
    {
        return $this->hasMany(MenuItemReview::class);
    }

    public function customerVoucher()
    {
        return $this->belongsTo(CustomerVoucher::class, 'customer_voucher_id');
    }

    /**
     * Kitchen Load Balancing relationships
     */
    public function kitchenLoads()
    {
        return $this->hasMany(KitchenLoad::class);
    }

    public function stationAssignments()
    {
        return $this->hasMany(StationAssignment::class);
    }

    public function loadBalancingLogs()
    {
        return $this->hasMany(LoadBalancingLog::class);
    }

    /**
     * Get promotion usage logs for this order
     */
    public function promotionUsageLogs()
    {
        return $this->hasMany(PromotionUsageLog::class);
    }

    // Helper methods
    public function isQROrder()
    {
        return $this->order_type === 'qr_table' && !empty($this->table_qrcode_id);
    }

    public function isWebsiteOrder()
    {
        return $this->order_type === 'website' && !empty($this->user_id);
    }

    public function getCustomerNameAttribute()
    {
        if ($this->isQROrder()) {
            return $this->guest_name;
        }

        return $this->user ? $this->user->name : 'Unknown';
    }

    public function getCustomerPhoneAttribute()
    {
        if ($this->isQROrder()) {
            return $this->guest_phone;
        }

        return $this->user ? $this->user->phone : null;
    }

    /**
     * Calculate total preparation time for all items in minutes
     * Uses PARALLEL station-based calculation (Critical Path Method)
     *
     * Logic: Items are prepared simultaneously by different stations,
     * so the total time = the longest station's time (bottleneck)
     */
    public function calculateTotalPreparationTime()
    {
        // Group items by their assigned kitchen station
        $stationTimes = [];

        // Check if we have station assignments (distributed orders)
        if ($this->stationAssignments && $this->stationAssignments->count() > 0) {
            // Use station assignments for accurate calculation
            foreach ($this->stationAssignments as $assignment) {
                $stationId = $assignment->station_id;
                $orderItem = $assignment->orderItem;

                if ($orderItem && $orderItem->menuItem) {
                    $prepTime = $orderItem->menuItem->preparation_time ?? 15; // Default 15 minutes
                    $itemTotalTime = $prepTime * $orderItem->quantity;

                    // Track the maximum time for each item at this station
                    if (!isset($stationTimes[$stationId])) {
                        $stationTimes[$stationId] = 0;
                    }

                    // For parallel cooking at same station, use the longest item time
                    // (multiple chefs can work on different items simultaneously)
                    $stationTimes[$stationId] = max($stationTimes[$stationId], $itemTotalTime);
                }
            }
        } else {
            // Fallback: If no station assignments yet (order just created),
            // estimate by grouping items by their effective station
            foreach ($this->items as $item) {
                if ($item->menuItem) {
                    $station = $item->menuItem->getEffectiveStation();
                    $stationId = $station ? $station->id : 'default';

                    $prepTime = $item->menuItem->preparation_time ?? 15;
                    $itemTotalTime = $prepTime * $item->quantity;

                    if (!isset($stationTimes[$stationId])) {
                        $stationTimes[$stationId] = 0;
                    }

                    // Use the longest item time per station
                    $stationTimes[$stationId] = max($stationTimes[$stationId], $itemTotalTime);
                }
            }
        }

        // The bottleneck station (longest time) determines the overall ETA
        // All other stations will finish earlier and wait
        $bottleneckTime = count($stationTimes) > 0 ? max($stationTimes) : 15; // Default 15 minutes if no items

        // Add buffer time (10% or minimum 5 minutes)
        $bufferTime = max(5, round($bottleneckTime * 0.1));

        return $bottleneckTime + $bufferTime;
    }

    /**
     * Auto create ETA based on order items preparation time
     */
    public function autoCreateETA()
    {
        $totalPrepTime = $this->calculateTotalPreparationTime();

        // Calculate estimated completion time
        $orderTime = $this->order_time ?? now();
        $estimatedTime = $orderTime->addMinutes($totalPrepTime);

        // Create ETA record with correct fields
        $eta = $this->etas()->create([
            'initial_estimate' => $totalPrepTime,
            'current_estimate' => $totalPrepTime,
            'is_delayed' => false,
            'delay_duration' => 0,
            'customer_notified' => false,
            'last_updated' => now(),
        ]);

        // Also update the order's estimated_completion_time field
        $this->update([
            'estimated_completion_time' => $estimatedTime
        ]);

        return $eta;
    }

    /**
     * Recalculate and update existing ETA when order items change
     */
    public function updateAutoETA()
    {
        // Find the auto-generated ETA (first one created)
        $autoEta = $this->etas()->first();

        if ($autoEta) {
            $totalPrepTime = $this->calculateTotalPreparationTime();
            $orderTime = $this->order_time ?? now();
            $estimatedTime = $orderTime->addMinutes($totalPrepTime);

            // Check if there's a significant change that warrants marking as delayed
            $isDelayed = $totalPrepTime > $autoEta->initial_estimate;
            $delayDuration = $isDelayed ? ($totalPrepTime - $autoEta->initial_estimate) : 0;

            $autoEta->update([
                'current_estimate' => $totalPrepTime,
                'is_delayed' => $isDelayed,
                'delay_duration' => $delayDuration,
                'last_updated' => now(),
            ]);

            // Update order's estimated_completion_time
            $this->update([
                'estimated_completion_time' => $estimatedTime
            ]);

            return $autoEta;
        }

        // If no auto ETA exists, create one
        return $this->autoCreateETA();
    }

    /**
     * Generate confirmation code with format STAG-20250917-7G3K
     * Prefix restoran (STAG) + Tarikh ringkas (20250917) + Random 3-4 char (7G3K)
     */
    public static function generateConfirmationCode()
    {
        do {
            $prefix = 'STAG';
            $date = now()->format('Ymd'); // 20250917
            $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4)); // 4 chars

            $code = "{$prefix}-{$date}-{$random}";
        } while (self::withTrashed()->where('confirmation_code', $code)->exists()); // Check including soft-deleted records

        return $code;
    }

    /**
     * Generate confirmation code when payment is completed
     */
    public function generateAndSaveConfirmationCode()
    {
        if (empty($this->confirmation_code)) {
            $this->confirmation_code = self::generateConfirmationCode();
            $this->save();
        }

        return $this->confirmation_code;
    }
}
