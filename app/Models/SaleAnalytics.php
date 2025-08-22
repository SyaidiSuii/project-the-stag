<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleAnalytics extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'total_sales',
        'total_orders',
        'average_order_value',
        'peak_hours',
        'popular_items',
        'unique_customers',
        'new_customers',
        'returning_customers',
        'dine_in_orders',
        'takeaway_orders',
        'delivery_orders',
        'mobile_orders',
        'qr_orders',
        'total_revenue_dine_in',
        'total_revenue_takeaway',
        'total_revenue_delivery',
        'average_preparation_time',
        'customer_satisfaction_avg',
    ];

    protected $casts = [
        'peak_hours' => 'array',
        'popular_items' => 'array',
        'date' => 'date',
        'total_sales' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'total_revenue_dine_in' => 'decimal:2',
        'total_revenue_takeaway' => 'decimal:2',
        'total_revenue_delivery' => 'decimal:2',
        'average_preparation_time' => 'decimal:2',
        'customer_satisfaction_avg' => 'decimal:2',
    ];
}
