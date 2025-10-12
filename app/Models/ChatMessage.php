<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_session_id',
        'role',
        'message',
        'context_data',
        'metadata'
    ];

    protected $casts = [
        'context_data' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the chat session that owns the message
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Check if message is from user
     */
    public function isUserMessage(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if message is from assistant
     */
    public function isAssistantMessage(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Format message for API response
     */
    public function toApiFormat(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'content' => $this->message,
            'timestamp' => $this->created_at->toIso8601String(),
        ];
    }
}
