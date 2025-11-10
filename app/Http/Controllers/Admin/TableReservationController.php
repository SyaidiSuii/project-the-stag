<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableReservation;
use App\Models\Table;
use App\Models\User;
use App\Events\TableBookingCreatedEvent;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TableReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request('cancel')) {
            return redirect()->route('admin.table-reservation.index');
        }

        $query = TableReservation::with(['user', 'table']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        // Filter by table
        if ($request->filled('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        // Search by customer name, phone, or confirmation code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('confirmation_code', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reservations = $query->orderByRaw("
            CASE
                WHEN status = 'pending' THEN 1
                WHEN status = 'confirmed' THEN 2
                WHEN status = 'seated' THEN 3
                ELSE 4
            END
        ")
                          ->orderBy('booking_date', 'asc')
                          ->orderBy('booking_time', 'asc')
                          ->paginate(15);

        // Get statistics for dashboard cards
        $tables = Table::where('is_active', true)->get();
        $statuses = ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'];
        $totalTables = Table::count();
        $pendingTables = TableReservation::where('status', 'pending')->count();
        $confirmedTables = TableReservation::where('status', 'confirmed')->count();
        $todayBooking = TableReservation::whereDate('created_at', now())->count();

        return view('admin.table-reservation.index', compact('reservations', 'tables', 'statuses', 'totalTables', 'pendingTables', 'confirmedTables', 'todayBooking'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $reservation = new TableReservation;
        $tables = Table::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('admin.table-reservation.form', compact('reservation', 'tables', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'nullable|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'guest_name' => 'required_without:user_id|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'party_size' => 'required|integer|min:1|max:50',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ], [
            'user_id.exists' => 'Selected user does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'booking_date.required' => 'Booking date is required.',
            'booking_date.after_or_equal' => 'Booking date must be today or later.',
            'booking_time.required' => 'Booking time is required.',
            'guest_name.required_without' => 'Guest name is required when no user is selected.',
            'guest_phone.required' => 'Phone number is required.',
            'party_size.required' => 'Party size is required.',
            'party_size.min' => 'Party size must be at least 1.',
            'party_size.max' => 'Party size cannot exceed 50.',
        ]);

        // Check table availability if table is selected
        if ($request->table_id) {
            $conflictingReservation = $this->checkTableAvailability(
                $request->table_id,
                $request->booking_date,
                $request->booking_time
            );

            if ($conflictingReservation) {
                return back()->withErrors([
                    'table_id' => 'Table is not available at the selected time. Conflicting reservation: ' . $conflictingReservation->confirmation_code
                ])->withInput();
            }
        }

        $reservation = new TableReservation;
        $reservation->fill($request->all());

        $reservation->save();

        return redirect()->route('admin.table-reservation.index')->with('message', 'Reservation has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TableReservation $tableReservation)
    {
        $tableReservation->load(['user', 'table', 'tableQrcode']);
        return view('admin.table-reservation.show', compact('tableReservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableReservation $tableReservation)
    {
       $reservation = $tableReservation; // Assign ke variable baru
        $tables = Table::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('admin.table-reservation.form', compact('reservation', 'tables', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableReservation $tableReservation)
    {
        $this->validate($request, [
            'user_id' => 'nullable|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required|date_format:H:i',
            'guest_name' => 'required_without:user_id|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'party_size' => 'required|integer|min:1|max:50',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ], [
            'user_id.exists' => 'Selected user does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'booking_date.required' => 'Booking date is required.',
            'booking_time.required' => 'Booking time is required.',
            'guest_name.required_without' => 'Guest name is required when no user is selected.',
            'guest_phone.required' => 'Phone number is required.',
            'party_size.required' => 'Party size is required.',
            'party_size.min' => 'Party size must be at least 1.',
            'party_size.max' => 'Party size cannot exceed 50.',
        ]);

        // Check table availability if table is being changed
        if ($request->table_id && $request->table_id != $tableReservation->table_id) {
            $conflictingReservation = $this->checkTableAvailability(
                $request->table_id,
                $request->booking_date,
                $request->booking_time,
                $tableReservation->id
            );

            if ($conflictingReservation) {
                return back()->withErrors([
                    'table_id' => 'Table is not available at the selected time. Conflicting reservation: ' . $conflictingReservation->confirmation_code
                ])->withInput();
            }
        }

        $tableReservation->fill($request->all());
        
        $tableReservation->save();

        return redirect()->route('admin.table-reservation.index')->with('message', 'Reservation has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableReservation $tableReservation)
    {
        $tableReservation->delete();
        return redirect()->route('admin.table-reservation.index')->with('message', 'Reservation has been deleted successfully!');
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, TableReservation $tableReservation)
    {
        $this->validate($request, [
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        // Load relationships to prevent errors
        $tableReservation->load(['table', 'tableQrcode', 'user']);

        // Track old status for comparison
        $oldStatus = $tableReservation->status;

        $tableReservation->status = $request->status;

        if ($request->filled('notes')) {
            $tableReservation->notes = $request->notes;
        }

        $tableReservation->save();

        // Fire event to send FCM notification when admin confirms reservation
        if ($oldStatus !== 'confirmed' && $request->status === 'confirmed') {
            event(new \App\Events\TableBookingConfirmedEvent($tableReservation->fresh(['user', 'table'])));

            \Log::info('Reservation confirmed - confirmation event fired', [
                'reservation_id' => $tableReservation->id,
                'user_id' => $tableReservation->user_id,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ]);
        }

        return redirect()->back()->with('message', 'Reservation status updated successfully!');
    }

    /**
     * Check if table is available at given time
     */
    private function checkTableAvailability($tableId, $date, $time, $excludeId = null)
    {
        $bookingDateTime = Carbon::parse($date . ' ' . $time);
        $bufferMinutes = 120; // 2 hours buffer between reservations

        $query = TableReservation::where('table_id', $tableId)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['confirmed', 'seated', 'pending'])
            ->where(function($q) use ($bookingDateTime, $bufferMinutes) {
                $q->whereBetween('booking_time', [
                    $bookingDateTime->copy()->subMinutes($bufferMinutes)->format('H:i'),
                    $bookingDateTime->copy()->addMinutes($bufferMinutes)->format('H:i')
                ]);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * Get today's reservations for dashboard
     */
    public function todayReservations()
    {
        $today = today();
        
        $reservations = TableReservation::with(['table', 'user'])
            ->whereDate('booking_date', $today)
            ->orderBy('booking_time')
            ->get()
            ->groupBy('status');

        return view('admin.table-reservation.today', compact('reservations'));
    }
}