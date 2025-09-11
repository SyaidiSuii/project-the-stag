<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCart;
use App\Models\MenuItem;

class CartController extends Controller
{
    // Get cart items for logged in user
    public function index()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $cartItems = UserCart::with('menuItem')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->menu_item_id,
                    'name' => $item->menuItem->name,
                    'price' => 'RM ' . number_format($item->unit_price, 2),
                    'quantity' => $item->quantity,
                    'notes' => $item->special_notes,
                    'total' => $item->total_price
                ];
            });

        return response()->json([
            'success' => true,
            'cart' => $cartItems,
            'total' => UserCart::getCartTotal(Auth::id()),
            'count' => UserCart::getCartCount(Auth::id())
        ]);
    }

    // Add item to cart
    public function addItem(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'special_notes' => 'nullable|string'
        ]);

        $menuItem = MenuItem::find($validated['menu_item_id']);

        // Check if item already exists in cart (including soft deleted)
        $cartItem = UserCart::withTrashed()
            ->where('user_id', Auth::id())
            ->where('menu_item_id', $validated['menu_item_id'])
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
            $cartItem->save();
        } else {
            // Create new cart item
            UserCart::create([
                'user_id' => Auth::id(),
                'menu_item_id' => $validated['menu_item_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $menuItem->price,
                'special_notes' => $validated['special_notes'] ?? null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => UserCart::getCartCount(Auth::id())
        ]);
    }

    // Update cart item quantity
    public function updateItem(Request $request, $menuItemId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $cartItem = UserCart::where('user_id', Auth::id())
            ->where('menu_item_id', $menuItemId)
            ->first();

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found in cart'], 404);
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

    // Remove item from cart
    public function removeItem($menuItemId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

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

    // Clear entire cart
    public function clearCart()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        UserCart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    // Merge localStorage cart with database cart (for login)
    public function mergeCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validated = $request->validate([
            'cart_items' => 'required|array',
            'cart_items.*.id' => 'required|exists:menu_items,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.notes' => 'nullable|string'
        ]);

        foreach ($validated['cart_items'] as $item) {
            $menuItem = MenuItem::find($item['id']);
            
            $cartItem = UserCart::withTrashed()
                ->where('user_id', Auth::id())
                ->where('menu_item_id', $item['id'])
                ->first();

            if ($cartItem) {
                if ($cartItem->trashed()) {
                    // Restore soft deleted item and set quantity
                    $cartItem->restore();
                    $cartItem->quantity = $item['quantity'];
                } else {
                    // Add quantities if item exists and is not soft deleted
                    $cartItem->quantity += $item['quantity'];
                }
                $cartItem->save();
            } else {
                // Create new cart item
                UserCart::create([
                    'user_id' => Auth::id(),
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'special_notes' => $item['notes'] ?? null
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart merged successfully',
            'cart_count' => UserCart::getCartCount(Auth::id())
        ]);
    }
}
