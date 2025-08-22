<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image_url',
        'allergens',
        'preparation_time',
        'availability',
        'is_featured',
        'rating_average',
        'rating_count',
    ];

    protected $casts = [
        'allergens' => 'array',
        'availability' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'rating_average' => 'decimal:2',
    ];
}
