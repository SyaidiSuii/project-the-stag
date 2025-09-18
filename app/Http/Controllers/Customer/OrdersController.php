<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TableReservation;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display customer orders page.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get customer's orders with related data
        $orders = Order::with(['items.menuItem', 'table'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get customer's table reservations
        $reservations = TableReservation::with('table')
            ->where('user_id', $userId)
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();
        
        return view('customer.order.index', compact('orders', 'reservations'));
    }

    /**
     * Get order details for AJAX request.
     */
    public function getOrderDetails($orderId)
    {
        $userId = Auth::id();
        
        $order = Order::with(['items.menuItem', 'table', 'user'])
            ->where('id', $orderId)
            ->where('user_id', $userId) // Ensure customer can only see their own orders
            ->first();
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'confirmation_code' => $order->confirmation_code,
                'order_number' => $order->confirmation_code ?? 'ORD-' . $order->id,
                'order_status' => $order->order_status,
                'order_type' => $order->order_type,
                'order_time' => $order->created_at->format('M j, g:i A'),
                'total_amount' => $order->total_amount,
                'formatted_total' => 'RM ' . number_format($order->total_amount, 2),
                'table_number' => $order->table ? $order->table->table_number : $order->table_number,
                'customer_name' => $order->user->name ?? 'Unknown',
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->menuItem->name ?? $item->name ?? 'Unknown Item',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->quantity * $item->unit_price,
                        'formatted_unit_price' => 'RM ' . number_format($item->unit_price, 2),
                        'formatted_total_price' => 'RM ' . number_format($item->quantity * $item->unit_price, 2),
                        'notes' => $item->notes
                    ];
                })
            ]
        ]);
    }

    /**
     * Get order tracking details for AJAX request.
     */
    public function getOrderTracking($orderId)
    {
        $userId = Auth::id();
        
        $order = Order::with(['items.menuItem', 'table', 'user', 'etas'])
            ->where('id', $orderId)
            ->where('user_id', $userId) // Ensure customer can only see their own orders
            ->first();
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Calculate progress based on status
        $progressData = $this->getOrderProgress($order->order_status);
        
        // Get ETA information
        $eta = null;
        if ($order->etas && $order->etas->count() > 0) {
            $latestEta = $order->etas->sortByDesc('created_at')->first();
            $eta = [
                'current_estimate' => $latestEta->current_estimate,
                'is_delayed' => $latestEta->is_delayed,
                'delay_duration' => $latestEta->delay_duration
            ];
        }
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'confirmation_code' => $order->confirmation_code,
                'order_number' => $order->confirmation_code ?? 'ORD-' . $order->id,
                'order_status' => $order->order_status,
                'order_type' => $order->order_type,
                'order_time' => $order->created_at->format('M j, g:i A'),
                'total_amount' => $order->total_amount,
                'formatted_total' => 'RM ' . number_format($order->total_amount, 2),
                'progress' => $progressData,
                'eta' => $eta,
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->menuItem->name ?? $item->name ?? 'Unknown Item',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'formatted_unit_price' => 'RM ' . number_format($item->unit_price, 2)
                    ];
                })
            ]
        ]);
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder($orderId)
    {
        $userId = Auth::id();
        
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId) // Ensure customer can only cancel their own orders
            ->first();
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Check if order can be cancelled (only pending orders can be cancelled)
        if ($order->order_status !== 'pending') {
            return response()->json([
                'error' => 'Order cannot be cancelled. Only pending orders can be cancelled.'
            ], 400);
        }
        
        // Update order status to cancelled
        $order->order_status = 'cancelled';
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order has been cancelled successfully.',
            'order' => [
                'id' => $order->id,
                'order_status' => $order->order_status,
                'confirmation_code' => $order->confirmation_code
            ]
        ]);
    }

    /**
     * Get reorder details for AJAX request.
     */
    public function getReorderDetails($orderId)
    {
        $userId = Auth::id();
        
        $order = Order::with(['items.menuItem'])
            ->where('id', $orderId)
            ->where('user_id', $userId) // Ensure customer can only reorder their own orders
            ->first();
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Calculate current total (prices might have changed)
        $currentTotal = 0;
        $availableItems = [];
        $unavailableItems = [];
        
        foreach ($order->items as $item) {
            if ($item->menuItem && $item->menuItem->availability) {
                $itemTotal = $item->menuItem->price * $item->quantity;
                $currentTotal += $itemTotal;
                
                $availableItems[] = [
                    'id' => $item->menuItem->id,
                    'name' => $item->menuItem->name,
                    'quantity' => $item->quantity,
                    'original_price' => $item->unit_price,
                    'current_price' => $item->menuItem->price,
                    'formatted_current_price' => 'RM ' . number_format($item->menuItem->price, 2),
                    'item_total' => $itemTotal,
                    'formatted_item_total' => 'RM ' . number_format($itemTotal, 2),
                    'price_changed' => $item->unit_price != $item->menuItem->price,
                    'notes' => $item->notes
                ];
            } else {
                $unavailableItems[] = [
                    'name' => $item->menuItem->name ?? 'Unknown Item',
                    'quantity' => $item->quantity,
                    'reason' => $item->menuItem ? 'Currently unavailable' : 'Item no longer exists'
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'confirmation_code' => $order->confirmation_code,
                'order_number' => $order->confirmation_code ?? 'ORD-' . $order->id,
                'original_total' => $order->total_amount,
                'formatted_original_total' => 'RM ' . number_format($order->total_amount, 2),
                'current_total' => $currentTotal,
                'formatted_current_total' => 'RM ' . number_format($currentTotal, 2),
                'price_changed' => $order->total_amount != $currentTotal,
                'available_items' => $availableItems,
                'unavailable_items' => $unavailableItems,
                'has_unavailable_items' => count($unavailableItems) > 0
            ]
        ]);
    }

    /**
     * Add order items to cart (reorder functionality)
     * Use CartController for consistency
     */
    public function addToCart($orderId)
    {
        try {
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json(['error' => 'Please login to add items to cart'], 401);
            }
            
            // Get the order with items
            $order = Order::with(['items.menuItem'])
                ->where('id', $orderId)
                ->where('user_id', $userId)
                ->first();
                
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }
            
            $cartController = new CartController();
            $addedItems = 0;
            $errors = [];
            
            // Loop through each order item and use CartController addItem
            foreach ($order->items as $orderItem) {
                // Skip if menu item no longer exists or not available
                if (!$orderItem->menuItem || !$orderItem->menuItem->availability) {
                    continue;
                }
                
                // Create request object for CartController
                $request = new \Illuminate\Http\Request([
                    'menu_item_id' => $orderItem->menu_item_id,
                    'quantity' => $orderItem->quantity,
                    'special_notes' => $orderItem->special_note
                ]);
                
                // Use existing CartController addItem method
                $response = $cartController->addItem($request);
                $responseData = json_decode($response->getContent(), true);
                
                if ($responseData['success']) {
                    $addedItems++;
                } else {
                    $errors[] = "Failed to add {$orderItem->menuItem->name}";
                }
            }
            
            if ($addedItems > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "{$addedItems} items added to your cart successfully!",
                    'cart_count' => UserCart::getCartCount($userId),
                    'errors' => $errors
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No items could be added to cart (items may be unavailable)',
                    'errors' => $errors
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Reorder addToCart error', [
                'order_id' => $orderId,
                'user_id' => $userId ?? null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to add items to cart. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get order progress data based on status.
     */
    private function getOrderProgress($status)
    {
        switch($status) {
            case 'pending':
                return [
                    'percentage' => 10,
                    'message' => 'Your order is pending confirmation',
                    'estimated_time' => 'Waiting for confirmation'
                ];
            case 'preparing':
                return [
                    'percentage' => 50,
                    'message' => 'Your order is being prepared by our kitchen',
                    'estimated_time' => 'Estimated: 15-20 mins'
                ];
            case 'ready':
                return [
                    'percentage' => 80,
                    'message' => 'Your order is ready for collection/serving',
                    'estimated_time' => 'Ready now!'
                ];
            case 'served':
                return [
                    'percentage' => 95,
                    'message' => 'Your order has been served',
                    'estimated_time' => 'Enjoy your meal!'
                ];
            case 'completed':
                return [
                    'percentage' => 100,
                    'message' => 'Your order is completed',
                    'estimated_time' => 'Thank you!'
                ];
            default:
                return [
                    'percentage' => 0,
                    'message' => 'Order status unknown',
                    'estimated_time' => 'Please contact support'
                ];
        }
    }
}