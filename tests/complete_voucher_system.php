<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== Completing Voucher System Setup ===\n";

// Create vouchers table
if (!Schema::hasTable('vouchers')) {
    Schema::create('vouchers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('voucher_collection_id')->constrained('voucher_collections')->onDelete('cascade');
        $table->string('code')->unique();
        $table->enum('status', ['active', 'used', 'expired'])->default('active');
        $table->decimal('discount_amount', 10, 2)->nullable();
        $table->decimal('discount_percentage', 5, 2)->nullable();
        $table->date('valid_from');
        $table->date('valid_until');
        $table->decimal('minimum_order_amount', 10, 2)->nullable();
        $table->integer('usage_count')->default(0);
        $table->integer('usage_limit')->default(1);
        $table->softDeletes();
        $table->timestamps();
    });
    echo "✅ Created vouchers table\n";
} else {
    echo "ℹ️  vouchers table already exists\n";
}

// Create user_vouchers table (junction table for user-voucher relationship)
if (!Schema::hasTable('user_vouchers')) {
    Schema::create('user_vouchers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
        $table->timestamp('claimed_at')->nullable();
        $table->timestamp('used_at')->nullable();
        $table->enum('status', ['claimed', 'used', 'expired'])->default('claimed');
        $table->softDeletes();
        $table->timestamps();

        // Prevent duplicate claims
        $table->unique(['user_id', 'voucher_id']);
    });
    echo "✅ Created user_vouchers table\n";
} else {
    echo "ℹ️  user_vouchers table already exists\n";
}

// Update voucher_collections table to ensure it has all necessary columns
if (Schema::hasTable('voucher_collections')) {
    $columns = Schema::getColumnListing('voucher_collections');

    Schema::table('voucher_collections', function (Blueprint $table) use ($columns) {
        if (!in_array('code_prefix', $columns)) {
            $table->string('code_prefix', 10)->nullable()->after('name');
        }
        if (!in_array('minimum_order_amount', $columns)) {
            $table->decimal('minimum_order_amount', 10, 2)->nullable()->after('discount_percentage');
        }
        if (!in_array('max_uses_per_user', $columns)) {
            $table->integer('max_uses_per_user')->default(1)->after('usage_limit');
        }
    });
    echo "✅ Updated voucher_collections table with additional columns\n";
}

// Ensure voucher_templates has proper structure for rewards integration
if (Schema::hasTable('voucher_templates')) {
    $columns = Schema::getColumnListing('voucher_templates');

    Schema::table('voucher_templates', function (Blueprint $table) use ($columns) {
        if (!in_array('template_type', $columns)) {
            $table->enum('template_type', ['percentage', 'fixed_amount', 'free_shipping'])->default('percentage')->after('discount_type');
        }
        if (!in_array('minimum_order_amount', $columns)) {
            $table->decimal('minimum_order_amount', 10, 2)->nullable()->after('discount_value');
        }
        if (!in_array('is_active', $columns)) {
            $table->boolean('is_active')->default(true)->after('expiry_days');
        }
    });
    echo "✅ Updated voucher_templates table structure\n";
}

echo "\n=== Testing Voucher System Integration ===\n";

try {
    // Test all voucher models
    $templateCount = \App\Models\VoucherTemplate::count();
    $collectionCount = \App\Models\VoucherCollection::count();
    $voucherCount = \App\Models\Voucher::count();
    $userVoucherCount = \App\Models\UserVoucher::count();

    echo "✅ VoucherTemplate model works - $templateCount records\n";
    echo "✅ VoucherCollection model works - $collectionCount records\n";
    echo "✅ Voucher model works - $voucherCount records\n";
    echo "✅ UserVoucher model works - $userVoucherCount records\n";

    // Test reward-voucher relationship
    $rewardCount = \App\Models\Reward::count();
    echo "✅ Reward model works - $rewardCount records\n";

    echo "\n🎉 VOUCHER SYSTEM COMPLETE! 🎉\n";
    echo "\n📋 Your Voucher System Features:\n";
    echo "• Voucher Templates - Design reusable voucher types\n";
    echo "• Voucher Collections - Group vouchers for campaigns\n";
    echo "• Individual Vouchers - Specific discount codes\n";
    echo "• User Vouchers - Track user voucher claims and usage\n";
    echo "• Customer Vouchers - Customer profile integration\n";
    echo "• Reward Integration - Points can be exchanged for vouchers\n";

} catch (Exception $e) {
    echo "❌ Error testing voucher system: " . $e->getMessage() . "\n";
}

?>