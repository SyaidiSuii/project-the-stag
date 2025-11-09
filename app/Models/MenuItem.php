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
        'station_override_id',
        'availability',
        'is_featured',
        'is_set_meal', // Added for set meals
        'rating_average',
        'rating_count',
        'station_type',
        'kitchen_load_factor',
    ];

    protected $casts = [
        'allergens' => 'array',
        'availability' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'preparation_time' => 'integer',
        'kitchen_load_factor' => 'decimal:2',
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
     * Get the station override for this menu item
     */
    public function stationOverride()
    {
        return $this->belongsTo(KitchenStation::class, 'station_override_id');
    }

    /**
     * Get the effective kitchen station for this item
     * Returns override if set, otherwise returns category's default station
     */
    public function getEffectiveStation()
    {
        // If item has station override, use it
        if ($this->station_override_id) {
            return $this->stationOverride;
        }

        // Otherwise, use category's effective station
        if ($this->category) {
            return $this->category->getEffectiveStation();
        }

        return null;
    }

    /**
     * Get all order items for this menu item
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all promotions that include this menu item
     */
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_items')
            ->withPivot([
                'quantity',
                'is_free',
                'is_required',
                'is_customizable',
                'custom_price',
                'item_options',
                'sort_order'
            ])
            ->withTimestamps();
    }

    /**
     * Get active promotions for this item
     */
    public function activePromotions()
    {
        return $this->promotions()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // ============================================
    // STOCK MANAGEMENT - TEMPORARILY DISABLED
    // ============================================
    // Uncomment these methods if you want to re-enable stock management feature

    /*
    /**
     * Get all stock items (ingredients) used in this menu item
     *\/
    public function stockItems()
    {
        return $this->belongsToMany(StockItem::class, 'recipe_ingredients')
            ->withPivot('quantity_required', 'notes')
            ->withTimestamps();
    }

    /**
     * Get recipe ingredients (pivot records)
     *\/
    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Check if all ingredients are available for this menu item
     *\/
    public function hasAvailableStock($quantity = 1)
    {
        foreach ($this->recipeIngredients as $ingredient) {
            $requiredQty = $ingredient->quantity_required * $quantity;
            if ($ingredient->stockItem->current_quantity < $requiredQty) {
                return false;
            }
        }
        return true;
    }

    /**
     * Deduct stock for this menu item (when order is placed)
     *\/
    public function deductStock($quantity, $orderId = null)
    {
        foreach ($this->recipeIngredients as $ingredient) {
            $requiredQty = $ingredient->quantity_required * $quantity;
            $ingredient->stockItem->reduceStock(
                $requiredQty,
                'Order',
                $orderId,
                "Used for {$this->name} x{$quantity}"
            );
        }
        return $this;
    }
    */

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
     * Get the effective category type (from parent if subcategory doesn't have type)
     */
    public function getEffectiveCategoryTypeAttribute()
    {
        if (!$this->category) {
            return null;
        }

        // If the category has a type, use it
        if ($this->category->type) {
            return $this->category->type;
        }

        // If the category is a subcategory (has a parent), get type from the parent
        if ($this->category->parent) {
            return $this->category->parent->type;
        }

        return $this->category->type; // Return as is
    }

    /**
     * Get the image URL for display
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? \Storage::url($this->image) : null;
    }

    /**
     * The components that make up a set meal.
     */
    public function components()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_set_meal', 'set_meal_menu_item_id', 'component_menu_item_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * The sets that this menu item is a part of.
     */
    public function partOfSets()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_set_meal', 'component_menu_item_id', 'set_meal_menu_item_id')
            ->withTimestamps();
    }

    /**
     * Get all reviews for this menu item
     */
    public function reviews()
    {
        return $this->hasMany(MenuItemReview::class);
    }

    /**
     * Get all add-ons for this menu item
     */
    public function addons()
    {
        return $this->hasMany(MenuItemAddon::class);
    }

    /**
     * Get available add-ons for this menu item
     */
    public function availableAddons()
    {
        return $this->addons()->available()->ordered();
    }

    /**
     * Recalculate rating average based on all reviews
     * More accurate than incremental addRating method
     */
    public function recalculateRating()
    {
        $reviews = $this->reviews()->whereNull('deleted_at')->get();

        if ($reviews->count() === 0) {
            $this->update([
                'rating_average' => 0.00,
                'rating_count' => 0
            ]);
            return $this;
        }

        $totalRating = $reviews->sum('rating');
        $reviewCount = $reviews->count();
        $average = $totalRating / $reviewCount;

        $this->update([
            'rating_average' => round($average, 2),
            'rating_count' => $reviewCount
        ]);

        return $this;
    }

    /**
     * Get star rating display (e.g., "★★★★☆")
     */
    public function getStarRatingAttribute()
    {
        $fullStars = floor($this->rating_average);
        $halfStar = ($this->rating_average - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return str_repeat('★', $fullStars) .
               ($halfStar ? '⯨' : '') .
               str_repeat('☆', $emptyStars);
    }

    /**
     * Get the effective station type (from category default if not set)
     */
    public function getEffectiveStationTypeAttribute()
    {
        // If item has its own station type, use it
        if ($this->station_type) {
            return $this->station_type;
        }

        // Otherwise, inherit from category
        if ($this->category && $this->category->default_station_type) {
            return $this->category->default_station_type;
        }

        // Default to general kitchen
        return 'general_kitchen';
    }

    /**
     * Get the effective kitchen load factor
     */
    public function getEffectiveLoadFactorAttribute()
    {
        // If item has its own load factor, use it
        if ($this->kitchen_load_factor !== null) {
            return $this->kitchen_load_factor;
        }

        // Otherwise, inherit from category
        if ($this->category && $this->category->default_load_factor !== null) {
            return $this->category->default_load_factor;
        }

        // Default to 1.0 (normal complexity)
        return 1.0;
    }

    /**
     * Calculate total load points for this item
     * Load = load_factor * (preparation_time / 10)
     */
    public function getLoadPointsAttribute()
    {
        $loadFactor = $this->effective_load_factor;
        $prepTime = $this->preparation_time ?? 15;

        return round($loadFactor * ($prepTime / 10), 2);
    }

    /**
     * Get station icon based on type
     */
    public function getStationIconAttribute()
    {
        return match($this->effective_station_type) {
            'general_kitchen' => '🍴',
            'drinks' => '🍹',
            'desserts' => '🍰',
            default => '🍴'
        };
    }

    /**
     * Get station display name
     */
    public function getStationDisplayNameAttribute()
    {
        return match($this->effective_station_type) {
            'general_kitchen' => 'Main Kitchen',
            'drinks' => 'Drinks Bar',
            'desserts' => 'Dessert Station',
            default => 'Main Kitchen'
        };
    }
}
