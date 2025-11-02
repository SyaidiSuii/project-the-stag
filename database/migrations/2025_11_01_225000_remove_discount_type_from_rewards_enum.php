<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove 'discount' from reward_type ENUM as it's redundant with voucher.
     * Any existing discount rewards will be converted to voucher type.
     */
    public function up(): void
    {
        // Convert existing discount type to voucher
        DB::statement("UPDATE rewards SET reward_type = 'voucher' WHERE reward_type = 'discount'");

        // Update ENUM to remove discount
        DB::statement("ALTER TABLE rewards MODIFY COLUMN reward_type ENUM('voucher', 'product', 'points') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore discount option
        DB::statement("ALTER TABLE rewards MODIFY COLUMN reward_type ENUM('voucher', 'discount', 'product', 'points') NOT NULL");
    }
};
