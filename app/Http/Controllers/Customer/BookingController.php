<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TableReservation;
use App\Models\Table;
use App\Models\UserCart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display booking page.
     */
    public function index()
    {
        $tables = \App\Models\Table::where('is_active', true)->get();
        
        // Get cart data if user is logged in
        $cartItems = [];
        $cartTotal = 0;
        $cartCount = 0;
        
        if (auth()->check()) {
            $cartItems = \App\Models\UserCart::with('menuItem')
                ->where('user_id', auth()->id())
                ->get();
            $cartTotal = \App\Models\UserCart::getCartTotal(auth()->id());
            $cartCount = \App\Models\UserCart::getCartCount(auth()->id());
        }
        
        return view('customer.booking.index', compact('tables', 'cartItems', 'cartTotal', 'cartCount'));
    }

    /**
     * Display customer's booking history.
     */
    public function history()
    {
        $user = Auth::user();
        
        $reservations = TableReservation::with(['table'])
            ->where(function ($query) use ($user) {
                if ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('guest_email', $user->email);
                } else {
                    $query->whereNull('id'); // Return empty if no user
                }
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(10);

        return view('customer.booking.history', compact('reservations'));
    }

    /**
     * Cancel a booking reservation.
     */
    public function cancel(Request $request, $reservationId)
    {
        try {
            $user = Auth::user();
            
            $reservation = TableReservation::with('table')
                ->where('id', $reservationId)
                ->where(function ($query) use ($user) {
                    if ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('guest_email', $user->email);
                    } else {
                        $query->whereNull('id'); // No access if not logged in
                    }
                })
                ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or you do not have permission to cancel this booking.'
                ], 404);
            }

            // Check if booking can be cancelled
            if (!in_array($reservation->status, ['confirmed', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled. Status: ' . $reservation->status
                ], 400);
            }

            // Check if booking date is in the future (allow same day cancellation)
            $bookingDateTime = $reservation->booking_date->setTimeFromTimeString($reservation->booking_time);
            if ($bookingDateTime->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel past bookings.'
                ], 400);
            }

            DB::beginTransaction();

            // Update reservation status to cancelled
            $reservation->update([
                'status' => 'cancelled',
                'notes' => ($reservation->notes ? $reservation->notes . "\n\n" : '') . 
                          'Cancelled by customer on ' . now()->format('Y-m-d H:i:s')
            ]);

            // If table status is reserved for this booking, make it available
            if ($reservation->table && $reservation->table->status === 'reserved') {
                $reservation->table->update(['status' => 'available']);
            }

            // Cancel associated order if exists
            $order = Order::where('reservation_id', $reservation->id)
                         ->where('order_status', '!=', 'completed')
                         ->first();
            
            if ($order) {
                $order->update([
                    'order_status' => 'cancelled',
                    'notes' => ($order->notes ? $order->notes . "\n\n" : '') . 
                              'Order cancelled due to booking cancellation on ' . now()->format('Y-m-d H:i:s')
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.',
                'reservation' => [
                    'id' => $reservation->id,
                    'confirmation_code' => $reservation->confirmation_code,
                    'status' => 'cancelled',
                    'booking_date' => $reservation->booking_date->format('Y-m-d'),
                    'booking_time' => $reservation->booking_time,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Store a new booking reservation.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'table_id' => 'required|exists:tables,table_number',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'party_size' => 'required|integer|min:1|max:12',
            'booking_type' => 'required|in:with-menu,table-only',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            // Get the table by table_number
            $table = Table::where('table_number', $request->table_id)->first();
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Table not found'], 404);
            }

            // Get user info - use form data for guest info
            $user = Auth::user();
            $guestName = $request->guest_name;
            $guestEmail = $request->guest_email;
            $guestPhone = $request->guest_phone;

            // Create the table reservation
            $reservation = TableReservation::create([
                'user_id' => $user ? $user->id : null,
                'table_id' => $table->id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
                'guest_phone' => $guestPhone,
                'party_size' => $request->party_size,
                'status' => 'confirmed',
                'special_requests' => $request->special_requests ?? null,
            ]);

            // If booking with menu, create an order with cart items
            if ($request->booking_type === 'with-menu' && $user) {
                $cartItems = UserCart::with('menuItem')
                    ->where('user_id', $user->id)
                    ->get();

                if ($cartItems->count() > 0) {
                    // Calculate total
                    $totalAmount = UserCart::getCartTotal($user->id);
                    
                    // Add VVIP booking fee if applicable
                    $bookingFee = 0;
                    if ($table->table_type === 'vip') {
                        $bookingFee = 50.00;
                        $totalAmount += $bookingFee;
                    }

                    // Create order
                    $order = Order::create([
                        'user_id' => $user->id,
                        'table_id' => $table->id,
                        'table_number' => $table->table_number,
                        'order_type' => 'dine_in',
                        'order_status' => 'pending',
                        'payment_status' => 'unpaid',
                        'total_amount' => $totalAmount,
                        'reservation_id' => $reservation->id,
                        'confirmation_code' => Order::generateConfirmationCode(),
                    ]);

                    // Create order items from cart
                    foreach ($cartItems as $cartItem) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'menu_item_id' => $cartItem->menu_item_id,
                            'quantity' => $cartItem->quantity,
                            'unit_price' => $cartItem->unit_price,
                            'total_price' => $cartItem->quantity * $cartItem->unit_price,
                            'special_note' => $cartItem->special_notes,
                        ]);
                    }

                    // Clear the cart
                    UserCart::where('user_id', $user->id)->delete();
                }
            }

            // Update table status to reserved
            $table->update(['status' => 'reserved']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully!',
                'reservation' => [
                    'confirmation_code' => $reservation->confirmation_code,
                    'booking_date' => $reservation->booking_date->format('Y-m-d'),
                    'booking_time' => $reservation->booking_time,
                    'table_number' => $table->table_number,
                ],
                'redirect_url' => route('customer.orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}