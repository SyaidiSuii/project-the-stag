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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('table_id')
                ->nullable()
                ->constrained('tables')
                ->onDelete('set null');

            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained('table_reservations')
                ->onDelete('set null');

            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery', 'event']);
            $table->enum('order_source', ['counter', 'web', 'mobile', 'waiter', 'qr_scan'])->default('counter');
            $table->enum('order_status', ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('pending');

            $table->timestamp('order_time')->useCurrent();
            $table->string('table_number', 10)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->json('special_instructions')->nullable();

            $table->timestamp('estimated_completion_time')->nullable();
            $table->timestamp('actual_completion_time')->nullable();
            $table->boolean('is_rush_order')->default(false);

            $table->string('confirmation_code', 10)->unique()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
