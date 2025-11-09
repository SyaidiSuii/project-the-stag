<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'payment_terms',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all stock items supplied by this supplier
     */
    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }

    /**
     * Get all purchase orders for this supplier
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get active stock items only
     */
    public function activeStockItems()
    {
        return $this->hasMany(StockItem::class)->where('is_active', true);
    }

    /**
     * Scope: Only active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted contact info
     */
    public function getContactInfoAttribute()
    {
        $info = [];
        if ($this->contact_person) $info[] = $this->contact_person;
        if ($this->phone) $info[] = $this->phone;
        if ($this->email) $info[] = $this->email;

        return implode(' | ', $info);
    }
}
