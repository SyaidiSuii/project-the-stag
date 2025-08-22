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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            // Payment details
            $table->enum('payment_method', [
                'cash', 'card', 'ewallet', 'bank_transfer', 'qr_pay', 'loyalty_points'
            ]);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('MYR');
            $table->string('transaction_id')->nullable();

            // Status
            $table->enum('payment_status', [
                'pending', 'processing', 'success', 'failed', 'refunded'
            ])->default('pending');

            // Extra info
            $table->json('payment_gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('reservation_id')->references('id')->on('table_reservations')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
