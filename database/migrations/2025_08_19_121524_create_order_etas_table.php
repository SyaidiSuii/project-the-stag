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
        Schema::create('order_etas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->integer('initial_estimate'); // dalam minit
            $table->integer('current_estimate');
            $table->integer('actual_completion_time')->nullable(); // dalam minit

            $table->string('delay_reason')->nullable();
            $table->boolean('is_delayed')->default(false);
            $table->integer('delay_duration')->default(0); // dalam minit
            $table->boolean('customer_notified')->default(false);

            $table->timestamp('last_updated')
                ->useCurrent()
                ->useCurrentOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_etas');
    }
};
