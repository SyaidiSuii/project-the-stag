<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCustomization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'customization_type',
        'customization_value',
        'additional_price',
    ];

    /**
     * Relationship ke OrderItem
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
