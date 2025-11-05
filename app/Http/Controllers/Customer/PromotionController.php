<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
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

        // DEBUG: Log promotion count and types
        \Log::info('Customer Promotions Index', [
            'total_count' => $promotions->count(),
            'promotions' => $promotions->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'type' => $p->promotion_type,
                'has_promo_code' => !empty($p->promo_code),
                'is_valid' => $p->isValid(),
                'can_be_used' => $p->canBeUsedBy($user?->id)
            ])
        ]);

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

        // Return type-specific views for combo deals and bundles
        $view = match($promotion->promotion_type) {
            Promotion::TYPE_COMBO_DEAL => 'customer.promotions.combo-deal',
            Promotion::TYPE_BUNDLE => 'customer.promotions.bundle',
            Promotion::TYPE_BUY_X_FREE_Y => 'customer.promotions.buy1free1',
            default => 'customer.promotions.show'
        };

        return view($view, compact('promotion', 'stats'));
    }

    /**
     * Test method to debug promotion data
     */
    public function test($id)
    {
        $promotion = Promotion::with(['menuItems', 'categories'])
            ->findOrFail($id);

        return view('customer.promotions.test', compact('promotion'));
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