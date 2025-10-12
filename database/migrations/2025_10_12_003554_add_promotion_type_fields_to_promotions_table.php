<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // Add promotion type system
            $table->enum('promotion_type', [
                'combo_deal',       // Set meals/bundles
                'item_discount',    // Discount on specific items/categories
                'buy_x_free_y',     // Buy X get Y free
                'promo_code',       // Voucher code system
                'seasonal',         // Seasonal/event promotions
                'bundle'            // Multi-item bundles
            ])->default('promo_code')->after('name');

            // Type-specific configuration stored as JSON
            $table->json('promo_config')->nullable()->after('promotion_type');

            // Time-based restrictions
            $table->json('applicable_days')->nullable()->after('end_date')
                ->comment('["monday", "tuesday", ...] or null for all days');
            $table->time('applicable_start_time')->nullable()->after('applicable_days');
            $table->time('applicable_end_time')->nullable()->after('applicable_start_time');

            // Terms and display
            $table->text('terms_conditions')->nullable()->after('minimum_order_value');
            $table->string('badge_text', 50)->nullable()->after('image_path')
                ->comment('Display badge like "HOT DEAL!", "NEW!", "LIMITED"');
            $table->string('banner_color', 7)->nullable()->after('badge_text')
                ->comment('Hex color for UI styling');

            // Usage limits
            $table->integer('usage_limit_per_customer')->nullable()->after('is_active')
                ->comment('Max uses per customer, null = unlimited');
            $table->integer('total_usage_limit')->nullable()->after('usage_limit_per_customer')
                ->comment('Max total uses, null = unlimited');
            $table->integer('current_usage_count')->default(0)->after('total_usage_limit');

            // Enhanced discount controls
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('discount_value')
                ->comment('Maximum discount cap in RM');

            // Display ordering
            $table->integer('display_order')->default(0)->after('is_active')
                ->comment('Sort order for display');

            // Feature flag
            $table->boolean('is_featured')->default(false)->after('is_active')
                ->comment('Show in featured/hot deals section');

            // Add indexes for performance
            $table->index('promotion_type');
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index(['is_featured', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropIndex(['promotions_promotion_type_index']);
            $table->dropIndex(['promotions_is_active_start_date_end_date_index']);
            $table->dropIndex(['promotions_is_featured_display_order_index']);

            $table->dropColumn([
                'promotion_type',
                'promo_config',
                'applicable_days',
                'applicable_start_time',
                'applicable_end_time',
                'terms_conditions',
                'badge_text',
                'banner_color',
                'usage_limit_per_customer',
                'total_usage_limit',
                'current_usage_count',
                'max_discount_amount',
                'display_order',
                'is_featured'
            ]);
        });
    }
};
