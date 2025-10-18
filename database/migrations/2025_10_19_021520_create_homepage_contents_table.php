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
        Schema::create('homepage_contents', function (Blueprint $table) {
            $table->id();

            // General Section Info
            $table->enum('section_type', [
                'hero', 'statistics', 'featured_menu', 'about', 'contact', 'promotion'
            ]);

            $table->string('title', 255)->nullable();
            $table->text('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_link', 500)->nullable();

            $table->json('statistics_data')->nullable(); // For statistics section
            $table->json('extra_data')->nullable(); // Additional section-specific data

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            /* -------------------------------
             * HERO SECTION SPECIFIC COLUMNS
             * ------------------------------- */
            $table->string('highlighted_text', 255)->nullable();
            $table->string('primary_button_text', 100)->nullable();
            $table->string('secondary_button_text', 100)->nullable();

            /* -------------------------------
             * ABOUT SECTION SPECIFIC COLUMNS
             * ------------------------------- */
            $table->text('description')->nullable();
            $table->string('feature_1', 255)->nullable();
            $table->string('feature_2', 255)->nullable();
            $table->string('feature_3', 255)->nullable();
            $table->string('feature_4', 255)->nullable();
            $table->string('about_primary_button_text', 100)->nullable();
            $table->string('about_secondary_button_text', 100)->nullable();

            /* -------------------------------
             * STATISTICS SECTION SPECIFIC COLUMNS
             * ------------------------------- */
            $table->string('stat1_icon', 10)->nullable();
            $table->string('stat1_value', 50)->nullable();
            $table->string('stat1_label', 100)->nullable();
            $table->string('stat2_icon', 10)->nullable();
            $table->string('stat2_value', 50)->nullable();
            $table->string('stat2_label', 100)->nullable();
            $table->string('stat3_icon', 10)->nullable();
            $table->string('stat3_value', 50)->nullable();
            $table->string('stat3_label', 100)->nullable();
            $table->string('stat4_icon', 10)->nullable();
            $table->string('stat4_value', 50)->nullable();
            $table->string('stat4_label', 100)->nullable();

            /* -------------------------------
             * CONTACT SECTION SPECIFIC COLUMNS
             * ------------------------------- */
            $table->string('address', 500)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('hours', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('feedback_form_title', 255)->nullable();
            $table->string('feedback_form_subtitle', 500)->nullable();

            /* -------------------------------
             * PROMOTION SECTION SPECIFIC COLUMNS
             * ------------------------------- */
            $table->decimal('discount_percentage', 5, 2)->nullable(); // 25.50%
            $table->string('promotion_code', 50)->nullable();
            $table->datetime('promotion_start_date')->nullable();
            $table->datetime('promotion_end_date')->nullable();
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->boolean('is_promotion_active')->default(false);

            /* -------------------------------
             * COLOR & STYLE FIELDS
             * ------------------------------- */
            $table->string('background_color_1', 7)->nullable(); // #ffffff
            $table->string('background_color_2', 7)->nullable(); // #000000
            $table->string('background_color_3', 7)->nullable(); // #ff0000
            $table->string('gradient_direction', 20)->default('to right');
            $table->string('text_color', 7)->nullable(); // #ffffff for text
            $table->string('button_bg_color', 7)->nullable();
            $table->string('button_text_color', 7)->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['section_type', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_contents');
    }
};
