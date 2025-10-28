<?php
/**
 * Test script to check available bundle/combo promotions
 * Run this with: php test-bundle-promotion.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Promotion;
use App\Models\MenuItem;

echo "=== BUNDLE/COMBO PROMOTIONS TEST ===\n\n";

// Check for bundle promotions
$bundlePromotions = Promotion::whereIn('promotion_type', ['bundle', 'combo_deal', 'buy_x_free_y'])
    ->where('is_active', true)
    ->get();

if ($bundlePromotions->isEmpty()) {
    echo "⚠ No active bundle/combo promotions found!\n";
    echo "\nCreating a test bundle promotion...\n";

    // Get some menu items to use
    $menuItems = MenuItem::where('is_available', true)->take(3)->get();

    if ($menuItems->count() < 3) {
        echo "✗ Not enough menu items available to create bundle\n";
        exit(1);
    }

    echo "\nAvailable menu items:\n";
    foreach ($menuItems as $item) {
        echo "  - ID {$item->id}: {$item->name} (RM {$item->price})\n";
    }

} else {
    echo "✓ Found {$bundlePromotions->count()} active bundle/combo promotions:\n\n";

    foreach ($bundlePromotions as $promo) {
        echo "Promotion: {$promo->name}\n";
        echo "  - Type: {$promo->promotion_type}\n";
        echo "  - ID: {$promo->id}\n";

        $config = is_string($promo->promo_config)
            ? json_decode($promo->promo_config, true)
            : $promo->promo_config;

        if ($config) {
            echo "  - Configuration:\n";

            if (isset($config['items'])) {
                echo "    Items:\n";
                foreach ($config['items'] as $item) {
                    $menuItem = MenuItem::find($item['item_id']);
                    if ($menuItem) {
                        echo "      • {$menuItem->name} (ID: {$item['item_id']})\n";
                        echo "        Quantity: {$item['quantity']}\n";
                        echo "        Price: RM " . ($item['price'] ?? $menuItem->price) . "\n";
                        if (isset($item['is_free']) && $item['is_free']) {
                            echo "        ⭐ FREE ITEM\n";
                        }
                    }
                }
            }

            if (isset($config['bundle_price'])) {
                echo "    Bundle Price: RM {$config['bundle_price']}\n";
            }

            if (isset($config['original_price'])) {
                echo "    Original Price: RM {$config['original_price']}\n";
            }
        }

        echo "\n";
    }

    echo "\n=== INSTRUCTIONS TO TEST ===\n";
    echo "1. Go to customer/promotions page\n";
    echo "2. Click 'Add to Cart' on one of the bundles above\n";
    echo "3. Go to cart and verify items are grouped\n";
    echo "4. Proceed to checkout and complete payment\n";
    echo "5. View order details to verify promotion grouping\n";
}

echo "\n=== TEST COMPLETE ===\n";
