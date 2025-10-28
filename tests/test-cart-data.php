<?php
/**
 * Test script to verify cart data structure and promotion tracking
 * Run this with: php test-cart-data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserCart;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Order;

echo "=== CART DATA STRUCTURE TEST ===\n\n";

// Test 1: Check UserCart for promotion fields
echo "1. Checking UserCart table structure:\n";
$sampleCart = UserCart::with('menuItem', 'promotion')->first();
if ($sampleCart) {
    echo "   ✓ Sample cart item found (ID: {$sampleCart->id})\n";
    echo "   - menu_item_id: {$sampleCart->menu_item_id}\n";
    echo "   - promotion_id: " . ($sampleCart->promotion_id ?? 'NULL') . "\n";
    echo "   - promotion_group_id: " . ($sampleCart->promotion_group_id ?? 'NULL') . "\n";
    echo "   - is_free_item: " . ($sampleCart->is_free_item ? 'YES' : 'NO') . "\n";
    echo "   - Promotion name: " . ($sampleCart->promotion->name ?? 'No promotion') . "\n";
} else {
    echo "   ⚠ No cart items found in database\n";
}

echo "\n2. Checking OrderItem table structure:\n";
$sampleOrderItem = OrderItem::with('menuItem', 'promotion')->first();
if ($sampleOrderItem) {
    echo "   ✓ Sample order item found (ID: {$sampleOrderItem->id})\n";
    echo "   - order_id: {$sampleOrderItem->order_id}\n";
    echo "   - menu_item_id: {$sampleOrderItem->menu_item_id}\n";
    echo "   - promotion_id: " . ($sampleOrderItem->promotion_id ?? 'NULL') . "\n";
    echo "   - combo_group_id: " . ($sampleOrderItem->combo_group_id ?? 'NULL') . "\n";
    echo "   - is_combo_item: " . ($sampleOrderItem->is_combo_item ? 'YES' : 'NO') . "\n";
    echo "   - Promotion name: " . ($sampleOrderItem->promotion->name ?? 'No promotion') . "\n";
} else {
    echo "   ⚠ No order items found in database\n";
}

// Test 2: Check latest order with promotion items
echo "\n3. Checking latest order with promotion items:\n";
$latestOrderWithPromo = Order::with(['items.menuItem', 'items.promotion'])
    ->whereHas('items', function($query) {
        $query->whereNotNull('promotion_id');
    })
    ->orderBy('created_at', 'desc')
    ->first();

if ($latestOrderWithPromo) {
    echo "   ✓ Found order #{$latestOrderWithPromo->id} (Code: {$latestOrderWithPromo->confirmation_code})\n";
    echo "   - Created at: {$latestOrderWithPromo->created_at}\n";
    echo "   - Total items: {$latestOrderWithPromo->items->count()}\n";

    foreach ($latestOrderWithPromo->items as $item) {
        echo "\n   Item: {$item->menuItem->name}\n";
        echo "     - promotion_id: " . ($item->promotion_id ?? 'NULL') . "\n";
        echo "     - combo_group_id: " . ($item->combo_group_id ?? 'NULL') . "\n";
        echo "     - is_combo_item: " . ($item->is_combo_item ? 'YES' : 'NO') . "\n";
        echo "     - Promotion: " . ($item->promotion->name ?? 'None') . "\n";
    }
} else {
    echo "   ⚠ No orders with promotion items found\n";
}

// Test 3: Check cart items with promotion groups
echo "\n\n4. Checking cart items with promotion_group_id:\n";
$cartWithPromoGroups = UserCart::with('menuItem', 'promotion')
    ->whereNotNull('promotion_group_id')
    ->get();

if ($cartWithPromoGroups->count() > 0) {
    echo "   ✓ Found {$cartWithPromoGroups->count()} cart items with promotion groups\n";

    $groups = $cartWithPromoGroups->groupBy('promotion_group_id');
    foreach ($groups as $groupId => $items) {
        echo "\n   Group: {$groupId}\n";
        echo "   - Items in group: {$items->count()}\n";
        foreach ($items as $item) {
            echo "     • {$item->menuItem->name} (Qty: {$item->quantity})\n";
            echo "       Promotion: " . ($item->promotion->name ?? 'None') . "\n";
        }
    }
} else {
    echo "   ⚠ No cart items with promotion_group_id found\n";
}

echo "\n\n=== TEST COMPLETE ===\n";
