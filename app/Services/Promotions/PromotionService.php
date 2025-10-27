<?php

namespace App\Services\Promotions;

use App\Models\Promotion;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PromotionService
{
    /**
     * Get all active and valid promotions
     */
    public function getActivePromotions(?User $user = null): Collection
    {
        $query = Promotion::valid()
            ->with(['menuItems', 'categories'])
            ->ordered();

        return $query->get()->filter(function($promotion) use ($user) {
            return $promotion->isValid() && $promotion->canBeUsedBy($user?->id);
        });
    }

    /**
     * Get featured promotions
     */
    public function getFeaturedPromotions(?User $user = null): Collection
    {
        return $this->getActivePromotions($user)
            ->where('is_featured', true)
            ->take(6);
    }

    /**
     * Get promotions by type
     */
    public function getPromotionsByType(string $type, ?User $user = null): Collection
    {
        return $this->getActivePromotions($user)
            ->where('promotion_type', $type);
    }

    /**
     * Get applicable promotions for given cart items
     *
     * @param array $cartItems Array of ['menu_item_id' => quantity]
     * @param User|null $user
     * @return Collection
     */
    public function getApplicablePromotions(array $cartItems, ?User $user = null): Collection
    {
        $activePromotions = $this->getActivePromotions($user);
        $menuItemIds = array_keys($cartItems);

        return $activePromotions->filter(function($promotion) use ($menuItemIds, $cartItems) {
            switch($promotion->promotion_type) {
                case Promotion::TYPE_COMBO_DEAL:
                    return $this->isComboApplicable($promotion, $cartItems);

                case Promotion::TYPE_ITEM_DISCOUNT:
                    return $this->isItemDiscountApplicable($promotion, $menuItemIds);

                case Promotion::TYPE_BUY_X_FREE_Y:
                    return $this->isBuyXFreeYApplicable($promotion, $cartItems);

                case Promotion::TYPE_PROMO_CODE:
                    // Promo codes must be explicitly applied
                    return false;

                case Promotion::TYPE_SEASONAL:
                case Promotion::TYPE_BUNDLE:
                    return $this->isBundleApplicable($promotion, $cartItems);

                default:
                    return false;
            }
        });
    }

    /**
     * Validate and get promotion by promo code
     */
    public function validatePromoCode(string $code, array $cartItems = [], ?User $user = null): ?Promotion
    {
        $promotion = Promotion::where('promo_code', $code)
            ->ofType(Promotion::TYPE_PROMO_CODE)
            ->first();

        if (!$promotion) {
            return null;
        }

        // Check if valid and user can use it
        if (!$promotion->isValid() || !$promotion->canBeUsedBy($user?->id)) {
            return null;
        }

        // Check minimum order value
        if ($promotion->minimum_order_value) {
            $cartTotal = $this->calculateCartTotal($cartItems);
            if ($cartTotal < $promotion->minimum_order_value) {
                return null;
            }
        }

        return $promotion;
    }

    /**
     * Calculate discount for a promotion applied to cart
     *
     * @param Promotion $promotion
     * @param array $cartItems ['menu_item_id' => ['item' => MenuItem, 'quantity' => int, 'price' => float]]
     * @return array ['discount' => float, 'affected_items' => array]
     */
    public function calculatePromotionDiscount(Promotion $promotion, array $cartItems): array
    {
        switch($promotion->promotion_type) {
            case Promotion::TYPE_COMBO_DEAL:
                return $this->calculateComboDiscount($promotion, $cartItems);

            case Promotion::TYPE_ITEM_DISCOUNT:
                return $this->calculateItemDiscount($promotion, $cartItems);

            case Promotion::TYPE_BUY_X_FREE_Y:
                return $this->calculateBuyXFreeYDiscount($promotion, $cartItems);

            case Promotion::TYPE_PROMO_CODE:
                return $this->calculatePromoCodeDiscount($promotion, $cartItems);

            case Promotion::TYPE_SEASONAL:
            case Promotion::TYPE_BUNDLE:
                return $this->calculateBundleDiscount($promotion, $cartItems);

            default:
                return ['discount' => 0, 'affected_items' => []];
        }
    }

    /**
     * Get best promotion for cart (highest discount)
     */
    public function getBestPromotion(array $cartItems, ?User $user = null): ?array
    {
        $applicablePromotions = $this->getApplicablePromotions($cartItems, $user);

        $bestPromotion = null;
        $maxDiscount = 0;

        foreach($applicablePromotions as $promotion) {
            $result = $this->calculatePromotionDiscount($promotion, $cartItems);

            if ($result['discount'] > $maxDiscount) {
                $maxDiscount = $result['discount'];
                $bestPromotion = [
                    'promotion' => $promotion,
                    'discount' => $result['discount'],
                    'affected_items' => $result['affected_items']
                ];
            }
        }

        return $bestPromotion;
    }

    /**
     * Apply promotion to order and log usage
     */
    public function applyPromotionToOrder(Promotion $promotion, $orderId, $userId, $discountAmount, $subtotal, $total)
    {
        try {
            DB::beginTransaction();

            // Use PromotionUsageLogger service for consistent logging
            $usageLogger = app(PromotionUsageLogger::class);

            // Get the order for logging
            $order = \App\Models\Order::find($orderId);
            if (!$order) {
                throw new \Exception("Order not found: {$orderId}");
            }

            // Get user instance if user ID provided
            $user = $userId ? User::find($userId) : null;

            // Log usage (this will increment current_usage_count and create log entry)
            $usageLogger->logUsage($promotion, $order, $discountAmount, $user);

            DB::commit();
            return true;

        } catch(\Exception $e) {
            DB::rollBack();
            Log::error('Failed to apply promotion', [
                'promotion_id' => $promotion->id,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Check if combo deal is applicable
     */
    private function isComboApplicable(Promotion $promotion, array $cartItems): bool
    {
        $comboItems = $promotion->menuItems;

        foreach($comboItems as $comboItem) {
            $requiredQty = $comboItem->pivot->quantity;
            $itemId = $comboItem->id;

            if ($comboItem->pivot->is_required) {
                if (!isset($cartItems[$itemId]) || $cartItems[$itemId] < $requiredQty) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if item discount is applicable
     */
    private function isItemDiscountApplicable(Promotion $promotion, array $menuItemIds): bool
    {
        $promoItems = $promotion->menuItems->pluck('id')->toArray();
        $promoCategories = $promotion->categories->pluck('id')->toArray();

        if (empty($promoItems) && empty($promoCategories)) {
            return true; // Apply to all items
        }

        // Check if any cart item matches promo items or categories
        foreach($menuItemIds as $itemId) {
            if (in_array($itemId, $promoItems)) {
                return true;
            }

            // Check category
            $item = MenuItem::find($itemId);
            if ($item && in_array($item->category_id, $promoCategories)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if Buy X Free Y is applicable
     */
    private function isBuyXFreeYApplicable(Promotion $promotion, array $cartItems): bool
    {
        $config = $promotion->promo_config;

        if (!isset($config['buy_quantity']) || !isset($config['buy_item_ids'])) {
            return false;
        }

        $buyItemIds = $config['buy_item_ids'];
        $buyQuantity = $config['buy_quantity'];

        // Check if cart has required buy items
        foreach($buyItemIds as $itemId) {
            if (isset($cartItems[$itemId]) && $cartItems[$itemId] >= $buyQuantity) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if bundle is applicable
     */
    private function isBundleApplicable(Promotion $promotion, array $cartItems): bool
    {
        return $this->isComboApplicable($promotion, $cartItems);
    }

    /**
     * Calculate combo deal discount
     */
    private function calculateComboDiscount(Promotion $promotion, array $cartItems): array
    {
        $config = $promotion->promo_config;
        $comboPrice = $config['combo_price'] ?? 0;
        $originalPrice = $config['original_price'] ?? 0;

        if (!$originalPrice || !$comboPrice) {
            // Calculate from items
            $originalPrice = 0;
            foreach($promotion->menuItems as $item) {
                $customPrice = $item->pivot->custom_price ?? $item->price;
                $qty = $item->pivot->quantity;
                $originalPrice += ($customPrice * $qty);
            }
            $comboPrice = $promotion->promo_config['combo_price'] ?? $originalPrice;
        }

        $discount = max(0, $originalPrice - $comboPrice);

        return [
            'discount' => $discount,
            'affected_items' => $promotion->menuItems->pluck('id')->toArray(),
            'combo_price' => $comboPrice,
            'original_price' => $originalPrice
        ];
    }

    /**
     * Calculate item discount
     */
    private function calculateItemDiscount(Promotion $promotion, array $cartItems): array
    {
        $discount = 0;
        $affectedItems = [];

        foreach($cartItems as $itemId => $data) {
            $item = $data['item'] ?? MenuItem::find($itemId);
            if (!$item) continue;

            // Check if item is in promotion
            $isInPromo = $promotion->menuItems->contains('id', $itemId);
            $isInPromoCategory = $promotion->categories->contains('id', $item->category_id);

            if ($isInPromo || $isInPromoCategory) {
                $itemPrice = $data['price'] ?? $item->price;
                $quantity = $data['quantity'] ?? 1;
                $itemTotal = $itemPrice * $quantity;

                $itemDiscount = $promotion->calculateDiscount($itemTotal);
                $discount += $itemDiscount;
                $affectedItems[] = $itemId;
            }
        }

        return [
            'discount' => $discount,
            'affected_items' => $affectedItems
        ];
    }

    /**
     * Calculate Buy X Free Y discount
     */
    private function calculateBuyXFreeYDiscount(Promotion $promotion, array $cartItems): array
    {
        $config = $promotion->promo_config;
        $buyQty = $config['buy_quantity'] ?? 1;
        $freeQty = $config['free_quantity'] ?? 1;
        $buyItemIds = $config['buy_item_ids'] ?? [];
        $freeItemIds = $config['free_item_ids'] ?? $buyItemIds;
        $maxFreeItems = $config['max_free_items'] ?? 999;

        $discount = 0;
        $affectedItems = [];

        foreach($buyItemIds as $buyItemId) {
            if (!isset($cartItems[$buyItemId])) continue;

            $quantity = $cartItems[$buyItemId]['quantity'] ?? $cartItems[$buyItemId];

            // Calculate how many free items customer gets
            $sets = floor($quantity / $buyQty);
            $freeItemsCount = min($sets * $freeQty, $maxFreeItems);

            if ($freeItemsCount > 0) {
                // Find price of free item
                $freeItemId = $freeItemIds[0] ?? $buyItemId;
                $freeItem = MenuItem::find($freeItemId);

                if ($freeItem) {
                    $discount += ($freeItem->price * $freeItemsCount);
                    $affectedItems[] = $buyItemId;
                    if ($freeItemId != $buyItemId) {
                        $affectedItems[] = $freeItemId;
                    }
                }
            }
        }

        return [
            'discount' => $discount,
            'affected_items' => array_unique($affectedItems),
            'free_items_count' => $freeItemsCount ?? 0
        ];
    }

    /**
     * Calculate promo code discount
     */
    private function calculatePromoCodeDiscount(Promotion $promotion, array $cartItems): array
    {
        $cartTotal = $this->calculateCartTotal($cartItems);

        // Check minimum order value
        if ($promotion->minimum_order_value && $cartTotal < $promotion->minimum_order_value) {
            return ['discount' => 0, 'affected_items' => []];
        }

        $discount = $promotion->calculateDiscount($cartTotal);

        return [
            'discount' => $discount,
            'affected_items' => array_keys($cartItems),
            'cart_total' => $cartTotal
        ];
    }

    /**
     * Calculate bundle discount
     */
    private function calculateBundleDiscount(Promotion $promotion, array $cartItems): array
    {
        return $this->calculateComboDiscount($promotion, $cartItems);
    }

    /**
     * Calculate cart total from cart items
     */
    private function calculateCartTotal(array $cartItems): float
    {
        $total = 0;

        foreach($cartItems as $itemId => $data) {
            if (is_array($data)) {
                $price = $data['price'] ?? 0;
                $quantity = $data['quantity'] ?? 1;
            } else {
                // If data is just quantity
                $item = MenuItem::find($itemId);
                $price = $item?->price ?? 0;
                $quantity = $data;
            }

            $total += ($price * $quantity);
        }

        return $total;
    }

    /**
     * Generate unique combo group ID
     */
    public function generateComboGroupId(): string
    {
        return 'COMBO_' . strtoupper(Str::random(8)) . '_' . time();
    }

    /**
     * Get promotion statistics
     */
    public function getPromotionStats(Promotion $promotion): array
    {
        $usageLogs = $promotion->usageLogs;

        return [
            'total_uses' => $usageLogs->count(),
            'total_discount_given' => $usageLogs->sum('discount_amount'),
            'total_revenue' => $usageLogs->sum('order_total'),
            'average_discount' => $usageLogs->avg('discount_amount'),
            'average_order_value' => $usageLogs->avg('order_total'),
            'unique_users' => $usageLogs->where('user_id', '!=', null)->unique('user_id')->count(),
            'remaining_uses' => $promotion->getRemainingUses(),
            'usage_percentage' => $promotion->usage_percentage
        ];
    }
}
