<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'qr_code',
        'nfc_tag_id',
        'location_description',
        'coordinates',
        'table_type',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'amenities'   => 'array',
        'is_active'   => 'boolean',
    ];
}
