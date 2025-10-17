<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'stock_item_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the purchase order this item belongs to
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the stock item
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Calculate total price (quantity * unit_price)
     */
    public function calculateTotalPrice()
    {
        $this->total_price = $this->quantity_ordered * $this->unit_price;
        $this->save();
        return $this;
    }

    /**
     * Check if fully received
     */
    public function isFullyReceived()
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Get pending quantity
     */
    public function getPendingQuantityAttribute()
    {
        return $this->quantity_ordered - $this->quantity_received;
    }
}
