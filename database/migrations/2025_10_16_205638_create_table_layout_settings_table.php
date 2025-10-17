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
        Schema::create('table_layout_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'main_layout'
            $table->integer('container_width')->default(1200);
            $table->integer('container_height')->default(600);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_layout_settings');
    }
};
