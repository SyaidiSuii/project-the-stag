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
        Schema::create('quick_reorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_profile_id')
                  ->constrained('customer_profiles')
                  ->onDelete('cascade');
            $table->string('name');
            $table->json('order_items');
            $table->decimal('total_amount', 10, 2);
            $table->integer('order_frequency')->default(1);
            $table->timestamp('last_ordered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_reorders');
    }
};
