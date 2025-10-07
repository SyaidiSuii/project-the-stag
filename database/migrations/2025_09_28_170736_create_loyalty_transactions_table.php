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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_profile_id')->constrained('customer_profiles')->onDelete('cascade');
            $table->enum('transaction_type', ['earn_points', 'redeem_points', 'expire_points', 'redeem_voucher']);
            $table->integer('points_change')->default(0);
            $table->string('description');
            $table->bigInteger('reference_id')->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->integer('balance_after');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
