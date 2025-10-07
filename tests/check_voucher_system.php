<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== Checking Voucher System Setup ===\n";

// Check all voucher-related tables
$voucherTables = [
    'voucher_templates',
    'voucher_collections',
    'vouchers',
    'user_vouchers',
    'customer_vouchers'
];

echo "📋 Voucher Tables Status:\n";
foreach ($voucherTables as $table) {
    $exists = Schema::hasTable($table);
    echo sprintf("%-25s %s\n", $table, $exists ? '✅ EXISTS' : '❌ MISSING');

    if ($exists) {
        $columns = Schema::getColumnListing($table);
        echo "   Columns: " . implode(', ', $columns) . "\n";
    }
}

echo "\n📋 Voucher Models Status:\n";
$voucherModels = [
    'App\Models\VoucherTemplate',
    'App\Models\VoucherCollection',
    'App\Models\Voucher',
    'App\Models\UserVoucher'
];

foreach ($voucherModels as $model) {
    $modelName = class_basename($model);
    if (class_exists($model)) {
        echo "✅ $modelName model exists\n";
        try {
            $count = $model::count();
            echo "   Records: $count\n";
        } catch (Exception $e) {
            echo "   Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ $modelName model missing\n";
    }
}

echo "\n📋 Checking Reward-Voucher Integration:\n";
// Check if rewards table has voucher_template_id foreign key
if (Schema::hasTable('rewards')) {
    $rewardColumns = Schema::getColumnListing('rewards');
    if (in_array('voucher_template_id', $rewardColumns)) {
        echo "✅ rewards.voucher_template_id exists\n";
    } else {
        echo "❌ rewards.voucher_template_id missing\n";
    }
}

?>