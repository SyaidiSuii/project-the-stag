<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User; // Assuming you might need to create a guest user or link to an existing one
use App\Models\UserCart;

class PaymentController extends Controller
{
    public function index()
    {
        return view('customer.payment.index');
    }

    public function placeOrder(Request $request)
    {
        // Debug log
        logger()->info('Payment submission received', ['data' => $request->all()]);
        
        // Basic validation
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_details' => 'required|array',
            'payment_details.method' => 'required|string|in:card,wallet,cash',
            'payment_details.email' => 'nullable|email',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = $validated['cart'];
            $paymentDetails = $validated['payment_details'];
            $totalAmount = 0;

            // Calculate total amount from the backend to prevent tampering
            foreach ($cartItems as $item) {
                $menuItem = \App\Models\MenuItem::find($item['id']);
                $totalAmount += $menuItem->price * $item['quantity'];
            }

            // Create the Order
            $order = Order::create([
                'user_id' => $user ? $user->id : null, // Handle guest users
                'guest_name' => $user ? $user->name : null,
                'guest_phone' => $user ? $user->phone : null,
                'total_amount' => $totalAmount,
                'order_status' => 'pending', // Changed to confirmed since payment is processed
                'payment_status' => 'paid', // Set as paid since payment is being processed
                'order_type' => 'takeaway', // Fixed: use valid enum value
                'order_source' => 'web', // Fixed: use valid enum value
                'order_time' => now(),
                'confirmation_code' => Order::generateConfirmationCode(), // Generate confirmation code
                // Add other necessary fields like table_session_id if applicable
            ]);

            // Create OrderItems
            foreach ($cartItems as $item) {
                $menuItem = \App\Models\MenuItem::find($item['id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'total_price' => $menuItem->price * $item['quantity'],
                    'special_note' => $item['notes'] ?? null,
                    'item_status' => 'pending',
                ]);
            }

            // Clear user's cart if logged in
            if ($user) {
                UserCart::where('user_id', $user->id)->delete();
            }

            DB::commit();

            // Generate a unique order ID for display
            $displayOrderId = 'STG-' . $order->created_at->format('Ymd') . '-' . $order->id;

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $displayOrderId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception with full details
            logger()->error('Order placement failed: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while placing your order. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
