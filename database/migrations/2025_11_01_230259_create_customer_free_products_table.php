<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tracks free product entitlements from reward redemptions.
     * When customer redeems a "product" type reward, they get a free product credit.
     */
    public function up(): void
    {
        Schema::create('customer_free_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->onDelete('cascade');
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->foreignId('customer_reward_id')->constrained('customer_rewards')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');

            $table->enum('status', ['available', 'used', 'expired'])->default('available');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');

            $table->timestamps();

            $table->index(['customer_profile_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_free_products');
    }
};
