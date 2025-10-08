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
        Schema::create('table_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('table_id')
                  ->nullable()
                  ->constrained('tables')
                  ->nullOnDelete();

            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 20);
            $table->integer('party_size');
            $table->text('special_requests')->nullable();

            $table->enum('status', [
                'pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'
            ])->default('pending');

            $table->string('confirmation_code', 10)->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('seated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('reminder_sent')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_reservations');
    }
};
