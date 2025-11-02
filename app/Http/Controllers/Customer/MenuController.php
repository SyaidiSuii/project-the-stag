<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Promotion;
use App\Models\KitchenStation;
use App\Models\Order;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display the unified menu page with food, drinks, and set meals.
     */
    public function index(Request $request)
    {
        // Get QR parameters (table number, order type, session code)
        $tableNumber = $request->get('table');
        $orderType = $request->get('order_type');
        $sessionCode = $request->get('session');

        // Store parameters in session for later use during checkout
        if ($tableNumber) {
            session(['qr_table_number' => $tableNumber]);
        }
        if ($orderType) {
            session(['order_type' => $orderType]);
        }
        if ($sessionCode) {
            session(['qr_session_code' => $sessionCode]);
        }

        // If no order type specified (direct access, not from QR), default to takeaway
        if (!$orderType) {
            $orderType = 'takeaway';
            session(['order_type' => 'takeaway']);
        }

        // Get all categories with their available menu items
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('availability', true)->orderBy('name');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        // Debug: Check first menu item rating data
        if ($categories->count() > 0 && $categories->first()->menuItems->count() > 0) {
            $firstItem = $categories->first()->menuItems->first();
            \Log::info('Menu Debug - First item rating:', [
                'name' => $firstItem->name,
                'rating_average' => $firstItem->rating_average,
                'rating_count' => $firstItem->rating_count
            ]);
        }

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

        // Get kitchen load status for customer recommendations
        $kitchenStatus = $this->getKitchenLoadStatus();

        // Get AI-based recommendations for authenticated users
        $recommendedItems = [];
        if (Auth::check()) {
            try {
                $userId = Auth::id();
                $recommendedItemIds = $this->recommendationService->getRecommendations($userId, 8);

                if (!empty($recommendedItemIds)) {
                    $recommendedItems = MenuItem::whereIn('id', $recommendedItemIds)
                        ->where('availability', true)
                        ->with('category')
                        ->get()
                        ->sortBy(function($item) use ($recommendedItemIds) {
                            return array_search($item->id, $recommendedItemIds);
                        })
                        ->values();
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch recommendations for menu page', [
                    'user_id' => Auth::id(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('customer.menu.index', compact('categories', 'kitchenStatus', 'recommendedItems', 'tableNumber', 'orderType'));
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

    /**
     * Get kitchen load status API for real-time updates
     */
    public function getKitchenStatus()
    {
        $status = $this->getKitchenLoadStatus();
        return response()->json($status);
    }

    /**
     * Get current kitchen load status with recommended menu items
     */
    private function getKitchenLoadStatus()
    {
        $stations = KitchenStation::where('is_active', true)
            ->with('stationType')
            ->select('id', 'name', 'station_type', 'station_type_id', 'current_load', 'max_capacity')
            ->get();

        $stationStatus = [];
        $fastStationTypes = [];
        $busyStations = [];

        foreach ($stations as $station) {
            $loadPercentage = $station->max_capacity > 0
                ? round(($station->current_load / $station->max_capacity) * 100, 1)
                : 0;

            $status = 'available';
            $estimatedWait = 5; // Default 5 minutes

            if ($loadPercentage >= 85) {
                $status = 'very_busy';
                $estimatedWait = 25;
                $busyStations[] = $station->station_type;
            } elseif ($loadPercentage >= 70) {
                $status = 'busy';
                $estimatedWait = 15;
            } elseif ($loadPercentage < 40) {
                $status = 'fast';
                $estimatedWait = 5;
                $fastStationTypes[] = $station->id;
            }

            $stationStatus[$station->station_type] = [
                'name' => $station->name,
                'load_percentage' => $loadPercentage,
                'status' => $status,
                'estimated_wait' => $estimatedWait,
                'current_load' => $station->current_load,
                'max_capacity' => $station->max_capacity,
            ];
        }

        // Get actual menu items from fast stations with estimated wait times
        $recommendedItems = collect();
        if (count($fastStationTypes) > 0) {
            $items = MenuItem::where('availability', true)
                ->whereHas('category', function ($query) use ($fastStationTypes) {
                    $query->whereIn('default_station_id', $fastStationTypes);
                })
                ->with('category.defaultStation')
                ->inRandomOrder()
                ->get();

            // Attach estimated wait time to each item based on preparation time
            $recommendedItems = $items->map(function ($item) use ($stations) {
                // Use item's preparation time (default 15 min if not set)
                $basePrepTime = $item->preparation_time ?? 15;

                // Get station load to adjust estimate
                $station = $item->category && $item->category->defaultStation
                    ? $stations->firstWhere('id', $item->category->defaultStation->id)
                    : null;

                if ($station) {
                    $loadPercentage = $station->max_capacity > 0
                        ? ($station->current_load / $station->max_capacity) * 100
                        : 0;

                    // Adjust preparation time based on station load
                    if ($loadPercentage >= 85) {
                        // Very busy - add 50% to prep time
                        $item->estimated_wait = ceil($basePrepTime * 1.5);
                    } elseif ($loadPercentage >= 70) {
                        // Busy - add 30% to prep time
                        $item->estimated_wait = ceil($basePrepTime * 1.3);
                    } elseif ($loadPercentage >= 40) {
                        // Normal - add 10% to prep time
                        $item->estimated_wait = ceil($basePrepTime * 1.1);
                    } else {
                        // Fast - use base prep time
                        $item->estimated_wait = $basePrepTime;
                    }
                } else {
                    $item->estimated_wait = $basePrepTime;
                }

                return $item;
            });
        }

        return [
            'stations' => $stationStatus,
            'recommended_items' => $recommendedItems,
            'busy_types' => $busyStations,
            'overall_status' => count($busyStations) > 2 ? 'busy' : 'normal',
        ];
    }
}
