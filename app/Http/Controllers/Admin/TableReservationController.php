<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableReservation;
use App\Models\Table;
use App\Models\User;
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
            $query->whereDate('reservation_date', $request->date);
        }

        // Filter by table
        if ($request->filled('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        // Search by guest name or confirmation code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('confirmation_code', 'like', "%{$search}%");
            });
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
                             ->orderBy('reservation_time', 'desc')
                             ->paginate(15);

        $tables = Table::where('is_active', true)->get();
        $statuses = ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'];

        return view('admin.table-reservation.index', compact('reservations', 'tables', 'statuses'));
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
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'auto_release_time' => 'nullable|date|after:now',
        ], [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'reservation_date.required' => 'Reservation date is required.',
            'reservation_date.after_or_equal' => 'Reservation date must be today or later.',
            'reservation_time.required' => 'Reservation time is required.',
            'guest_name.required' => 'Guest name is required.',
            'guest_phone.required' => 'Guest phone is required.',
            'number_of_guests.required' => 'Number of guests is required.',
            'number_of_guests.min' => 'Number of guests must be at least 1.',
            'number_of_guests.max' => 'Number of guests cannot exceed 50.',
        ]);

        // Check table availability if table is selected
        if ($request->table_id) {
            $conflictingReservation = $this->checkTableAvailability(
                $request->table_id,
                $request->reservation_date,
                $request->reservation_time
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
        $tableReservation->load(['user', 'table', 'tableSession']);
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
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'auto_release_time' => 'nullable|date|after:now',
        ], [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'reservation_date.required' => 'Reservation date is required.',
            'reservation_time.required' => 'Reservation time is required.',
            'guest_name.required' => 'Guest name is required.',
            'guest_phone.required' => 'Guest phone is required.',
            'number_of_guests.required' => 'Number of guests is required.',
            'number_of_guests.min' => 'Number of guests must be at least 1.',
            'number_of_guests.max' => 'Number of guests cannot exceed 50.',
        ]);

        // Check table availability if table is being changed
        if ($request->table_id && $request->table_id != $tableReservation->table_id) {
            $conflictingReservation = $this->checkTableAvailability(
                $request->table_id,
                $request->reservation_date,
                $request->reservation_time,
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
        $tableReservation->load(['table', 'tableSession']);
        
        $tableReservation->status = $request->status;
        
        if ($request->filled('notes')) {
            $tableReservation->notes = $request->notes;
        }

        $tableReservation->save();

        return redirect()->back()->with('message', 'Reservation status updated successfully!');
    }

    /**
     * Check if table is available at given time
     */
    private function checkTableAvailability($tableId, $date, $time, $excludeId = null)
    {
        $reservationDateTime = Carbon::parse($date . ' ' . $time);
        $bufferMinutes = 120; // 2 hours buffer between reservations

        $query = TableReservation::where('table_id', $tableId)
            ->whereDate('reservation_date', $date)
            ->whereIn('status', ['confirmed', 'seated', 'pending'])
            ->where(function($q) use ($reservationDateTime, $bufferMinutes) {
                $q->whereBetween('reservation_time', [
                    $reservationDateTime->copy()->subMinutes($bufferMinutes)->format('H:i'),
                    $reservationDateTime->copy()->addMinutes($bufferMinutes)->format('H:i')
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
            ->whereDate('reservation_date', $today)
            ->orderBy('reservation_time')
            ->get()
            ->groupBy('status');

        return view('admin.table-reservation.today', compact('reservations'));
    }
}