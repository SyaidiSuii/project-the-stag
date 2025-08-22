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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->onDelete('set null');

            $table->foreignId('reservation_id')
                  ->nullable()
                  ->constrained('table_reservations')
                  ->onDelete('set null');

            $table->string('title');
            $table->text('message');

            $table->enum('type', [
                'order_update',
                'order_ready',
                'reservation_reminder',
                'reservation_confirmed',
                'issue_alert',
                'eta_update'
            ]);

            $table->json('data')->nullable();

            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->enum('delivery_status', [
                'pending',
                'sent',
                'delivered',
                'failed'
            ])->default('pending');

            $table->timestamp('scheduled_for')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
