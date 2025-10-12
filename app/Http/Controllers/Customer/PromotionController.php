<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\HappyHourDeal;
use App\Services\Promotions\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Display all active promotions and deals
     */
    public function index()
    {
        $user = Auth::user();

        // Get all active promotions with relationships
        $promotions = $this->promotionService->getActivePromotions($user)
            ->load(['menuItems', 'categories', 'usageLogs']);

        return view('customer.promotions.index', compact('promotions'));
    }

    /**
     * Show specific promotion details
     */
    public function show($id)
    {
        $promotion = Promotion::with(['menuItems', 'categories'])
            ->findOrFail($id);

        // Check if promotion is valid
        if (!$promotion->isValid()) {
            return redirect()
                ->route('customer.promotions.index')
                ->with('error', 'This promotion is not currently available.');
        }

        $user = Auth::user();

        // Check if user can use this promotion
        if (!$promotion->canBeUsedBy($user?->id)) {
            return redirect()
                ->route('customer.promotions.index')
                ->with('error', 'You have reached the usage limit for this promotion.');
        }

        // Get promotion stats for display
        $stats = [
            'remaining_uses' => $promotion->getRemainingUses(),
            'usage_percentage' => $promotion->usage_percentage
        ];

        return view('customer.promotions.show', compact('promotion', 'stats'));
    }

    /**
     * Show happy hour deal details
     */
    public function showHappyHour($id)
    {
        $happyHourDeal = HappyHourDeal::active()
            ->with('menuItems.category')
            ->findOrFail($id);

        $timeStatus = $happyHourDeal->getTimeStatus();
        $isActive = $happyHourDeal->isCurrentlyActive();

        return view('customer.promotions.happy-hour', compact(
            'happyHourDeal',
            'timeStatus',
            'isActive'
        ));
    }

    /**
     * Apply promo code to cart/order
     */
    public function applyPromoCode(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
            'cart_items' => 'nullable|array'
        ]);

        $user = Auth::user();
        $cartItems = $request->input('cart_items', []);

        // Validate promo code using service
        $promotion = $this->promotionService->validatePromoCode(
            $request->promo_code,
            $cartItems,
            $user
        );

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid, expired, or unavailable promo code.'
            ], 404);
        }

        // Calculate discount
        $discountResult = $this->promotionService->calculatePromotionDiscount($promotion, $cartItems);

        if ($discountResult['discount'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This promo code cannot be applied to your cart.'
            ], 400);
        }

        // Store promo in session
        session([
            'applied_promo' => [
                'id' => $promotion->id,
                'code' => $promotion->promo_code,
                'name' => $promotion->name,
                'discount' => $discountResult['discount'],
                'type' => $promotion->discount_type,
                'value' => $promotion->discount_value,
                'affected_items' => $discountResult['affected_items'] ?? []
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo code applied successfully!',
            'discount' => $discountResult['discount'],
            'promotion' => [
                'name' => $promotion->name,
                'discount_text' => $promotion->discount_text,
                'terms' => $promotion->terms_conditions
            ]
        ]);
    }

    /**
     * Remove applied promo code
     */
    public function removePromoCode()
    {
        session()->forget('applied_promo');

        return response()->json([
            'success' => true,
            'message' => 'Promo code removed.'
        ]);
    }

    /**
     * Get best promotion for current cart
     */
    public function getBestPromotion(Request $request)
    {
        $request->validate([
            'cart_items' => 'required|array'
        ]);

        $user = Auth::user();
        $cartItems = $request->input('cart_items');

        $bestPromo = $this->promotionService->getBestPromotion($cartItems, $user);

        if (!$bestPromo) {
            return response()->json([
                'success' => false,
                'message' => 'No applicable promotions found for your cart.'
            ]);
        }

        return response()->json([
            'success' => true,
            'promotion' => [
                'id' => $bestPromo['promotion']->id,
                'name' => $bestPromo['promotion']->name,
                'type' => $bestPromo['promotion']->type_label,
                'discount' => $bestPromo['discount'],
                'discount_text' => $bestPromo['promotion']->discount_text
            ]
        ]);
    }

    /**
     * Get currently active happy hour deals (for AJAX)
     */
    public function activeHappyHours()
    {
        $deals = HappyHourDeal::active()
            ->with('menuItems')
            ->get()
            ->filter(function($deal) {
                return $deal->isCurrentlyActive();
            });

        return response()->json([
            'success' => true,
            'deals' => $deals->map(function($deal) {
                return [
                    'id' => $deal->id,
                    'name' => $deal->name,
                    'discount_percentage' => $deal->discount_percentage,
                    'time_status' => $deal->getTimeStatus(),
                    'menu_items' => $deal->menuItems->pluck('id')
                ];
            })
        ]);
    }

    /**
     * Browse promotions by type
     */
    public function byType($type)
    {
        $user = Auth::user();
        $promotions = $this->promotionService->getPromotionsByType($type, $user);

        $typeLabel = match($type) {
            Promotion::TYPE_COMBO_DEAL => 'Combo Deals',
            Promotion::TYPE_ITEM_DISCOUNT => 'Discounts',
            Promotion::TYPE_BUY_X_FREE_Y => 'Buy X Free Y',
            Promotion::TYPE_PROMO_CODE => 'Promo Codes',
            Promotion::TYPE_SEASONAL => 'Seasonal Offers',
            Promotion::TYPE_BUNDLE => 'Bundle Deals',
            default => 'Promotions'
        };

        return view('customer.promotions.by-type', compact('promotions', 'type', 'typeLabel'));
    }
}
