<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItemReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'menu_item_id',
        'order_id',
        'rating',
        'review_text',
        'is_anonymous',
        'helpful_count',
        'admin_response',
        'admin_response_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_anonymous' => 'boolean',
        'helpful_count' => 'integer',
        'admin_response_at' => 'datetime',
    ];

    /**
     * Boot the model to update MenuItem ratings when review created/updated
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            $review->menuItem->recalculateRating();
        });

        static::updated(function ($review) {
            if ($review->isDirty('rating')) {
                $review->menuItem->recalculateRating();
            }
        });

        static::deleted(function ($review) {
            $review->menuItem->recalculateRating();
        });
    }

    /**
     * Get the user who wrote the review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu item being reviewed
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the order this review is associated with
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get reviewer name (handle anonymous reviews)
     */
    public function getReviewerNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Anonymous Customer';
        }

        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Check if review has admin response
     */
    public function hasAdminResponse()
    {
        return !empty($this->admin_response);
    }

    /**
     * Scope to get verified reviews (from actual orders)
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('order_id');
    }

    /**
     * Scope to get recent reviews
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope to filter by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to get reviews with text (not just ratings)
     */
    public function scopeWithText($query)
    {
        return $query->whereNotNull('review_text');
    }

    /**
     * Get star display (e.g., "★★★★★" for 5 stars)
     */
    public function getStarDisplayAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get formatted review date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M j, Y');
    }

    /**
     * Get formatted review date with time
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->created_at->format('M j, Y g:i A');
    }
}
