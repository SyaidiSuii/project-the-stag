<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'reservation_id',
        'title',
        'message',
        'type',
        'data',
        'is_sent',
        'sent_at',
        'is_read',
        'read_at',
        'delivery_status',
        'scheduled_for',
    ];

    protected $casts = [
        'data' => 'array',
        'is_sent' => 'boolean',
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'scheduled_for' => 'datetime',
    ];

    // 🔗 Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reservation()
    {
        return $this->belongsTo(TableReservation::class, 'reservation_id');
    }
}
