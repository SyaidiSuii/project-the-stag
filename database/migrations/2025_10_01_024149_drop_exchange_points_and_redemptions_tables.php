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
        Schema::dropIfExists('exchange_point_redemptions');
        Schema::dropIfExists('exchange_points');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate exchange_points table
        Schema::create('exchange_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('reward_type')->nullable();
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->integer('validity_days')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->decimal('minimum_order', 10, 2)->nullable();
            $table->enum('redemption_method', ['show_to_staff', 'qr_code_scan', 'auto_applied', 'bring_voucher', 'phone_verification'])->nullable();
            $table->boolean('transferable')->default(false);
            $table->foreignId('voucher_template_id')->nullable()->constrained('voucher_templates')->onDelete('set null');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Recreate exchange_point_redemptions table
        Schema::create('exchange_point_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exchange_point_id')->constrained('exchange_points')->onDelete('cascade');
            $table->enum('status', ['pending', 'redeemed', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('redeemed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
