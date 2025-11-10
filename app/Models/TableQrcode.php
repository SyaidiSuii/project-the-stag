<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class TableQrcode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'table_id',
        'reservation_id', 
        'session_code',
        'qr_code_url',
        'qr_code_data',
        'qr_code_png',
        'qr_code_svg',
        'guest_name',
        'guest_phone',
        'guest_count',
        'started_at',
        'expires_at',
        'completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'qr_code_data' => 'array',
        'started_at' => 'datetime',
        'expires_at' => 'datetime', 
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate session code when creating new session
        static::creating(function ($session) {
            if (empty($session->session_code)) {
                // Load table if needed
                if (!$session->relationLoaded('table') && $session->table_id) {
                    $session->load('table');
                }
                $session->session_code = $session->generateSessionCode();
            }
            
            // Set default expiry time (4 hours from now)
            if (empty($session->expires_at)) {
                $session->expires_at = now()->addHours(4);
            }
            
            // Set started_at if not set
            if (empty($session->started_at)) {
                $session->started_at = now();
            }
        });

        // Generate QR code after session is created
        static::created(function ($session) {
            // Ensure table relationship is loaded
            if (!$session->relationLoaded('table')) {
                $session->load('table');
            }
            $session->generateQRCode();
        });
    }

    // Relationships
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(TableReservation::class, 'reservation_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_qrcode_id');
    }

    // Helper Methods
    public function generateSessionCode()
    {
        do {
            // Safely get table number
            $tableNumber = 'T' . $this->table_id; // Default fallback
            if ($this->table && is_object($this->table)) {
                $tableNumber = $this->table->table_number;
            } elseif ($this->table_id) {
                // Load table if we have table_id but no relationship
                $table = Table::find($this->table_id);
                if ($table) {
                    $tableNumber = $table->table_number;
                }
            }
            
            $code = 'SES_' . $tableNumber . '_' . now()->format('Ymd_His') . '_' . strtoupper(Str::random(3));
        } while (static::where('session_code', $code)->exists());

        return $code;
    }

    public function generateQRCode()
    {
        try {
            // Delete old QR files first
            $this->deleteOldQRFiles();

            // Generate menu URL
            $menuUrl = config('app.url') . '/qr/menu?session=' . $this->session_code;

            $qrData = [
                'url' => $menuUrl,
                'table_number' => $this->table->table_number ?? 'Unknown',
                'session_code' => $this->session_code,
                'expires_at' => $this->expires_at->toISOString(),
            ];

            // Generate PNG QR code
            $pngPath = $this->createQRImage('png', $menuUrl);

            // Generate SVG QR code
            $svgPath = $this->createQRImage('svg', $menuUrl);

            $this->update([
                'qr_code_url' => $menuUrl,
                'qr_code_data' => $qrData,
                'qr_code_png' => $pngPath,
                'qr_code_svg' => $svgPath,
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
        }
    }

    public function regenerateQRCode()
    {
        try {
            // Delete old QR files first
            $this->deleteOldQRFiles();

            // Generate new session code
            $newSessionCode = $this->generateSessionCode();

            // Extend expiration time - add 4 hours from now
            $newExpiresAt = now()->addHours(4);

            // Generate menu URL with new session code
            $menuUrl = config('app.url') . '/qr/menu?session=' . $newSessionCode;

            $qrData = [
                'url' => $menuUrl,
                'table_number' => $this->table->table_number ?? 'Unknown',
                'session_code' => $newSessionCode,
                'expires_at' => $newExpiresAt->toISOString(),
            ];

            // Generate PNG QR code
            $pngPath = $this->createQRImage('png', $menuUrl, $newSessionCode);

            // Generate SVG QR code
            $svgPath = $this->createQRImage('svg', $menuUrl, $newSessionCode);

            $this->update([
                'session_code' => $newSessionCode,
                'qr_code_url' => $menuUrl,
                'qr_code_data' => $qrData,
                'qr_code_png' => $pngPath,
                'qr_code_svg' => $svgPath,
                'expires_at' => $newExpiresAt,
                'status' => 'active', // Reset status to active
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('QR Code regeneration failed: ' . $e->getMessage());
            return false;
        }
    }

    private function deleteOldQRFiles()
    {
        $patterns = [
            "qr-codes/qr_{$this->session_code}.png",
            "qr-codes/qr_{$this->session_code}.svg"
        ];
        
        foreach($patterns as $pattern) {
            if (Storage::disk('public')->exists($pattern)) {
                Storage::disk('public')->delete($pattern);
            }
        }
    }

    private function createQRImage($format, $url, $sessionCode = null)
    {
        $sessionCode = $sessionCode ?? $this->session_code;
        $filename = "qr_{$sessionCode}.{$format}";
        $directory = 'qr-codes';
        $fullPath = $directory . '/' . $filename;

        // Ensure directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Create writer based on format
        $writer = $format === 'png' ? new PngWriter() : new SvgWriter();

        // Generate QR code using Builder API (v6)
        $builder = new Builder();
        $result = $builder->build(
            writer: $writer,
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10
        );

        // Save to storage
        Storage::disk('public')->put($fullPath, $result->getString());

        return $fullPath;
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function isExpired()
    {
        return $this->expires_at <= now() || $this->status === 'expired';
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function expire()
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    public function extend($hours = 2)
    {
        // If expires_at is somehow null, extend from the current time.
        // Otherwise, extend from the existing expiry time.
        $newExpiry = ($this->expires_at ?? now())->addHours($hours);

        $this->update([
            'expires_at' => $newExpiry,
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    public function scopeForTable($query, $tableId)
    {
        return $query->where('table_id', $tableId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
                    ->orWhere('status', 'expired');
    }

    // Accessors
    public function getTimeRemainingAttribute()
    {
        if ($this->isExpired()) {
            return null;
        }
        
        return $this->expires_at->diffInMinutes(now());
    }

    public function getDurationAttribute()
    {
        $endTime = $this->completed_at ?? now();
        return $this->started_at->diffInMinutes($endTime);
    }
}
