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
        'table_session_id',
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
        // QR Order specific fields
        'guest_name',
        'guest_phone',
        'session_token',
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

    public function etas()
    {
        return $this->hasMany(OrderEtas::class);
    }

    public function tableSession()
    {
        return $this->belongsTo(TableSession::class, 'table_session_id');
    }

    // Helper methods
    public function isQROrder()
    {
        return $this->order_type === 'qr_table' && !empty($this->table_session_id);
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
     */
    public function calculateTotalPreparationTime()
    {
        $totalPrepTime = 0;
        
        foreach ($this->items as $item) {
            if ($item->menuItem) {
                $prepTime = $item->menuItem->preparation_time ?? 15; // Default 15 minutes
                $totalPrepTime += ($prepTime * $item->quantity);
            }
        }
        
        // Add buffer time (10% or minimum 5 minutes)  
        $bufferTime = max(5, round($totalPrepTime * 0.1));
        
        return $totalPrepTime + $bufferTime;
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
        } while (self::where('confirmation_code', $code)->exists());

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
