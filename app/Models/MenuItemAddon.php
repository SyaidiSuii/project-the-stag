<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItemAddon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'menu_item_id',
        'name',
        'price',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'price' => 0.00,
        'is_available' => true,
        'sort_order' => 0,
    ];

    /**
     * Get the menu item that owns this addon
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return '+RM ' . number_format($this->price, 2);
    }

    /**
     * Scope to get only available addons
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
