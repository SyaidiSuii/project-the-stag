<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_rewards', function (Blueprint $table) {
            // Add missing fields that are used in controllers and views
            $table->integer('points_spent')->default(0)->after('reward_id');
            $table->string('redemption_code', 50)->unique()->nullable()->after('points_spent');
            $table->timestamp('claimed_at')->nullable()->after('status');
            $table->timestamp('expires_at')->nullable()->after('claimed_at');

            // // Step 1: Change enum to string temporarily to allow data conversion
            // $table->string('status', 20)->default('pending')->change();
        });

        // // Step 2: Update existing 'active' status to 'pending'
        // DB::table('customer_rewards')
        //     ->where('status', 'active')
        //     ->update(['status' => 'pending']);

        // // Step 3: Change back to enum with new values
        // Schema::table('customer_rewards', function (Blueprint $table) {
        //     $table->enum('status', ['pending', 'redeemed', 'expired', 'cancelled'])->default('pending')->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_rewards', function (Blueprint $table) {
            // Drop added columns
            $table->dropColumn(['points_spent', 'redemption_code', 'claimed_at', 'expires_at']);

            // Revert status enum to original
            // $table->enum('status', ['active', 'redeemed', 'expired'])->default('active')->change();
        });
    }
};
