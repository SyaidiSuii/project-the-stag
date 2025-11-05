<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add customer_reward_id to track which reward redemption this free item is from.
     * This allows marking the CustomerReward as "redeemed" when order is paid.
     */
    public function up(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            // Add customer_reward_id to link free items to their reward redemption
            $table->foreignId('customer_reward_id')
                ->nullable()
                ->after('is_free_item')
                ->constrained('customer_rewards')
                ->onDelete('cascade')
                ->comment('Links free item to its reward redemption for status tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            $table->dropForeign(['customer_reward_id']);
            $table->dropColumn('customer_reward_id');
        });
    }
};
