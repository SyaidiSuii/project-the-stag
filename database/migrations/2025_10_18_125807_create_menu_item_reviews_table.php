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
        Schema::create('menu_item_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->tinyInteger('rating')->unsigned()->comment('Rating from 1-5 stars');
            $table->text('review_text')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_response_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['menu_item_id', 'rating']);
            $table->index(['user_id']);
            $table->index(['order_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_reviews');
    }
};
