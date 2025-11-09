<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'stock_item_id',
        'quantity_required',
        'notes',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:2',
    ];

    /**
     * Get the menu item
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the stock item (ingredient)
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Calculate stock needed for given menu item quantity
     */
    public function calculateStockNeeded($menuItemQuantity)
    {
        return $this->quantity_required * $menuItemQuantity;
    }
}
