<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_token',
        'user_ip',
        'user_agent',
        'status',
        'last_activity_at'
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->session_token)) {
                $model->session_token = Str::random(64);
            }
            $model->last_activity_at = now();
        });
    }

    /**
     * Get the user that owns the chat session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all messages for this chat session
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Update last activity timestamp
     */
    public function touchActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * End this chat session
     */
    public function endSession(): void
    {
        $this->update(['status' => 'ended']);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if session has timed out (30 minutes of inactivity)
     */
    public function hasTimedOut(): bool
    {
        if ($this->status === 'timeout') {
            return true;
        }

        if ($this->last_activity_at && $this->last_activity_at->diffInMinutes(now()) > 30) {
            $this->update(['status' => 'timeout']);
            return true;
        }

        return false;
    }
}
