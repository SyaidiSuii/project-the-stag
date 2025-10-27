<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'sort_order',
        'default_station_type',
        'default_load_factor',
        'default_station_id',
    ];

    protected $casts = [
        'default_load_factor' => 'decimal:2',
    ];

    // Satu kategori boleh ada banyak sub-kategori
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('sort_order');
    }
    
    // Satu kategori boleh ada satu parent
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    // Satu kategori boleh ada banyak menu items
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    // Default kitchen station for this category
    public function defaultStation()
    {
        return $this->belongsTo(KitchenStation::class, 'default_station_id');
    }

    // Method to get the effective type (from parent if this is a subcategory without a type)
    public function getEffectiveType()
    {
        if ($this->type) {
            return $this->type;
        }

        if ($this->parent) {
            return $this->parent->type;
        }

        return $this->type;
    }

    // Get the effective station (from parent if this category doesn't have one)
    public function getEffectiveStation()
    {
        if ($this->default_station_id) {
            return $this->defaultStation;
        }

        if ($this->parent) {
            return $this->parent->getEffectiveStation();
        }

        return null;
    }

}
