<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the unified menu page with food, drinks, and set meals.
     */
    public function index()
    {
        // Get all categories with their available menu items
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('availability', true)->orderBy('name');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        // Get active item discount promotions
        $itemDiscounts = Promotion::where('promotion_type', Promotion::TYPE_ITEM_DISCOUNT)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        // Build item discount map: item_id => discount info
        $itemDiscountMap = [];
        foreach ($itemDiscounts as $promotion) {
            $itemIds = $promotion->getDiscountedItemIds();
            if ($itemIds) {
                foreach ($itemIds as $itemId) {
                    // Only store first/best discount per item
                    if (!isset($itemDiscountMap[$itemId])) {
                        $itemDiscountMap[$itemId] = [
                            'promotion_id' => $promotion->id,
                            'promotion_name' => $promotion->name,
                            'discount_type' => $promotion->discount_type,
                            'discount_value' => $promotion->discount_value,
                        ];
                    }
                }
            }
        }

        // Attach discount info to menu items in categories
        foreach ($categories as $category) {
            foreach ($category->menuItems as $item) {
                if (isset($itemDiscountMap[$item->id])) {
                    $discount = $itemDiscountMap[$item->id];

                    // Calculate discounted price
                    if ($discount['discount_type'] === 'percentage') {
                        $discountAmount = ($item->price * $discount['discount_value']) / 100;
                        $discountedPrice = $item->price - $discountAmount;
                    } else {
                        $discountAmount = $discount['discount_value'];
                        $discountedPrice = max(0, $item->price - $discountAmount);
                    }

                    $item->has_discount = true;
                    $item->discount_info = [
                        'promotion_id' => $discount['promotion_id'],
                        'promotion_name' => $discount['promotion_name'],
                        'discount_type' => $discount['discount_type'],
                        'discount_value' => $discount['discount_value'],
                        'discount_amount' => round($discountAmount, 2),
                        'discounted_price' => round($discountedPrice, 2),
                        'original_price' => $item->price,
                    ];
                } else {
                    $item->has_discount = false;
                    $item->discount_info = null;
                }
            }
        }

        return view('customer.menu.index', compact('categories'));
    }

    /**
     * Get menu data for AJAX requests
     */
    public function getMenuData(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = MenuItem::where('availability', true)->with('category');

        if ($type !== 'all') {
            $query->whereHas('category', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $menuItems = $query->orderBy('name')->get();

        // Get active item discount promotions
        $itemDiscounts = Promotion::where('promotion_type', Promotion::TYPE_ITEM_DISCOUNT)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        // Build item discount map: item_id => discount info
        $itemDiscountMap = [];
        foreach ($itemDiscounts as $promotion) {
            $itemIds = $promotion->getDiscountedItemIds();
            if ($itemIds) {
                foreach ($itemIds as $itemId) {
                    // Only store first/best discount per item
                    if (!isset($itemDiscountMap[$itemId])) {
                        $itemDiscountMap[$itemId] = [
                            'promotion_id' => $promotion->id,
                            'promotion_name' => $promotion->name,
                            'discount_type' => $promotion->discount_type,
                            'discount_value' => $promotion->discount_value,
                        ];
                    }
                }
            }
        }

        // Attach discount info to menu items
        $menuItems = $menuItems->map(function($item) use ($itemDiscountMap) {
            if (isset($itemDiscountMap[$item->id])) {
                $discount = $itemDiscountMap[$item->id];

                // Calculate discounted price
                if ($discount['discount_type'] === 'percentage') {
                    $discountAmount = ($item->price * $discount['discount_value']) / 100;
                    $discountedPrice = $item->price - $discountAmount;
                } else {
                    $discountAmount = $discount['discount_value'];
                    $discountedPrice = max(0, $item->price - $discountAmount);
                }

                $item->has_discount = true;
                $item->discount_info = [
                    'promotion_id' => $discount['promotion_id'],
                    'promotion_name' => $discount['promotion_name'],
                    'discount_type' => $discount['discount_type'],
                    'discount_value' => $discount['discount_value'],
                    'discount_amount' => round($discountAmount, 2),
                    'discounted_price' => round($discountedPrice, 2),
                    'original_price' => $item->price,
                ];
            } else {
                $item->has_discount = false;
                $item->discount_info = null;
            }

            return $item;
        });

        return response()->json($menuItems);
    }
}
