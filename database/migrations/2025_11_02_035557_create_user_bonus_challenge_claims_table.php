<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Track individual user claims for bonus challenges
     * - Prevents duplicate claims beyond max_claims_per_user
     * - Records when user claimed the bonus
     * - Tracks points awarded
     */
    public function up(): void
    {
        Schema::create('user_bonus_challenge_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bonus_point_challenge_id')->constrained('bonus_point_challenges')->onDelete('cascade');
            $table->integer('points_awarded');
            $table->timestamps();

            // Ensure we can quickly check if user already claimed
            $table->index(['user_id', 'bonus_point_challenge_id'], 'user_challenge_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bonus_challenge_claims');
    }
};
