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
        Schema::create('rewards_contents', function (Blueprint $table) {
            $table->id();
            $table->string('main_title')->nullable();
            $table->string('points_label')->nullable();
            $table->string('checkin_header')->nullable();
            $table->text('checkin_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards_contents');
    }
};
