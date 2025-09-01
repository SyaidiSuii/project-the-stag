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
        Schema::create('menu_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->onDelete('cascade');
            $table->string('customization_type', 100);
            $table->string('customization_value', 255);
            $table->decimal('additional_price', 8, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_customizations');
    }
};
