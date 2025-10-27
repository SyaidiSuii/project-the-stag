<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCart;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\CustomerVoucher;
use App\Models\Order;
use App\Services\Promotions\PromotionService;

class CartController extends Controller
{
    // Get cart items for both logged in and guest users
    public function index()
    {
        // Guest users - return session cart
        if (!Auth::check()) {
            return $this->getGuestCart();
        }

        // Logged in users - return database cart
        $userId = Auth::id();
        $allCartItems = UserCart::with(['menuItem', 'promotion'])
            ->where('user_id', $userId)
            ->get();

        // Separate regular items and promotion groups
        $regularItems = [];
        $promotionGroupsData = [];

        foreach ($allCartItems as $item) {
            // Get availability status
            $availabilityStatus = $item->getAvailabilityStatus();
            $isAvailable = $availabilityStatus['available'];

            // Update tracking timestamps
            $item->updateLastChecked();

            // Mark as unavailable if needed
            if (!$isAvailable && !$item->unavailable_since) {
                $item->markAsUnavailable();
            } elseif ($isAvailable && $item->unavailable_since) {
                $item->markAsAvailable();
            }

            $menuItem = $item->menuItem;

            // IMPORTANT: Validate item_discount promotion if cart item has promotion_id
            if ($item->promotion_id && !$item->promotion_group_id) {
                // This is an item_discount (not bundle/combo)
                $promotion = Promotion::find($item->promotion_id);

                // Check if promotion is still valid
                $isPromotionValid = $promotion
                    && $promotion->promotion_type === Promotion::TYPE_ITEM_DISCOUNT
                    && $promotion->is_active
                    && $promotion->start_date <= now()
                    && $promotion->end_date >= now();

                if (!$isPromotionValid) {
                    // Promotion expired/disabled - revert to original price
                    \Log::warning('Item discount promotion invalid, reverting to original price', [
                        'cart_item_id' => $item->id,
                        'menu_item_id' => $item->menu_item_id,
                        'old_promotion_id' => $item->promotion_id,
                        'old_unit_price' => $item->unit_price,
                        'new_unit_price' => $menuItem ? $menuItem->price : $item->unit_price,
                    ]);

                    // Update cart item to original price
                    $item->unit_price = $menuItem ? $menuItem->price : $item->unit_price;
                    $item->promotion_id = null;
                    $item->save();
                }
            }

            // Build item data
            $itemData = [
                'id' => $item->menu_item_id,
                'cart_id' => $item->id,
                'name' => $menuItem ? $menuItem->name : 'Produk Tidak Tersedia',
                'price' => 'RM ' . number_format($item->unit_price, 2),
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'notes' => $item->special_notes,
                'image' => $menuItem ? $menuItem->image_url : null,
                'total' => $item->total_price,
                'is_available' => $isAvailable,
                'availability_reason' => $availabilityStatus['reason'],
                'availability_message' => $availabilityStatus['message'],
                'unavailable_since' => $item->unavailable_since ? $item->unavailable_since->diffForHumans() : null,
                'is_free_item' => $item->is_free_item,
                'original_price' => $menuItem ? $menuItem->price : 0,
                // IMPORTANT: Include promotion tracking fields for checkout
                'promotion_id' => $item->promotion_id,
                'promotion_group_id' => $item->promotion_group_id,
            ];

            // Group items by promotion_group_id
            if ($item->promotion_group_id) {
                $groupId = $item->promotion_group_id;

                if (!isset($promotionGroupsData[$groupId])) {
                    $promotion = $item->promotion;
                    $promotionGroupsData[$groupId] = [
                        'group_id' => $groupId,
                        'promotion' => [
                            'id' => $promotion ? $promotion->id : null,
                            'name' => $promotion ? $promotion->name : 'Promotion',
                            'type' => $promotion ? $promotion->promotion_type : null,
                            'type_label' => $promotion ? $promotion->type_label : 'Promotion',
                        ],
                        'items' => [],
                        'total_price' => 0,
                        'total_quantity' => 0,
                        'original_total_price' => 0,
                        'savings' => 0,
                        'is_locked' => true, // Promotion items are always locked
                    ];
                }

                $promotionGroupsData[$groupId]['items'][] = $itemData;
                $promotionGroupsData[$groupId]['total_price'] += $item->total_price;
                $promotionGroupsData[$groupId]['total_quantity'] += $item->quantity;

                // Calculate savings
                if ($menuItem) {
                    $originalItemTotal = $menuItem->price * $item->quantity;
                    $promotionGroupsData[$groupId]['original_total_price'] += $originalItemTotal;
                    $promotionGroupsData[$groupId]['savings'] += ($originalItemTotal - $item->total_price);
                }
            } else {
                // Regular items (not part of promotion)
                $regularItems[] = $itemData;
            }
        }

        // Convert promotion groups to array
        $promotionGroups = array_values($promotionGroupsData);

        // IMPORTANT: Auto-cleanup - if cart has bundle/combo items + promo code, remove promo code
        // This ensures consistency even on page refresh
        $hasBundleItems = count($promotionGroups) > 0;
        if ($hasBundleItems) {
            $hasPromoCode = UserCart::where('user_id', $userId)
                ->whereNotNull('applied_promo_code')
                ->exists();

            if ($hasPromoCode) {
                // Silently remove promo code to prevent double discount
                UserCart::where('user_id', $userId)
                    ->whereNotNull('applied_promo_code')
                    ->update([
                        'applied_promo_code' => null,
                        'promo_discount_amount' => 0
                    ]);

                \Log::info('Auto-cleanup: Promo code removed on cart fetch (has bundle items)', [
                    'user_id' => $userId,
                    'promotion_groups_count' => count($promotionGroups)
                ]);
            }
        }

        // Get promo code info
        $promoCode = UserCart::where('user_id', $userId)
            ->whereNotNull('applied_promo_code')
            ->value('applied_promo_code');
        $promoDiscount = UserCart::where('user_id', $userId)
            ->sum('promo_discount_amount');
        $availableTotal = UserCart::getAvailableCartTotal($userId);

        // Flatten all items for backward compatibility
        $allItems = $regularItems;
        foreach ($promotionGroups as $group) {
            $allItems = array_merge($allItems, $group['items']);
        }

        return response()->json([
            'success' => true,
            'cart' => $allItems, // For backward compatibility: flatten all items
            'regular_items' => $regularItems,
            'promotion_groups' => $promotionGroups,
            'total' => UserCart::getCartTotal($userId),
            'available_total' => $availableTotal,
            'count' => UserCart::getCartCount($userId),
            'unavailable_count' => UserCart::getUnavailableCount($userId),
            'applied_promo_code' => $promoCode,
            'promo_discount' => $promoDiscount,
            'final_total' => max(0, $availableTotal - $promoDiscount),
        ]);
    }

