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
        Schema::create('order_trackings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->enum('status', [
                'received',
                'confirmed',
                'preparing',
                'cooking',
                'plating',
                'ready',
                'served',
                'completed',
            ]);

            $table->string('station_name', 100)->nullable(); // Kitchen, Bar, Grill
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->integer('estimated_time')->nullable(); // dalam minit
            $table->integer('actual_time')->nullable(); // dalam minit

            $table->text('notes')->nullable();

            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_trackings');
    }
};
