<?php

namespace App\Http\Controllers\QR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableSession;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MenuController extends Controller
{
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
        $session = TableSession::where('session_code', $sessionCode)
            ->with(['table', 'reservation'])
            ->first();
            
        if (!$session) {
            return redirect()->route('qr.error')->with('error', 'Session not found.');
        }
        
        if (!$session->isActive()) {
            return redirect()->route('qr.error')->with('error', 'Session has expired.');
        }
        
        // Get available menu items
        $menuItems = MenuItem::where('availability', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        // Get cart from session if exists
        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);
        $cartTotal = $this->calculateCartTotal($cart);
        
        return view('qr.menu', compact('session', 'menuItems', 'cart', 'cartTotal'));
    }
    
    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_sessions,session_code',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);
        
        $session = TableSession::where('session_code', $request->session_code)
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
            'session_code' => 'required|exists:table_sessions,session_code',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:0|max:10',
        ]);
        
        $session = TableSession::where('session_code', $request->session_code)
            ->where('status', 'active')
            ->first();
            
        if (!$session || !$session->isActive()) {
            return response()->json(['error' => 'Session expired'], 400);
        }
        
        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);
        
        $itemId = $request->menu_item_id;
        
        if ($request->quantity == 0) {
            unset($cart[$itemId]);
        } else {
            if (isset($cart[$itemId])) {
                $cart[$itemId]['quantity'] = $request->quantity;
            }
        }
        
        session([$cartKey => $cart]);
        
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
        
        $session = TableSession::where('session_code', $sessionCode)
            ->with(['table'])
            ->first();
            
        if (!$session || !$session->isActive()) {
            return redirect()->route('qr.error')->with('error', 'Session expired.');
        }
        
        $cartKey = 'qr_cart_' . $session->session_code;
        $cart = session($cartKey, []);
        $cartTotal = $this->calculateCartTotal($cart);
        
        return view('qr.cart', compact('session', 'cart', 'cartTotal'));
    }
    
    /**
     * Place order from cart
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_sessions,session_code',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'special_instructions' => 'nullable|string|max:500',
        ]);
        
        $session = TableSession::where('session_code', $request->session_code)
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
            'table_session_id' => $session->id,
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
            ->with(['items.menuItem', 'tableSession.table', 'trackings'])
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
     * Calculate cart total
     */
    private function calculateCartTotal($cart)
    {
        return array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
    }
    
    /**
     * Call waiter (simple notification)
     */
    public function callWaiter(Request $request)
    {
        $request->validate([
            'session_code' => 'required|exists:table_sessions,session_code',
            'message' => 'nullable|string|max:255',
        ]);
        
        $session = TableSession::where('session_code', $request->session_code)
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
}
