<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'special_notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    // Helper methods
    public function getTotalPriceAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    public static function getCartTotal($userId)
    {
        return static::where('user_id', $userId)->get()->sum('total_price');
    }

    public static function getCartCount($userId)
    {
        return static::where('user_id', $userId)->sum('quantity');
    }
}
