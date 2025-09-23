<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'qr_code',
        'nfc_tag_id',
        'location_description',
        'coordinates',
        'table_type',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'amenities'   => 'array',
        'is_active'   => 'boolean',
    ];

    // Relationships
    public function reservations()
    {
        return $this->hasMany(TableReservation::class);
    }

    public function sessions()
    {
        return $this->hasMany(TableQrcode::class);
    }

    public function currentSession()
    {
        return $this->hasOne(TableQrcode::class)
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->latest();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Helper methods
    public function hasActiveSession()
    {
        return $this->currentSession()->exists();
    }

    public function createSession($data = [])
    {
        // Complete any existing active sessions first
        $this->sessions()->active()->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Create new session
        return $this->sessions()->create(array_merge([
            'started_at' => now(),
            'expires_at' => now()->addHours(4),
            'status' => 'active',
        ], $data));
    }

    public function getCurrentSessionQR()
    {
        $session = $this->currentSession;
        return $session ? $session->qr_code_url : null;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where('is_active', true);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeWithActiveSessions($query)
    {
        return $query->whereHas('sessions', function($q) {
            $q->where('status', 'active')
              ->where('expires_at', '>', now());
        });
    }
}
