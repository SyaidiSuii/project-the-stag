<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_type',
        'title',
        'subtitle',
        'content',
        'image_url',
        'button_text',
        'button_link',
        'statistics_data',
        'extra_data',
        'is_active',
        'sort_order',
        'background_color_1',
        'background_color_2',
        'background_color_3',
        'gradient_direction',
        'text_color',
        'button_bg_color',
        'button_text_color',
        'discount_percentage',
        'promotion_code',
        'promotion_start_date',
        'promotion_end_date',
        'minimum_order_amount',
        'is_promotion_active',

        // Hero section specific fields
        'highlighted_text',
        'primary_button_text',
        'secondary_button_text',

        // About section specific fields
        'description',
        'feature_1',
        'feature_2',
        'feature_3',
        'feature_4',
        'about_primary_button_text',
        'about_secondary_button_text',

        // Statistics section specific fields
        'stat1_icon',
        'stat1_value',
        'stat1_label',
        'stat2_icon',
        'stat2_value',
        'stat2_label',
        'stat3_icon',
        'stat3_value',
        'stat3_label',
        'stat4_icon',
        'stat4_value',
        'stat4_label',

        // Contact section specific fields
        'address',
        'phone',
        'hours',
        'email',
        'feedback_form_title',
        'feedback_form_subtitle'
    ];

    protected $casts = [
        'statistics_data' => 'array',
        'extra_data' => 'array',
        'is_active' => 'boolean',
        'is_promotion_active' => 'boolean',
        'promotion_start_date' => 'datetime',
        'promotion_end_date' => 'datetime',
        'discount_percentage' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySection($query, $sectionType)
    {
        return $query->where('section_type', $sectionType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeActivePromotions($query)
    {
        return $query->where('section_type', 'promotion')
                    ->where('is_promotion_active', true)
                    ->where('promotion_start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('promotion_end_date')
                          ->orWhere('promotion_end_date', '>=', now());
                    });
    }

    public function getGradientStyleAttribute()
    {
        if ($this->background_color_1 && $this->background_color_2) {
            $gradient = "linear-gradient({$this->gradient_direction}, {$this->background_color_1}";
            if ($this->background_color_2) {
                $gradient .= ", {$this->background_color_2}";
            }
            if ($this->background_color_3) {
                $gradient .= ", {$this->background_color_3}";
            }
            $gradient .= ")";
            return $gradient;
        }
        return null;
    }
}
