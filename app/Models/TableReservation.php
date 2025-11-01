<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TableReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'table_id',
        'booking_date',
        'booking_time',
        'guest_name',
        'guest_email',
        'guest_phone',
        'party_size',
        'special_requests',
        'status',
        'confirmation_code',
        'confirmed_at',
        'seated_at',
        'completed_at',
        'notes',
        'reminder_sent'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'seated_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate a unique confirmation code when creating a new reservation.
        static::creating(function ($reservation) {
            if (empty($reservation->confirmation_code)) {
                do {
                    // Format: BK-YYYYMMDD-XXXX
                    $prefix = 'BK';
                    $date = now()->format('Ymd'); // 20250920
                    $random = strtoupper(Str::random(4)); // XYZ1
                    $code = $prefix . '-' . $date . '-' . $random;
                } while (static::withTrashed()->where('confirmation_code', $code)->exists()); // Check including soft-deleted records
                $reservation->confirmation_code = $code;
            }
        });

        // Automatically set timestamps when the status changes.
        static::saving(function ($reservation) {
            if ($reservation->isDirty('status')) {
                $now = now();
                switch ($reservation->status) {
                    case 'confirmed':
                        if (!$reservation->confirmed_at) $reservation->confirmed_at = $now;
                        break;
                    case 'seated':
                        if (!$reservation->confirmed_at) $reservation->confirmed_at = $now;
                        if (!$reservation->seated_at) $reservation->seated_at = $now;
                        break;
                    case 'completed':
                        if (!$reservation->confirmed_at) $reservation->confirmed_at = $now;
                        if (!$reservation->seated_at) $reservation->seated_at = $now;
                        if (!$reservation->completed_at) $reservation->completed_at = $now;
                        break;
                    case 'cancelled':
                        // Cancelled reservations don't need additional timestamps
                        break;
                }
            }
        });
        
        // Handle session creation/completion on status changes
        static::saved(function ($reservation) {
            if ($reservation->wasChanged('status') && $reservation->table_id) {
                $table = Table::find($reservation->table_id);
                if (!$table) return; // Safety check
                
                switch ($reservation->status) {
                    case 'confirmed':
                        // Update table status to occupied when admin confirms booking
                        $table->update(['status' => 'occupied']);
                        break;
                        
                    case 'seated':
                        // Create table session when customer is seated
                        if (!$table->hasActiveSession()) {
                            $table->createSession([
                                'reservation_id' => $reservation->id,
                                'guest_name' => $reservation->guest_name,
                                'guest_phone' => $reservation->guest_phone,
                                'guest_count' => $reservation->party_size,
                                'notes' => 'Auto-created from reservation: ' . $reservation->confirmation_code,
                            ]);
                            
                            // Ensure table status is occupied
                            $table->update(['status' => 'occupied']);
                        }
                        break;
                        
                    case 'completed':
                    case 'cancelled':
                        // Complete table session when reservation is completed or cancelled
                        $activeSession = $table->currentSession;
                        if ($activeSession && $activeSession->reservation_id == $reservation->id) {
                            $activeSession->complete();
                        }
                        
                        // Check if there are any other active reservations for this table
                        $hasOtherReservations = static::where('table_id', $table->id)
                            ->whereIn('status', ['confirmed', 'pending'])
                            ->where('id', '!=', $reservation->id)
                            ->where('booking_date', '>=', now()->toDateString())
                            ->exists();
                        
                        // Set table back to available if no other active sessions and no other reservations
                        if (!$table->hasActiveSession() && !$hasOtherReservations) {
                            $table->update(['status' => 'available']);
                        }
                        break;
                }
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    
    public function tableQrcode()
    {
        return $this->hasOne(TableQrcode::class, 'reservation_id');
    }
    
    // Helper methods
    public function hasActiveSession()
    {
        return $this->tableQrcode && $this->tableQrcode->isActive();
    }
    
    public function getQRCodeUrl()
    {
        if ($this->hasActiveSession()) {
            return $this->tableQrcode->qr_code_url;
        }
        return null;
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        // Can only cancel confirmed or pending bookings
        if (!in_array($this->status, ['confirmed', 'pending'])) {
            return false;
        }

        // Can't cancel past bookings
        $bookingDateTime = $this->booking_date->setTimeFromTimeString($this->booking_time);
        return $bookingDateTime->isFuture() || $bookingDateTime->isToday();
    }

    /**
     * Get status color for display
     */
    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'seated' => 'info',
            'completed' => 'primary',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get formatted status text
     */
    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Pending Confirmation',
            'confirmed' => 'Confirmed',
            'seated' => 'Currently Seated',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }
}