    // Add item to cart (for both guest and logged in users)
    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'special_notes' => 'nullable|string'
        ]);

        $menuItem = MenuItem::find($validated['menu_item_id']);

        // Check for active item discount promotion
        $itemDiscount = $this->getActiveItemDiscount($validated['menu_item_id']);

        \Log::info('CartController::addItem - Checking item discount', [
            'menu_item_id' => $validated['menu_item_id'],
            'menu_item_name' => $menuItem->name,
            'has_discount' => $itemDiscount ? 'YES' : 'NO',
            'discount_data' => $itemDiscount,
        ]);

        // Determine price and promotion tracking
        $unitPrice = $menuItem->price;  // Default: original price
        $promotionId = null;

        if ($itemDiscount) {
            // Apply item discount
            if ($itemDiscount['discount_type'] === 'percentage') {
                $discountAmount = ($menuItem->price * $itemDiscount['discount_value']) / 100;
                $unitPrice = $menuItem->price - $discountAmount;
            } else {
                $unitPrice = max(0, $menuItem->price - $itemDiscount['discount_value']);
            }
            $promotionId = $itemDiscount['promotion_id'];

            \Log::info('Item discount auto-applied', [
                'menu_item_id' => $validated['menu_item_id'],
                'original_price' => $menuItem->price,
                'discounted_price' => $unitPrice,
                'promotion_id' => $promotionId,
            ]);
        } else {
            \Log::info('No item discount found for this item', [
                'menu_item_id' => $validated['menu_item_id'],
            ]);
        }

        // Guest users - store in session
        if (!Auth::check()) {
            $cart = session('guest_cart', []);

            // Check if item already exists in cart (regular items only, not bundle items)
            $existingIndex = collect($cart)->search(function ($item) use ($validated) {
                return $item['menu_item_id'] == $validated['menu_item_id']
                    && !isset($item['promotion_id']);  // Only match non-promotion items
            });

            if ($existingIndex !== false) {
                // Update existing item quantity
                $cart[$existingIndex]['quantity'] += $validated['quantity'];
            } else {
                // Add new item to cart
                $cart[] = [
                    'menu_item_id' => $validated['menu_item_id'],
                    'quantity' => $validated['quantity'],
                    'unit_price' => $unitPrice,  // Use discounted price if applicable
                    'special_notes' => $validated['special_notes'] ?? null,
                    'promotion_id' => $promotionId,  // Track item discount promotion
                ];
            }

            session(['guest_cart' => $cart]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'cart_count' => collect($cart)->sum('quantity')
            ]);
        }

        // Logged in users - store in database
        // IMPORTANT: Only match regular items (without promotion_id)
        // This ensures bundle items and regular items are treated separately
        $cartItem = UserCart::withTrashed()
            ->where('user_id', Auth::id())
            ->where('menu_item_id', $validated['menu_item_id'])
            ->whereNull('promotion_id')  // Only match non-promotion items
            ->first();

        if ($cartItem) {
            if ($cartItem->trashed()) {
                // Restore soft deleted item and update quantity
                $cartItem->restore();
                $cartItem->quantity = $validated['quantity'];
            } else {
                // Update quantity if item exists and is not soft deleted
                $cartItem->quantity += $validated['quantity'];
            }

            // IMPORTANT: Update price and promotion_id in case discount changed
            $cartItem->unit_price = $unitPrice;
            $cartItem->promotion_id = $promotionId;
            $cartItem->save();

            \Log::info('Existing cart item updated with new price', [
                'cart_item_id' => $cartItem->id,
                'menu_item_id' => $validated['menu_item_id'],
                'new_quantity' => $cartItem->quantity,
                'unit_price' => $unitPrice,
                'promotion_id' => $promotionId,
            ]);
        } else {
            // Create new cart item
            UserCart::create([
                'user_id' => Auth::id(),
                'menu_item_id' => $validated['menu_item_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,  // Use discounted price if applicable
                'special_notes' => $validated['special_notes'] ?? null,
                'promotion_id' => $promotionId,  // Track item discount promotion
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => UserCart::getCartCount(Auth::id())
        ]);
    }

    // Update cart item quantity (for both guest and logged in users)
    public function updateItem(Request $request, $menuItemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        // Guest users - update session cart
        if (!Auth::check()) {
            $cart = session('guest_cart', []);

            $existingIndex = collect($cart)->search(function ($item) use ($menuItemId) {
                return $item['menu_item_id'] == $menuItemId;
            });

            if ($existingIndex === false) {
                return response()->json(['error' => 'Item not found in cart'], 404);
            }

            // Check if item is part of a promotion (LOCK CHECK for guest)
            $cartItem = $cart[$existingIndex];
            if (isset($cartItem['promotion_id']) && $cartItem['promotion_id']) {
                // Load promotion info for better error message
                $promotion = Promotion::find($cartItem['promotion_id']);
                $promotionName = $promotion ? $promotion->name : 'this promotion';

                return response()->json([
                    'success' => false,
                    'message' => "This item is part of '{$promotionName}' and cannot be modified individually. Remove the entire promotion to make changes.",
                    'is_locked' => true,
                    'promotion_name' => $promotionName
                ], 400);
            }

            if ($validated['quantity'] == 0) {
                // Remove item from cart
                unset($cart[$existingIndex]);
                $cart = array_values($cart); // Re-index array
                $message = 'Item removed from cart';
            } else {
                // Update quantity
                $cart[$existingIndex]['quantity'] = $validated['quantity'];
                $message = 'Cart updated';
            }

            session(['guest_cart' => $cart]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => collect($cart)->sum('quantity')
            ]);
        }

        // Logged in users - update database
        $cartItem = UserCart::where('user_id', Auth::id())
            ->where('menu_item_id', $menuItemId)
            ->first();

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found in cart'], 404);
        }

        // LOCK CHECK: Prevent modification of promotion items
        if ($cartItem->promotion_id) {
            // Load promotion for better error message
            $promotion = $cartItem->promotion;
            $promotionName = $promotion ? $promotion->name : 'this promotion';

            return response()->json([
                'success' => false,
                'message' => "This item is part of '{$promotionName}' and cannot be modified individually. Remove the entire promotion to make changes.",
                'is_locked' => true,
                'promotion_name' => $promotionName,
                'promotion_group_id' => $cartItem->promotion_group_id
            ], 400);
        }

        if ($validated['quantity'] == 0) {
            $cartItem->delete();
            $message = 'Item removed from cart';
        } else {
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();
            $message = 'Cart updated';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => UserCart::getCartCount(Auth::id())
        ]);
    }

    // Remove item from cart (for both guest and logged in users)
    public function removeItem($menuItemId)
    {
        // Guest users - remove from session
        if (!Auth::check()) {
            $cart = session('guest_cart', []);

            $existingIndex = collect($cart)->search(function ($item) use ($menuItemId) {
                return $item['menu_item_id'] == $menuItemId;
            });

            if ($existingIndex === false) {
                return response()->json(['error' => 'Item not found in cart'], 404);
            }

            unset($cart[$existingIndex]);
            $cart = array_values($cart); // Re-index array
            session(['guest_cart' => $cart]);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => collect($cart)->sum('quantity')
            ]);
        }

        // Logged in users - remove from database
        $deleted = UserCart::where('user_id', Auth::id())
            ->where('menu_item_id', $menuItemId)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Item not found in cart'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => UserCart::getCartCount(Auth::id())
        ]);
    }

    // Clear entire cart (for both guest and logged in users)
    public function clearCart()
    {
        // Guest users - clear session
        if (!Auth::check()) {
            session()->forget('guest_cart');

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared'
            ]);
        }

        // Logged in users - clear database
        UserCart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    // Merge guest cart with database cart (called on login)
    public function mergeCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Check if there's a session cart to merge
        $sessionCart = session('guest_cart', []);

        // Also accept cart_items from request (for backward compatibility)
        $validated = $request->validate([
            'cart_items' => 'sometimes|array',
            'cart_items.*.id' => 'required_with:cart_items|exists:menu_items,id',
            'cart_items.*.quantity' => 'required_with:cart_items|integer|min:1',
            'cart_items.*.notes' => 'nullable|string'
        ]);

        // Merge session cart first (priority)
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                $cartItem = UserCart::withTrashed()
                    ->where('user_id', Auth::id())
                    ->where('menu_item_id', $item['menu_item_id'])
                    ->first();

                if ($cartItem) {
                    if ($cartItem->trashed()) {
                        $cartItem->restore();
                        $cartItem->quantity = $item['quantity'];
                    } else {
                        $cartItem->quantity += $item['quantity'];
                    }
                    $cartItem->save();
                } else {
                    UserCart::create([
                        'user_id' => Auth::id(),
                        'menu_item_id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $menuItem->price,
                        'special_notes' => $item['special_notes'] ?? null
                    ]);
                }
            }

            // Clear session cart after merge
            session()->forget('guest_cart');
        }

        // Also merge from request cart_items (for localStorage backward compatibility)
        if (isset($validated['cart_items'])) {
            foreach ($validated['cart_items'] as $item) {
                $menuItem = MenuItem::find($item['id']);

                $cartItem = UserCart::withTrashed()
                    ->where('user_id', Auth::id())
                    ->where('menu_item_id', $item['id'])
                    ->first();

                if ($cartItem) {
                    if ($cartItem->trashed()) {
                        $cartItem->restore();
                        $cartItem->quantity = $item['quantity'];
                    } else {
                        $cartItem->quantity += $item['quantity'];
                    }
                    $cartItem->save();
                } else {
                    UserCart::create([
                        'user_id' => Auth::id(),
                        'menu_item_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $menuItem->price,
                        'special_notes' => $item['notes'] ?? null
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart merged successfully',
            'cart_count' => UserCart::getCartCount(Auth::id())
        ]);
    }

    // Remove all unavailable items from cart (bulk delete)
    public function removeUnavailableItems()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $userId = Auth::id();

        // Get all cart items
        $cartItems = UserCart::with('menuItem')
            ->where('user_id', $userId)
            ->get();

        $removedCount = 0;

        foreach ($cartItems as $item) {
            if (!$item->isMenuItemAvailable()) {
                $item->delete();
                $removedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$removedCount item(s) yang tidak tersedia telah dikeluarkan",
            'removed_count' => $removedCount,
            'cart_count' => UserCart::getCartCount($userId)
        ]);
    }

    // Helper: Get guest cart from session
    private function getGuestCart()
    {
        $sessionCart = session('guest_cart', []);

        // Separate regular items and promotion groups
        $regularItems = [];
        $promotionGroupsData = [];

        foreach ($sessionCart as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            $isAvailable = $menuItem && $menuItem->availability;

            $itemData = [
                'id' => $item['menu_item_id'],
                'cart_id' => null, // Guest carts don't have DB IDs
                'name' => $menuItem ? $menuItem->name : 'Produk Tidak Tersedia',
                'price' => 'RM ' . number_format($item['unit_price'], 2),
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'notes' => $item['special_notes'] ?? null,
                'image' => $menuItem ? $menuItem->image_url : null,
                'total' => $item['unit_price'] * $item['quantity'],
                'is_available' => $isAvailable,
                'availability_reason' => !$isAvailable ? 'Item tidak tersedia' : null,
                'availability_message' => !$isAvailable ? 'Item ini sedang tidak tersedia' : null,
                'unavailable_since' => null,
                'is_free_item' => $item['is_free_item'] ?? false,
                'original_price' => $menuItem ? $menuItem->price : 0,
                // IMPORTANT: Include promotion tracking fields for checkout
                'promotion_id' => $item['promotion_id'] ?? null,
                'promotion_group_id' => $item['promotion_group_id'] ?? null,
            ];

            // Group by promotion_group_id
            if (isset($item['promotion_group_id']) && $item['promotion_group_id']) {
                $groupId = $item['promotion_group_id'];

                if (!isset($promotionGroupsData[$groupId])) {
                    $promotion = isset($item['promotion_id']) ? Promotion::find($item['promotion_id']) : null;
                    $promotionGroupsData[$groupId] = [
                        'group_id' => $groupId,
                        'promotion' => [
                            'id' => $promotion ? $promotion->id : null,
                            'name' => $promotion ? $promotion->name : 'Promotion',
                            'type' => $promotion ? $promotion->promotion_type : null,
                            'type_label' => $promotion ? $promotion->type_label : 'Promotion',
                        ],
                        'items' => [],
                        'total_price' => 0,
                        'total_quantity' => 0,
                        'original_total_price' => 0,
                        'savings' => 0,
                        'is_locked' => true,
                    ];
                }

                $promotionGroupsData[$groupId]['items'][] = $itemData;
                $promotionGroupsData[$groupId]['total_price'] += $itemData['total'];
                $promotionGroupsData[$groupId]['total_quantity'] += $item['quantity'];

                if ($menuItem) {
                    $originalItemTotal = $menuItem->price * $item['quantity'];
                    $promotionGroupsData[$groupId]['original_total_price'] += $originalItemTotal;
                    $promotionGroupsData[$groupId]['savings'] += ($originalItemTotal - $itemData['total']);
                }
            } else {
                $regularItems[] = $itemData;
            }
        }

        $promotionGroups = array_values($promotionGroupsData);

        // Flatten all items for backward compatibility
        $allItems = $regularItems;
        foreach ($promotionGroups as $group) {
            $allItems = array_merge($allItems, $group['items']);
        }

        $total = collect($allItems)->sum('total');
        $availableTotal = collect($allItems)->filter(fn($item) => $item['is_available'])->sum('total');
        $count = collect($allItems)->sum('quantity');
        $unavailableCount = collect($allItems)->filter(fn($item) => !$item['is_available'])->count();

        // Get applied promo code from session if exists
        $appliedPromoCode = session('guest_promo_code');
        $promoDiscount = session('guest_promo_discount', 0);

        return response()->json([
            'success' => true,
            'cart' => $allItems,
            'regular_items' => $regularItems,
            'promotion_groups' => $promotionGroups,
            'total' => $total,
            'available_total' => $availableTotal,
            'count' => $count,
            'unavailable_count' => $unavailableCount,
            'applied_promo_code' => $appliedPromoCode,
            'promo_discount' => $promoDiscount,
            'final_total' => max(0, $availableTotal - $promoDiscount),
        ]);
    }

    /**
     * Apply promo code to cart
     */
    public function applyPromoCode(Request $request)
    {
        $validated = $request->validate([
            'promo_code' => 'required|string|max:50'
        ]);

        $promoCode = strtoupper(trim($validated['promo_code']));
        $userId = Auth::id();
        $promotionService = app(PromotionService::class);

        // Get cart items in format PromotionService expects
        if (Auth::check()) {
            $cartItems = UserCart::where('user_id', $userId)
                ->with('menuItem')
                ->get()
                ->filter(fn($item) => $item->isMenuItemAvailable())
                ->mapWithKeys(function($item) {
                    return [$item->menu_item_id => [
                        'item' => $item->menuItem,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price
                    ]];
                })->toArray();
        } else {
            // Guest cart from session
            $sessionCart = session('guest_cart', []);
            $cartItems = [];
            foreach ($sessionCart as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem && $menuItem->availability) {
                    $cartItems[$item['menu_item_id']] = [
                        'item' => $menuItem,
                        'quantity' => $item['quantity'],
                        'price' => $item['unit_price']
                    ];
                }
            }
        }

        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart kosong atau tiada item tersedia'
            ], 400);
        }

        // IMPORTANT: Prevent double discount - check if cart has bundle/combo/buy1free1/item_discount items
        if (Auth::check()) {
            $hasPromotionItems = UserCart::where('user_id', $userId)
                ->whereNotNull('promotion_id')
                ->exists();
        } else {
            $sessionCart = session('guest_cart', []);
            $hasPromotionItems = collect($sessionCart)->contains(function($item) {
                return isset($item['promotion_id']) && !empty($item['promotion_id']);
            });
        }

        if ($hasPromotionItems) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code tidak boleh digunakan dengan promotional items (bundle/combo/item discount). Sila keluarkan promotional items dahulu atau proceed tanpa promo code.',
                'error_type' => 'double_discount_prevented'
            ], 400);
        }

        // Validate promo code
        $promotion = $promotionService->validatePromoCode($promoCode, $cartItems, Auth::user());

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'The promo code is invalid, expired, or does not meet the minimum requirements.'
            ], 400);
        }

        // Additional validation: Check usage limits using PromotionUsageLogger
        $usageLogger = app(\App\Services\Promotions\PromotionUsageLogger::class);
        $canUseResult = $usageLogger->canUserUsePromotion($promotion, $userId);

        if (!$canUseResult['can_use']) {
            return response()->json([
                'success' => false,
                'message' => $canUseResult['reason'],
                'error_type' => $canUseResult['error_type'] ?? 'validation_failed',
                'details' => $canUseResult
            ], 400);
        }

        // Calculate discount
        $discountResult = $promotionService->calculatePromotionDiscount($promotion, $cartItems);
        $discountAmount = $discountResult['discount'];

        logger()->info('Promo discount calculation', [
            'cart_items' => $cartItems,
            'discount_result' => $discountResult,
            'discount_amount' => $discountAmount
        ]);

        if ($discountAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kod promo tidak memberikan sebarang diskaun untuk cart anda'
            ], 400);
        }

        // Apply promo code
        if (Auth::check()) {
            // Get cart items count first
            $cartItemsCount = UserCart::where('user_id', $userId)->count();
            $discountPerItem = $cartItemsCount > 0 ? ($discountAmount / $cartItemsCount) : 0;

            // Update all cart items for this user with the promo code
            UserCart::where('user_id', $userId)->update([
                'applied_promo_code' => $promoCode,
                'promo_discount_amount' => $discountPerItem
            ]);

            logger()->info('Promo applied to cart items', [
                'cart_items_count' => $cartItemsCount,
                'total_discount' => $discountAmount,
                'discount_per_item' => $discountPerItem
            ]);
        } else {
            // Store in session for guest
            session([
                'guest_promo_code' => $promoCode,
                'guest_promo_discount' => $discountAmount
            ]);
        }

        $cartTotal = collect($cartItems)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        return response()->json([
            'success' => true,
            'message' => 'Kod promo berjaya digunakan!',
            'promo_code' => $promoCode,
            'discount_amount' => $discountAmount,
            'cart_total' => $cartTotal,
            'final_total' => max(0, $cartTotal - $discountAmount),
            'promotion_name' => $promotion->name,
            'discount_text' => $promotion->discount_text
        ]);
    }

    /**
     * Remove promo code from cart
     */
    public function removePromoCode()
    {
        if (Auth::check()) {
            UserCart::where('user_id', Auth::id())->update([
                'applied_promo_code' => null,
                'promo_discount_amount' => 0
            ]);
        } else {
            session()->forget(['guest_promo_code', 'guest_promo_discount']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kod promo telah dikeluarkan'
        ]);
    }

    /**
     * Get promo code details (for cart display)
     */
    public function getPromoCodeDetails()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $cartItem = UserCart::where('user_id', $userId)
                ->whereNotNull('applied_promo_code')
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => true,
                    'has_promo' => false
                ]);
            }

            $totalDiscount = UserCart::where('user_id', $userId)
                ->sum('promo_discount_amount');

            return response()->json([
                'success' => true,
                'has_promo' => true,
                'promo_code' => $cartItem->applied_promo_code,
                'discount_amount' => $totalDiscount
            ]);
        } else {
            $promoCode = session('guest_promo_code');
            $discount = session('guest_promo_discount', 0);

            return response()->json([
                'success' => true,
                'has_promo' => $promoCode ? true : false,
                'promo_code' => $promoCode,
                'discount_amount' => $discount
            ]);
        }
    }

    /**
     * Add promotion (combo/bundle/buy-x-free-y) to cart
     * This automatically adds all required items
     */
    public function addPromotionToCart(Request $request)
    {
        $validated = $request->validate([
            'promotion_id' => 'required|exists:promotions,id',
        ]);

        $promotion = Promotion::with('menuItems')->findOrFail($validated['promotion_id']);

        // Check if promotion is valid
        if (!$promotion->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This promotion is no longer valid or active.',
            ], 400);
        }

        // Check if user can use this promotion (usage limits)
        $userId = Auth::id();
        if (!$promotion->canBeUsedBy($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the usage limit for this promotion.',
            ], 400);
        }

        // Generate unique group ID for this promotion instance
        $promotionGroupId = \Illuminate\Support\Str::uuid()->toString();

        $itemsAdded = [];
        $totalItems = 0;
        $promoCodeRemoved = false; // Track if promo code was removed

        try {
            \DB::beginTransaction();

            // IMPORTANT: Remove any applied promo code when adding bundle/combo items (prevent double discount)
            if ($userId) {
                // Logged-in users: Clear promo code from all cart items
                $hasPromoCode = UserCart::where('user_id', $userId)
                    ->whereNotNull('applied_promo_code')
                    ->exists();

                if ($hasPromoCode) {
                    UserCart::where('user_id', $userId)
                        ->whereNotNull('applied_promo_code')
                        ->update([
                            'applied_promo_code' => null,
                            'promo_discount_amount' => 0
                        ]);

                    $promoCodeRemoved = true;

                    \Log::info('Promo code removed because bundle/combo item added', [
                        'user_id' => $userId,
                        'promotion_id' => $promotion->id,
                        'promotion_name' => $promotion->name
                    ]);
                }
            } else {
                // Guest users: Clear promo code from session
                $cart = session('guest_cart', []);
                $hasPromoCode = collect($cart)->contains(function($item) {
                    return isset($item['applied_promo_code']) && !empty($item['applied_promo_code']);
                });

                if ($hasPromoCode) {
                    $cart = collect($cart)->map(function($item) {
                        unset($item['applied_promo_code']);
                        return $item;
                    })->toArray();
                    session(['guest_cart' => $cart]);

                    $promoCodeRemoved = true;

                    \Log::info('Promo code removed from guest cart because bundle/combo item added', [
                        'promotion_id' => $promotion->id,
                        'promotion_name' => $promotion->name
                    ]);
                }
            }

            // Handle different promotion types
            switch ($promotion->promotion_type) {
                case Promotion::TYPE_COMBO_DEAL:
                case Promotion::TYPE_BUNDLE:
                    // Get items and bundle price
                    $items = $promotion->promotion_type === Promotion::TYPE_COMBO_DEAL
                        ? $promotion->getComboItems()
                        : $promotion->getBundleItems();

                    $bundlePrice = $promotion->promotion_type === Promotion::TYPE_COMBO_DEAL
                        ? $promotion->getComboPrice()
                        : $promotion->getBundlePrice();

                    if (!$items || empty($items)) {
                        throw new \Exception('No items configured for this promotion');
                    }

                    // NOTE: We don't delete existing cart items anymore because:
                    // 1. Each bundle instance has unique promotion_group_id
                    // 2. Users should be able to add same bundle multiple times
                    // 3. Bundle items are separate from regular items (checked by promotion_id)

                    // Calculate total regular price
                    // IMPORTANT: Always use MenuItem->price (original price), NOT discounted price
                    // Item discounts should NOT affect bundle/combo pricing
                    $totalRegularPrice = 0;
                    $menuItems = [];
                    foreach ($items as $item) {
                        $menuItem = MenuItem::find($item['item_id']);
                        if (!$menuItem || !$menuItem->availability) {
                            throw new \Exception("Item '{$menuItem->name}' is not available");
                        }
                        $quantity = $item['quantity'] ?? 1;
                        // Use original price from database (item_discount does not apply here)
                        $totalRegularPrice += $menuItem->price * $quantity;
                        $menuItems[] = [
                            'menu_item' => $menuItem,
                            'quantity' => $quantity
                        ];
                    }

                    // Calculate proportional prices (distribute bundle price proportionally)
                    foreach ($menuItems as $item) {
                        $menuItem = $item['menu_item'];
                        $quantity = $item['quantity'];

                        // Calculate proportional price
                        $itemRegularTotal = $menuItem->price * $quantity;
                        $proportion = $totalRegularPrice > 0 ? ($itemRegularTotal / $totalRegularPrice) : 0;
                        $proportionalPrice = $bundlePrice * $proportion;
                        $unitProportionalPrice = $quantity > 0 ? ($proportionalPrice / $quantity) : 0;

                        // Log for debugging
                        \Log::info('Bundle item pricing', [
                            'item' => $menuItem->name,
                            'regular_price' => $menuItem->price,
                            'quantity' => $quantity,
                            'proportion' => $proportion,
                            'proportional_price' => $unitProportionalPrice,
                            'bundle_price' => $bundlePrice,
                            'total_regular' => $totalRegularPrice
                        ]);

                        $this->addPromotionItemToCart(
                            $userId,
                            $menuItem,
                            $quantity,
                            $promotion->id,
                            $promotionGroupId,
                            false,
                            $unitProportionalPrice // Use proportional price instead of regular price
                        );

                        $itemsAdded[] = $menuItem->name . ' (x' . $quantity . ')';
                        $totalItems += $quantity;
                    }
                    break;

                case Promotion::TYPE_BUY_X_FREE_Y:
                    // Get buy/free configuration
                    $config = $promotion->getBuyXGetYConfig();

                    if (!$config['buy_item_id'] || !$config['get_item_id']) {
                        throw new \Exception('Buy X Free Y promotion not configured properly');
                    }

                    $buyItem = MenuItem::find($config['buy_item_id']);
                    $freeItem = MenuItem::find($config['get_item_id']);

                    if (!$buyItem || !$buyItem->availability || !$freeItem || !$freeItem->availability) {
                        throw new \Exception('One or more items are not available');
                    }

                    // Add items to buy
                    $buyQty = $config['buy_quantity'] ?? 1;
                    $this->addPromotionItemToCart(
                        $userId,
                        $buyItem,
                        $buyQty,
                        $promotion->id,
                        $promotionGroupId,
                        false
                    );
                    $itemsAdded[] = $buyItem->name . ' (x' . $buyQty . ')';
                    $totalItems += $buyQty;

                    // Add free items
                    $freeQty = $config['get_quantity'] ?? 1;
                    $this->addPromotionItemToCart(
                        $userId,
                        $freeItem,
                        $freeQty,
                        $promotion->id,
                        $promotionGroupId,
                        true // is_free_item
                    );
                    $itemsAdded[] = $freeItem->name . ' (x' . $freeQty . ' FREE)';
                    $totalItems += $freeQty;
                    break;

                default:
                    throw new \Exception('This promotion type does not support auto-add to cart');
            }

            \DB::commit();

            // Build success message
            $message = $promotion->name . ' added to cart!';
            if ($promoCodeRemoved) {
                $message .= ' (Promo code removed - cannot combine with bundles)';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'items_added' => $itemsAdded,
                'total_items' => $totalItems,
                'promotion_group_id' => $promotionGroupId,
                'cart_count' => UserCart::getCartCount($userId),
                'promo_code_removed' => $promoCodeRemoved
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to add promotion to cart', [
                'promotion_id' => $promotion->id,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Helper method to add individual promotion item to cart
     */
    private function addPromotionItemToCart($userId, $menuItem, $quantity, $promotionId, $promotionGroupId, $isFreeItem = false, $customUnitPrice = null)
    {
        // Determine unit price
        $unitPrice = $isFreeItem ? 0 : ($customUnitPrice ?? $menuItem->price);

        // Guest users - store in session
        if (!$userId) {
            $cart = session('guest_cart', []);
            $cart[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'special_notes' => null,
                'promotion_id' => $promotionId,
                'promotion_group_id' => $promotionGroupId,
                'is_free_item' => $isFreeItem,
            ];
            session(['guest_cart' => $cart]);
            return;
        }

        // Logged in users - store in database
        // Simply create new cart item (no unique constraint after migration refresh)
        UserCart::create([
            'user_id' => $userId,
            'menu_item_id' => $menuItem->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'special_notes' => null,
            'promotion_id' => $promotionId,
            'promotion_group_id' => $promotionGroupId,
            'is_free_item' => $isFreeItem,
        ]);
    }

    /**
     * Remove entire promotion group from cart (all items with same promotion_group_id)
     */
    public function removePromotionGroup(Request $request, $promotionGroupId)
    {
        // Guest users - remove from session cart
        if (!Auth::check()) {
            $cart = session('guest_cart', []);

            // Find all items with matching promotion_group_id
            $itemsToRemove = collect($cart)->filter(function ($item) use ($promotionGroupId) {
                return isset($item['promotion_group_id']) && $item['promotion_group_id'] === $promotionGroupId;
            });

            if ($itemsToRemove->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promotion group not found in cart'
                ], 404);
            }

            // Get promotion name for message
            $firstItem = $itemsToRemove->first();
            $promotion = isset($firstItem['promotion_id']) ? Promotion::find($firstItem['promotion_id']) : null;
            $promotionName = $promotion ? $promotion->name : 'Promotion';
            $itemCount = $itemsToRemove->count();

            // Remove all items with matching promotion_group_id
            $cart = collect($cart)->filter(function ($item) use ($promotionGroupId) {
                return !isset($item['promotion_group_id']) || $item['promotion_group_id'] !== $promotionGroupId;
            })->values()->toArray();

            session(['guest_cart' => $cart]);

            return response()->json([
                'success' => true,
                'message' => "'{$promotionName}' removed from cart ({$itemCount} items removed)",
                'promotion_name' => $promotionName,
                'items_removed' => $itemCount,
                'cart_count' => collect($cart)->sum('quantity')
            ]);
        }

        // Logged in users - remove from database
        $userId = Auth::id();

        // Find all cart items with matching promotion_group_id
        $cartItems = UserCart::with('promotion')
            ->where('user_id', $userId)
            ->where('promotion_group_id', $promotionGroupId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion group not found in cart'
            ], 404);
        }

        // Get promotion name for message
        $promotion = $cartItems->first()->promotion;
        $promotionName = $promotion ? $promotion->name : 'Promotion';
        $itemCount = $cartItems->count();

        // Delete all items in this promotion group
        UserCart::where('user_id', $userId)
            ->where('promotion_group_id', $promotionGroupId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "'{$promotionName}' removed from cart ({$itemCount} items removed)",
            'promotion_name' => $promotionName,
            'items_removed' => $itemCount,
            'cart_count' => UserCart::getCartCount($userId)
        ]);
    }

    /**
     * Helper: Get active item discount for a menu item
     * Returns discount info or null if no active discount
     */
    private function getActiveItemDiscount($menuItemId)
    {
        // Get active item discount promotions
        $itemDiscounts = Promotion::where('promotion_type', Promotion::TYPE_ITEM_DISCOUNT)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        \Log::info('getActiveItemDiscount - Found promotions', [
            'menu_item_id' => $menuItemId,
            'total_item_discounts' => $itemDiscounts->count(),
            'promotions' => $itemDiscounts->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'discount_type' => $p->discount_type,
                    'discount_value' => $p->discount_value,
                    'item_ids' => $p->getDiscountedItemIds(),
                ];
            })
        ]);

        // Check if this menu item has discount
        foreach ($itemDiscounts as $promotion) {
            $itemIds = $promotion->getDiscountedItemIds();
            if ($itemIds && in_array($menuItemId, $itemIds)) {
                \Log::info('getActiveItemDiscount - Match found!', [
                    'menu_item_id' => $menuItemId,
                    'promotion_id' => $promotion->id,
                    'promotion_name' => $promotion->name,
                ]);

                return [
                    'promotion_id' => $promotion->id,
                    'promotion_name' => $promotion->name,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                ];
            }
        }

        \Log::info('getActiveItemDiscount - No match found', [
            'menu_item_id' => $menuItemId,
        ]);

        return null;
    }

    /**
     * Get available vouchers for current user
     */
    public function getAvailableVouchers()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login']);
        }

        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return response()->json(['success' => false, 'vouchers' => []]);
        }

        // Get user's active vouchers
        $vouchers = CustomerVoucher::where('customer_profile_id', $customerProfile->id)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->with('voucherTemplate')
            ->get()
            ->map(function($voucher) {
                return [
                    'id' => $voucher->id,
                    'name' => $voucher->voucherTemplate->name ?? 'Voucher',
                    'description' => $voucher->voucherTemplate->description ?? '',
                    'discount_type' => $voucher->voucherTemplate->discount_type ?? 'fixed',
                    'discount_value' => $voucher->voucherTemplate->discount_value ?? 0,
                    'minimum_spend' => $voucher->voucherTemplate->minimum_spend ?? 0,
                    'expiry_date' => $voucher->expiry_date ? $voucher->expiry_date->format('M j, Y') : null,
                    'source' => $voucher->source ?? 'collection',
                ];
            });

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers
        ]);
    }

    /**
     * Apply voucher to cart
     */
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:customer_vouchers,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login']);
        }

        $voucher = CustomerVoucher::with('voucherTemplate')->findOrFail($request->voucher_id);

        // Verify voucher belongs to user
        if ($voucher->customer_profile_id != $user->customerProfile->id) {
            return response()->json(['success' => false, 'message' => 'Invalid voucher']);
        }

        // Check if voucher is active
        if ($voucher->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'This voucher is no longer active']);
        }

        // Check expiry
        if ($voucher->expiry_date && $voucher->expiry_date < now()) {
            return response()->json(['success' => false, 'message' => 'This voucher has expired']);
        }

        // Get cart total
        $cartItems = UserCart::where('user_id', $user->id)->with('menuItem')->get();
        $cartTotal = $cartItems->sum(function($item) {
            return $item->menuItem->price * $item->quantity;
        });

        // Check minimum spend
        $minimumSpend = $voucher->voucherTemplate->minimum_spend ?? 0;
        if ($cartTotal < $minimumSpend) {
            return response()->json([
                'success' => false,
                'message' => sprintf('Minimum spend RM%.2f required. Add RM%.2f more.', $minimumSpend, $minimumSpend - $cartTotal)
            ]);
        }

        // Calculate discount
        $discountType = $voucher->voucherTemplate->discount_type ?? 'fixed';
        $discountValue = $voucher->voucherTemplate->discount_value ?? 0;

        if ($discountType === 'percentage') {
            $discount = ($cartTotal * $discountValue) / 100;
            // Cap at maximum discount if set
            if (isset($voucher->voucherTemplate->max_discount)) {
                $discount = min($discount, $voucher->voucherTemplate->max_discount);
            }
        } else {
            $discount = $discountValue;
        }

        // Store voucher in session
        session(['applied_voucher' => [
            'id' => $voucher->id,
            'name' => $voucher->voucherTemplate->name ?? 'Voucher',
            'description' => $voucher->voucherTemplate->description ?? '',
            'discount' => $discount,
            'discount_type' => $discountType,
            'discount_value' => $discountValue
        ]]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher applied successfully!',
            'voucher' => [
                'name' => $voucher->voucherTemplate->name ?? 'Voucher',
                'description' => $voucher->voucherTemplate->description ?? '',
                'discount' => $discount
            ],
            'new_total' => max(0, $cartTotal - $discount)
        ]);
    }

    /**
     * Remove applied voucher from cart
     */
    public function removeVoucher()
    {
        session()->forget('applied_voucher');

        return response()->json([
            'success' => true,
            'message' => 'Voucher removed'
        ]);
    }

    /**
     * Get applied voucher from session
     */
    public function getAppliedVoucher()
    {
        $appliedVoucher = session('applied_voucher');

        if ($appliedVoucher) {
            return response()->json([
                'success' => true,
                'voucher' => $appliedVoucher
            ]);
        }

        return response()->json([
            'success' => true,
            'voucher' => null
        ]);
    }
}
