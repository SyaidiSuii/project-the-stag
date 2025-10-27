<?php

namespace App\Http\Controllers\QR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableQrcode;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PaymentService;
use App\Services\SimpleRecommendationService;
use App\Services\Kitchen\OrderDistributionService;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MenuController extends Controller
{
    protected $paymentService;
    protected $simpleRecommender;
    protected $distributionService;

    public function __construct(
        PaymentService $paymentService,
        SimpleRecommendationService $simpleRecommender,
        OrderDistributionService $distributionService
    )
    {
        $this->paymentService = $paymentService;
        $this->simpleRecommender = $simpleRecommender;
        $this->distributionService = $distributionService;
    }

    /**
     * Display menu for QR code access
     */
    public function index(Request $request)
    {
        $sessionCode = $request->get('session');

        if (!$sessionCode) {
            return redirect()->route('qr.error')->with('error', 'Invalid QR code. Session not found.');
        }

        // Find and validate session
        $session = TableQrcode::where('session_code', $sessionCode)
            ->with(['table', 'reservation'])
            ->first();

        if (!$session) {
            return redirect()->route('qr.error')->with('error', 'Session not found.');
        }

        if (!$session->isActive()) {
            return redirect()->route('qr.error')->with('error', 'Session has expired.');
        }

        // Get all categories with their available menu items (same as customer menu)
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('availability', true)->orderBy('name');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        // Get cart from session if exists
        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);
        $cartTotal = $this->calculateCartTotal($cart);

        // Get kitchen load status for customer recommendations
        $kitchenStatus = $this->getKitchenLoadStatus();

        // Get popular/trending items as recommendations for QR guests
        $recommendedItems = [];
        try {
            // For QR guests, we'll get popular items using the simple recommender
            // Pass a guest user ID (0) to get popular items
            $recommendedItemIds = $this->simpleRecommender->getPopularItems(8);

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
            \Log::warning('Failed to fetch recommendations for QR menu page', [
                'session_code' => $session->session_code,
                'error' => $e->getMessage()
            ]);
        }

        // Store order type as 'dine in' in session for QR orders
        session(['qr_order_type_' . $session->session_code => 'dine in']);

        // Disable caching for QR menu page
        return response()
            ->view('qr.menu', compact('session', 'categories', 'cart', 'cartTotal', 'kitchenStatus', 'recommendedItems'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_qrcodes,session_code',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $session = TableQrcode::where('session_code', $request->session_code)
            ->where('status', 'active')
            ->first();

        if (!$session || !$session->isActive()) {
            return response()->json(['error' => 'Session expired'], 400);
        }

        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        if (!$menuItem->availability) {
            return response()->json(['error' => 'Item not available'], 400);
        }

        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);

        $itemId = $menuItem->id;

        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] += $request->quantity;
        } else {
            $cart[$itemId] = [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'quantity' => $request->quantity,
                'image' => $menuItem->image,
            ];
        }

        session([$cartKey => $cart]);

        $cartTotal = $this->calculateCartTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'cart_total' => $cartTotal,
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_qrcodes,session_code',
            'menu_item_id' => 'required|integer',
        ]);

        \Log::info('Updating cart with data: ' . json_encode($request->all()));

        $session = TableQrcode::where('session_code', $request->session_code)
            ->where('status', 'active')
            ->first();

        if (!$session || !$session->isActive()) {
            \Log::info('Session expired during update');
            return response()->json(['error' => 'Session expired'], 400);
        }

        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);

        \Log::info('Current cart before update: ' . json_encode($cart));

        $itemId = $request->menu_item_id;

        // Handle remove request
        if ($request->has('remove') && $request->remove) {
            if (isset($cart[$itemId])) {
                unset($cart[$itemId]);
                \Log::info('Removing item ' . $itemId . ' from cart');
            }
        }
        // Handle quantity change
        elseif ($request->has('change')) {
            $change = intval($request->change);
            if (isset($cart[$itemId])) {
                $cart[$itemId]['quantity'] += $change;

                // Remove item if quantity is 0 or less
                if ($cart[$itemId]['quantity'] <= 0) {
                    unset($cart[$itemId]);
                    \Log::info('Removing item ' . $itemId . ' from cart (quantity 0)');
                } else {
                    \Log::info('Updated item ' . $itemId . ' quantity to ' . $cart[$itemId]['quantity']);
                }
            }
        }
        // Legacy support for old 'quantity' parameter
        elseif ($request->has('quantity')) {
            $quantity = intval($request->quantity);
            // Special case: clear all items when itemId is 0 and quantity is 0
            if ($itemId == 0 && $quantity == 0) {
                $cart = [];
                \Log::info('Clearing all items from cart');
            } else {
                // Regular update
                if (isset($cart[$itemId])) {
                    $cart[$itemId]['quantity'] += $quantity;

                    // Remove item if quantity is 0 or less
                    if ($cart[$itemId]['quantity'] <= 0) {
                        unset($cart[$itemId]);
                        \Log::info('Removing item ' . $itemId . ' from cart');
                    } else {
                        \Log::info('Updated item ' . $itemId . ' quantity to ' . $cart[$itemId]['quantity']);
                    }
                }
            }
        }

        session([$cartKey => $cart]);

        \Log::info('Cart after update: ' . json_encode($cart));

        $cartTotal = $this->calculateCartTotal($cart);

        return response()->json([
            'success' => true,
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'cart_total' => $cartTotal,
        ]);
    }

    /**
     * Show cart contents
     */
    public function viewCart(Request $request)
    {
        $sessionCode = $request->get('session');

        // Log the session code for debugging
        \Log::info('Viewing cart with session code: ' . $sessionCode);

        $session = TableQrcode::where('session_code', $sessionCode)
            ->with(['table'])
            ->first();

        // Log the session object for debugging
        \Log::info('Session object: ' . json_encode($session));

        if (!$session || !$session->isActive()) {
            \Log::info('Session expired or not found');
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Session expired.'], 400);
            }
            return redirect()->route('qr.error')->with('error', 'Session expired.');
        }

        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);

        // Log the cart contents for debugging
        \Log::info('Cart contents: ' . json_encode($cart));

        $cartTotal = $this->calculateCartTotal($cart);

        // If AJAX request, return JSON data
        if ($request->expectsJson()) {
            // Convert cart to proper format
            $cartItems = [];
            foreach ($cart as $item) {
                // Get menu item to access image_url
                $menuItem = MenuItem::find($item['id']);
                $cartItems[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'image' => $item['image'] ?? null,
                    'image_url' => $menuItem ? $menuItem->image_url : null
                ];
            }

            return response()->json([
                'cart' => $cartItems,
                'cart_total' => $cartTotal,
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);
        }

        return view('qr.cart', compact('session', 'cart', 'cartTotal'));
    }

    /**
     * Place order from cart
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_qrcodes,session_code',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        $session = TableQrcode::where('session_code', $request->session_code)
            ->with(['table'])
            ->where('status', 'active')
            ->first();

        if (!$session || !$session->isActive()) {
            return response()->json(['error' => 'Session expired'], 400);
        }

        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $cartTotal = $this->calculateCartTotal($cart);

        // Create order
        $order = Order::create([
            'user_id' => null, // QR orders don't have user_id
            'table_id' => $session->table_id,
            'table_qrcode_id' => $session->id,
            'order_type' => 'qr_table',
            'order_source' => 'qr_scan',
            'order_status' => 'pending',
            'order_time' => now(),
            'table_number' => $session->table->table_number,
            'total_amount' => $cartTotal,
            'payment_status' => 'pending',
            'special_instructions' => $request->special_instructions,
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'session_token' => Str::random(32),
            'confirmation_code' => 'ORD' . strtoupper(Str::random(6)),
        ]);

        // Create order items
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
                'status' => 'pending',
            ]);
        }

        // Distribute order to kitchen stations automatically
        try {
            $this->distributionService->distributeOrder($order);
            \Log::info("QR Order #{$order->confirmation_code} distributed to kitchen stations", [
                'order_id' => $order->id,
                'table' => $session->table->table_number
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to distribute QR order to stations', [
                'order_id' => $order->id,
                'confirmation_code' => $order->confirmation_code,
                'error' => $e->getMessage()
            ]);
            // Don't fail the order, just log the warning
            // Order can still be processed manually if distribution fails
        }

        // Clear cart
        session()->forget($cartKey);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'confirmation_code' => $order->confirmation_code,
            'session_token' => $order->session_token,
        ]);
    }

    /**
     * Track order status
     */
    /**
     * Show order tracking page
     */
    public function showTrackingPage()
    {
        return view('qr.track');
    }

    /**
     * Track order via API
     */
    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'phone' => 'required|string',
        ]);

        $order = Order::where('confirmation_code', $request->order_code)
            ->where('guest_phone', $request->phone)
            ->where('order_type', 'qr_table')
            ->with(['items.menuItem', 'tableQrcode.table', 'trackings'])
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'order' => $order,
            'status' => $order->order_status,
            'table_number' => $order->table_number,
            'total_amount' => $order->total_amount,
            'estimated_time' => $order->estimated_completion_time,
            'items' => $order->items,
        ]);
    }

    /**
     * Show error page
     */
    public function error()
    {
        return view('qr.error');
    }

    /**
     * Call waiter (simple notification)
     */
    public function callWaiter(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_qrcodes,session_code',
            'message' => 'nullable|string|max:255',
        ]);

        $session = TableQrcode::where('session_code', $request->session_code)
            ->with(['table'])
            ->first();

        if (!$session || !$session->isActive()) {
            return response()->json(['error' => 'Session expired'], 400);
        }

        // Here you would typically send notification to staff
        // For now, we'll just log it
        \Log::info('Waiter called for table ' . $session->table->table_number, [
            'session_code' => $session->session_code,
            'message' => $request->message ?? 'Customer needs assistance',
            'timestamp' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Waiter has been notified!',
        ]);
    }

    /**
     * Calculate cart total
     */
    private function calculateCartTotal($cart)
    {
        $total = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        // Round to 2 decimal places for currency
        return round($total, 2);
    }

    /**
     * Get kitchen load status API for real-time updates (QR version)
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
        $stations = \App\Models\KitchenStation::where('is_active', true)
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
                ->limit(4)
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
