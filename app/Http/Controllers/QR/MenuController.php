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
use Illuminate\Support\Str;
use Carbon\Carbon;

class MenuController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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

        // Get main categories (Food and Drinks)
        $mainCategories = Category::whereNull('parent_id')
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%food%')
                    ->orWhere('name', 'LIKE', '%drink%');
            })
            ->orderBy('name')
            ->get();

        // Prepare menu items grouped by main category and subcategory
        $menuData = [];

        foreach ($mainCategories as $mainCategory) {
            // Get subcategories for this main category
            $subCategories = Category::where('parent_id', $mainCategory->id)
                ->with(['menuItems' => function ($query) {
                    $query->available()->orderBy('name');
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            // Group items by subcategory
            $categoryItems = [];
            foreach ($subCategories as $subCategory) {
                if ($subCategory->menuItems->count() > 0) {
                    $categoryItems[$subCategory->name] = $subCategory->menuItems;
                }
            }

            // Only add category if it has items
            if (!empty($categoryItems)) {
                $menuData[$mainCategory->name] = $categoryItems;
            }
        }

        // Get cart from session if exists
        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);
        $cartTotal = $this->calculateCartTotal($cart);

        return view('qr.menu', compact('session', 'menuData', 'cart', 'cartTotal'));
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
            'quantity' => 'required|integer|min:-10|max:10',
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

        // Special case: clear all items when itemId is 0 and quantity is 0
        if ($itemId == 0 && $request->quantity == 0) {
            $cart = [];
            \Log::info('Clearing all items from cart');
        } else {
            // Regular update
            if (isset($cart[$itemId])) {
                $cart[$itemId]['quantity'] += $request->quantity;

                // Remove item if quantity is 0 or less
                if ($cart[$itemId]['quantity'] <= 0) {
                    unset($cart[$itemId]);
                    \Log::info('Removing item ' . $itemId . ' from cart');
                } else {
                    \Log::info('Updated item ' . $itemId . ' quantity to ' . $cart[$itemId]['quantity']);
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
}
