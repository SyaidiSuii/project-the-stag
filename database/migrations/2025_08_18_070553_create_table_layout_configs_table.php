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
        Schema::create('table_layout_configs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment
            $table->string('layout_name'); // NOT NULL by default
            $table->string('floor_plan_image')->nullable();
            $table->integer('canvas_width')->default(800);
            $table->integer('canvas_height')->default(600);
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_layout_configs');
    }
};
