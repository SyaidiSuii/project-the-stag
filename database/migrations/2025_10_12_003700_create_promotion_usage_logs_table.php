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
        Schema::create('promotion_usage_logs', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('promotion_id')->constrained('promotions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')
                ->comment('Null for guest orders');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');

            // Usage details
            $table->decimal('discount_amount', 10, 2)
                ->comment('Actual discount amount given');

            $table->decimal('order_subtotal', 10, 2)
                ->comment('Order subtotal before discount');

            $table->decimal('order_total', 10, 2)
                ->comment('Order total after discount');

            $table->string('promo_code', 50)->nullable()
                ->comment('Promo code used (if applicable)');

            $table->string('session_id', 100)->nullable()
                ->comment('Session ID for guest tracking');

            $table->string('ip_address', 45)->nullable()
                ->comment('User IP address for fraud detection');

            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();

            // Indexes for analytics and reporting
            $table->index('promotion_id');
            $table->index('user_id');
            $table->index('order_id');
            $table->index('used_at');
            $table->index(['promotion_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_usage_logs');
    }
};
