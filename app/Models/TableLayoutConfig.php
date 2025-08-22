<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableLayoutConfig extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'layout_name',
        'floor_plan_image',
        'canvas_width',
        'canvas_height',
        'is_active',
    ];
}
