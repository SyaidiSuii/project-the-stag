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
    
}
