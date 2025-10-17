<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySalesSummary extends Model
{
    use HasFactory;

    protected $table = 'daily_sales_summary';

    protected $fillable = [
        'date',
        'total_revenue',
        'total_orders',
        'total_items_sold',
    ];

    protected $casts = [
        'date' => 'date',
        'total_revenue' => 'decimal:2',
    ];
}
