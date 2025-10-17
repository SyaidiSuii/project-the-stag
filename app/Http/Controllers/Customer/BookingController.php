<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TableReservation;
use App\Models\Table;
use App\Models\UserCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableLayoutSetting;
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
        // Get all active tables with their active QR sessions
        $tables = \App\Models\Table::where('is_active', true)
            ->with(['currentSession'])
            ->get()
            ->map(function ($table) {
                // Check if table has an active QR session
                if ($table->currentSession && $table->currentSession->isActive()) {
                    // Mark table as occupied if it has an active QR session
                    $table->status = 'occupied';
                }
                return $table;
            });

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

        // Get saved layout dimensions from admin
        $layoutSetting = TableLayoutSetting::getOrCreate('main_layout', 1200, 600);

        return view('customer.booking.index', compact('tables', 'cartItems', 'cartTotal', 'cartCount', 'layoutSetting'));
    }

    /**
     * Check availability for a specific table at a given date/time.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,table_number',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'party_size' => 'nullable|integer|min:1|max:12'
        ]);

        try {
            $table = Table::where('table_number', $request->table_id)
                ->with('currentSession')
                ->first();

            if (!$table) {
                return response()->json([
                    'available' => false,
                    'message' => 'Table not found'
                ]);
            }

            // Check party size vs capacity
            if ($request->party_size && $request->party_size > $table->capacity) {
                return response()->json([
                    'available' => false,
                    'message' => "Party size exceeds table capacity ({$table->capacity} guests)",
                    'reason' => 'capacity_exceeded'
                ]);
            }

            // Check for active QR session
            if ($table->currentSession && $table->currentSession->isActive()) {
                return response()->json([
                    'available' => false,
                    'message' => 'Table is currently occupied',
                    'reason' => 'qr_session_active'
                ]);
            }

            // Check table status
            if (!in_array($table->status, ['available'])) {
                return response()->json([
                    'available' => false,
                    'message' => 'Table is not available: ' . $table->status,
                    'reason' => 'status_unavailable'
                ]);
            }

            // Check for reservation conflicts
            $requestedTime = \Carbon\Carbon::parse($request->booking_time);
            $bufferMinutes = 120;

            $conflictingReservation = TableReservation::where('table_id', $table->id)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['confirmed', 'pending'])
                ->where(function ($query) use ($requestedTime, $bufferMinutes) {
                    $query->whereRaw("ABS(TIME_TO_SEC(TIMEDIFF(booking_time, ?))) < ?", [
                        $requestedTime->format('H:i:s'),
                        $bufferMinutes * 60
                    ]);
                })
                ->first();

            if ($conflictingReservation) {
                return response()->json([
                    'available' => false,
                    'message' => "Already reserved for {$conflictingReservation->booking_time}",
                    'reason' => 'time_conflict',
                    'conflicting_time' => $conflictingReservation->booking_time
                ]);
            }

            // Get available time slots for this table on this date
            $availableSlots = $this->getAvailableTimeSlots($table->id, $request->booking_date);

            return response()->json([
                'available' => true,
                'message' => 'Table is available',
                'table' => [
                    'id' => $table->table_number,
                    'capacity' => $table->capacity,
                    'type' => $table->table_type
                ],
                'available_slots' => $availableSlots
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => 'Error checking availability',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get available time slots for a table on a specific date.
     */
    private function getAvailableTimeSlots($tableId, $date)
    {
        $timeSlots = [
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00',
            '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00'
        ];

        $bookedSlots = TableReservation::where('table_id', $tableId)
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->pluck('booking_time')
            ->toArray();

        $availableSlots = [];
        $bufferMinutes = 120;

        foreach ($timeSlots as $slot) {
            $slotTime = \Carbon\Carbon::parse($slot);
            $isAvailable = true;

            foreach ($bookedSlots as $bookedTime) {
                $bookedTimeParsed = \Carbon\Carbon::parse($bookedTime);
                $diffInMinutes = abs($slotTime->diffInMinutes($bookedTimeParsed));

                if ($diffInMinutes < $bufferMinutes) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
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

            // Get the table by table_number with its current session
            $table = Table::where('table_number', $request->table_id)
                ->with('currentSession')
                ->first();

            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Table not found'], 404);
            }

            // Validate party size against table capacity
            if ($request->party_size > $table->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => "Party size ({$request->party_size}) exceeds table capacity ({$table->capacity} guests). Please select a larger table."
                ], 400);
            }

            // Validate booking time is within business hours (11:00 AM - 9:00 PM)
            $bookingTime = \Carbon\Carbon::parse($request->booking_time);
            $openingTime = \Carbon\Carbon::parse('11:00');
            $closingTime = \Carbon\Carbon::parse('21:00');

            if ($bookingTime->lt($openingTime) || $bookingTime->gt($closingTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking time must be between 11:00 AM and 9:00 PM.'
                ], 400);
            }

            // Validate booking lead time (at least 1 hour in advance)
            $bookingDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->booking_time);
            $minBookingTime = now()->addHour();

            if ($bookingDateTime->lt($minBookingTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservations must be made at least 1 hour in advance. Please select a later time.'
                ], 400);
            }

            // Check if table has an active QR session
            if ($table->currentSession && $table->currentSession->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table is currently occupied with an active QR session. Please select another table.'
                ], 400);
            }

            // Check if table is available for booking
            if (!in_array($table->status, ['available'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table is not available for booking. Status: ' . $table->status
                ], 400);
            }

            // CRITICAL: Check for reservation conflicts (prevent double booking)
            // A table is considered booked if there's a reservation within 2 hours of the requested time
            $conflictingReservation = TableReservation::where('table_id', $table->id)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['confirmed', 'pending'])
                ->where(function ($query) use ($request) {
                    $requestedTime = \Carbon\Carbon::parse($request->booking_time);
                    $bufferMinutes = 120; // 2-hour buffer between reservations

                    $query->where(function ($q) use ($requestedTime, $bufferMinutes) {
                        // Check if existing reservation overlaps with requested time
                        $q->whereRaw("ABS(TIME_TO_SEC(TIMEDIFF(booking_time, ?))) < ?", [
                            $requestedTime->format('H:i:s'),
                            $bufferMinutes * 60
                        ]);
                    });
                })
                ->first();

            if ($conflictingReservation) {
                return response()->json([
                    'success' => false,
                    'message' => "This table is already reserved for {$conflictingReservation->booking_time} on this date. Please choose a different time or table."
                ], 400);
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