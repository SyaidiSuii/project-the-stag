<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\RecommendationService;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
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
        'rating_count' => 'integer',
        'preparation_time' => 'integer',
    ];

    protected $attributes = [
        'availability' => true,
        'is_featured' => false,
        'rating_average' => 0.00,
        'rating_count' => 0,
        'preparation_time' => 15,
    ];

    /**
     * Boot the model and add event listeners for AI model retraining
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger AI model retrain when menu item is created
        static::created(function ($menuItem) {
            try {
                app(RecommendationService::class)->onMenuUpdated('created', $menuItem->id);
            } catch (\Exception $e) {
                \Log::warning('Failed to trigger AI retrain on menu item creation', [
                    'menu_item_id' => $menuItem->id,
                    'error' => $e->getMessage()
                ]);
            }
        });

        // Trigger AI model retrain when menu item is updated
        static::updated(function ($menuItem) {
            // Check if important fields changed
            if ($menuItem->isDirty(['name', 'category_id', 'price', 'availability', 'is_featured'])) {
                try {
                    app(RecommendationService::class)->onMenuUpdated('updated', $menuItem->id);
                } catch (\Exception $e) {
                    \Log::warning('Failed to trigger AI retrain on menu item update', [
                        'menu_item_id' => $menuItem->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });

        // Trigger retrain when menu item is deleted
        static::deleted(function ($menuItem) {
            try {
                app(RecommendationService::class)->onMenuUpdated('deleted', $menuItem->id);
            } catch (\Exception $e) {
                \Log::warning('Failed to trigger AI retrain on menu item deletion', [
                    'menu_item_id' => $menuItem->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    /**
     * Get the category that owns the menu item
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all order items for this menu item
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to get only available items
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', true);
    }

    /**
     * Scope to get only featured items
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get the full category path (for sub-categories)
     */
    public function getCategoryPathAttribute()
    {
        if (!$this->category) {
            return 'Uncategorized';
        }

        if ($this->category->parent) {
            return $this->category->parent->name . ' > ' . $this->category->name;
        }

        return $this->category->name;
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return 'RM ' . number_format($this->price, 2);
    }

    /**
     * Get rating display (e.g., "4.5 (23 reviews)")
     */
    public function getRatingDisplayAttribute()
    {
        if ($this->rating_count == 0) {
            return 'No ratings yet';
        }

        $reviews = $this->rating_count == 1 ? 'review' : 'reviews';
        return number_format($this->rating_average, 1) . " ({$this->rating_count} {$reviews})";
    }

    /**
     * Check if item has any allergens
     */
    public function hasAllergens()
    {
        return !empty($this->allergens);
    }

    /**
     * Get allergens as a comma-separated string
     */
    public function getAllergensStringAttribute()
    {
        return $this->hasAllergens() ? implode(', ', $this->allergens) : 'None';
    }

    /**
     * Update rating average when new rating is added
     */
    public function addRating($rating)
    {
        $newRatingCount = $this->rating_count + 1;
        $newRatingAverage = (($this->rating_average * $this->rating_count) + $rating) / $newRatingCount;

        $this->update([
            'rating_average' => round($newRatingAverage, 2),
            'rating_count' => $newRatingCount
        ]);

        return $this;
    }

    /**
     * Toggle availability status
     */
    public function toggleAvailability()
    {
        $this->update(['availability' => !$this->availability]);
        return $this;
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured()
    {
        $this->update(['is_featured' => !$this->is_featured]);
        return $this;
    }

    /**
     * Get the image URL for display
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? \Storage::url($this->image) : null;
    }
}