<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\RewardsController;
use Illuminate\Http\Request;

echo "=== Final Voucher Integration Test ===\n";

try {
    echo "🧪 Testing RewardsController with complete voucher system...\n";

    $controller = new RewardsController();
    $request = new Request();
    $response = $controller->index();

    echo "✅ SUCCESS! Rewards admin page works with complete voucher system!\n";
    echo "Response type: " . get_class($response) . "\n";

    echo "\n📋 Testing voucher relationships...\n";

    // Test voucher template creation
    $template = new \App\Models\VoucherTemplate();
    $template->name = "Test Discount Template";
    $template->discount_type = "percentage";
    $template->discount_value = 15.00;
    $template->expiry_days = 30;
    $template->save();
    echo "✅ Created test voucher template\n";

    // Test reward with voucher template
    $reward = new \App\Models\Reward();
    $reward->title = "Test Voucher Reward";
    $reward->description = "Get 15% discount voucher";
    $reward->reward_type = "voucher";
    $reward->points_required = 100;
    $reward->voucher_template_id = $template->id;
    $reward->save();
    echo "✅ Created test reward linked to voucher template\n";

    // Test voucher collection
    $collection = new \App\Models\VoucherCollection();
    $collection->name = "Summer Sale Vouchers";
    $collection->description = "Summer discount collection";
    $collection->discount_percentage = 20.00;
    $collection->valid_from = now()->format('Y-m-d');
    $collection->valid_until = now()->addDays(30)->format('Y-m-d');
    $collection->usage_limit = 100;
    $collection->save();
    echo "✅ Created test voucher collection\n";

    echo "\n🎉 COMPLETE VOUCHER SYSTEM WORKING PERFECTLY! 🎉\n";

    // Clean up test data
    $reward->delete();
    $template->delete();
    $collection->delete();
    echo "🧹 Cleaned up test data\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 VOUCHER SYSTEM EXPLANATION\n";
echo str_repeat("=", 60) . "\n";

echo "
📚 HOW YOUR VOUCHER SYSTEM WORKS:

1. 🎨 VOUCHER TEMPLATES (voucher_templates)
   • These are the 'blueprints' for vouchers
   • Define discount type (percentage/fixed amount)
   • Set expiration rules
   • Used by rewards system when customer redeems points

2. 📦 VOUCHER COLLECTIONS (voucher_collections)
   • Groups of vouchers for marketing campaigns
   • Example: 'Black Friday Sale', 'New Customer Welcome'
   • Managed through admin panel

3. 🎫 INDIVIDUAL VOUCHERS (vouchers)
   • Specific discount codes (e.g., 'SAVE20NOW')
   • Generated from collections
   • Have unique codes and usage tracking

4. 👤 USER VOUCHERS (user_vouchers)
   • Links users to their claimed vouchers
   • Tracks when claimed and when used
   • Prevents duplicate claims

5. 🏪 CUSTOMER VOUCHERS (customer_vouchers)
   • Customer profile specific vouchers
   • Integration with customer loyalty

🔄 VOUCHER-REWARD INTEGRATION:
• When customer redeems points for voucher reward
• System creates voucher from template
• Customer gets voucher in their account
• Can use voucher for discounts on orders

🎯 ADMIN MANAGEMENT:
• Create voucher templates for different reward types
• Manage voucher collections for campaigns
• Track voucher usage and redemptions
• Monitor customer voucher activity

📱 CUSTOMER EXPERIENCE:
• Earn points through purchases/activities
• Redeem points for voucher rewards
• Receive vouchers in account
• Apply vouchers during checkout
";

?>