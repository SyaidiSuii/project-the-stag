<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickReorder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'order_name',
        'order_items',
        'total_amount',
        'order_frequency',
        'last_ordered_at',
    ];

    protected $casts = [
        'order_items' => 'array',
        'last_ordered_at' => 'datetime',
    ];

    // Relationship dengan CustomerProfile
    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }
}
