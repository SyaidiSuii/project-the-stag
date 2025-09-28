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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('reward_type', ['points', 'voucher', 'tier_upgrade']);
            $table->integer('points_required')->nullable()->comment('berapa points nak redeem');
            $table->foreignId('voucher_template_id')->nullable()->constrained('voucher_templates')->onDelete('set null');
            $table->integer('expiry_days')->nullable()->comment('expiry after claim');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
