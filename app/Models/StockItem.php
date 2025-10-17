<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'unit_of_measure',
        'current_quantity',
        'minimum_threshold',
        'reorder_point',
        'reorder_quantity',
        'unit_price',
        'supplier_id',
        'storage_location',
        'is_active',
        'last_restock_date',
        'notes',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'minimum_threshold' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'reorder_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
        'last_restock_date' => 'date',
    ];

    /**
     * Get the supplier for this stock item
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get all transactions for this stock item
     */
    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get all purchase order items
     */
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get all usage predictions
     */
    public function usagePredictions()
    {
        return $this->hasMany(StockUsagePrediction::class);
    }

    /**
     * Get all menu items that use this ingredient (via recipe_ingredients pivot)
     */
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'recipe_ingredients')
            ->withPivot('quantity_required', 'notes')
            ->withTimestamps();
    }

    /**
     * Scope: Low stock items (below reorder point)
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_quantity <= reorder_point');
    }

    /**
     * Scope: Critical stock items (below minimum threshold)
     */
    public function scopeCriticalStock($query)
    {
        return $query->whereRaw('current_quantity <= minimum_threshold');
    }

    /**
     * Scope: Active items only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if stock is low (needs reordering)
     */
    public function isLowStock()
    {
        return $this->current_quantity <= $this->reorder_point;
    }

    /**
     * Check if stock is critical (below minimum)
     */
    public function isCriticalStock()
    {
        return $this->current_quantity <= $this->minimum_threshold;
    }

    /**
     * Get stock status badge color
     */
    public function getStockStatusAttribute()
    {
        if ($this->isCriticalStock()) {
            return 'critical'; // red
        } elseif ($this->isLowStock()) {
            return 'low'; // yellow
        }
        return 'good'; // green
    }

    /**
     * Get stock status text
     */
    public function getStockStatusTextAttribute()
    {
        if ($this->isCriticalStock()) {
            return 'Critical - Reorder Immediately';
        } elseif ($this->isLowStock()) {
            return 'Low Stock - Reorder Soon';
        }
        return 'Stock Level Good';
    }

    /**
     * Calculate total value of current stock
     */
    public function getStockValueAttribute()
    {
        return $this->current_quantity * $this->unit_price;
    }

    /**
     * Get formatted current quantity with unit
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->current_quantity, 2) . ' ' . $this->unit_of_measure;
    }

    /**
     * Add stock (restock)
     */
    public function addStock($quantity, $reference_type = null, $reference_id = null, $notes = null, $user_id = null)
    {
        $previousQty = $this->current_quantity;
        $this->current_quantity += $quantity;
        $this->last_restock_date = now();
        $this->save();

        // Log transaction
        $this->transactions()->create([
            'transaction_type' => 'restock',
            'quantity' => $quantity,
            'previous_quantity' => $previousQty,
            'new_quantity' => $this->current_quantity,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'notes' => $notes,
            'created_by' => $user_id,
        ]);

        return $this;
    }

    /**
     * Reduce stock (usage)
     */
    public function reduceStock($quantity, $reference_type = null, $reference_id = null, $notes = null, $user_id = null)
    {
        $previousQty = $this->current_quantity;
        $this->current_quantity -= $quantity;

        if ($this->current_quantity < 0) {
            $this->current_quantity = 0;
        }

        $this->save();

        // Log transaction
        $this->transactions()->create([
            'transaction_type' => 'usage',
            'quantity' => -$quantity, // Negative for usage
            'previous_quantity' => $previousQty,
            'new_quantity' => $this->current_quantity,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'notes' => $notes,
            'created_by' => $user_id,
        ]);

        return $this;
    }

    /**
     * Adjust stock (manual adjustment)
     */
    public function adjustStock($newQuantity, $notes = null, $user_id = null)
    {
        $previousQty = $this->current_quantity;
        $difference = $newQuantity - $previousQty;
        $this->current_quantity = $newQuantity;
        $this->save();

        // Log transaction
        $this->transactions()->create([
            'transaction_type' => 'adjustment',
            'quantity' => $difference,
            'previous_quantity' => $previousQty,
            'new_quantity' => $this->current_quantity,
            'notes' => $notes,
            'created_by' => $user_id,
        ]);

        return $this;
    }
}
