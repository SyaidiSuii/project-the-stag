<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableSession;
use App\Models\Table;
use App\Models\TableReservation;
use Carbon\Carbon;

class TableSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TableSession::with(['table', 'reservation', 'orders']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by table
        if ($request->filled('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('started_at', $request->date);
        }

        // Search by session code or guest info
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('session_code', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%");
            });
        }

        $sessions = $query->orderBy('started_at', 'desc')
                         ->paginate(15);

        $tables = Table::where('is_active', true)->get();
        $statuses = ['active', 'completed', 'expired'];

        return view('admin.table-sessions.index', compact('sessions', 'tables', 'statuses'));
    }

    /**
     * Active sessions dashboard
     */
    public function active()
    {
        $activeSessions = TableSession::with(['table', 'reservation', 'orders'])
            ->active()
            ->orderBy('started_at', 'asc')
            ->get();

        return view('admin.table-sessions.active', compact('activeSessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tables = Table::available()->get();
        $session = new TableSession();
        
        return view('admin.table-sessions.create', compact('tables', 'session'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'guest_count' => 'nullable|integer|min:1|max:50',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $table = Table::findOrFail($request->table_id);
        
        // Create new session
        $session = $table->createSession([
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'guest_count' => $request->guest_count,
            'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : now()->addHours(4),
            'notes' => $request->notes,
        ]);

        // Update table status to occupied
        $table->update(['status' => 'occupied']);

        return redirect()->route('admin.table-sessions.index')
                        ->with('message', 'Table session created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TableSession $tableSession)
    {
        $tableSession->load(['table', 'reservation', 'orders.items.menuItem']);
        
        return view('admin.table-sessions.show', compact('tableSession'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableSession $tableSession)
    {
        $tables = Table::where('is_active', true)->get();
        
        return view('admin.table-sessions.edit', compact('tableSession', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableSession $tableSession)
    {
        $request->validate([
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:20', 
            'guest_count' => 'nullable|integer|min:1|max:50',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $tableSession->update($request->only([
            'guest_name',
            'guest_phone',
            'guest_count',
            'expires_at',
            'notes'
        ]));

        return redirect()->route('admin.table-sessions.show', $tableSession)
                        ->with('message', 'Session updated successfully!');
    }

    /**
     * Complete a table session
     */
    public function complete(TableSession $tableSession)
    {
        $tableSession->complete();
        
        // Set table back to available if no other active sessions
        $table = $tableSession->table;
        if (!$table->hasActiveSession()) {
            $table->update(['status' => 'available']);
        }

        return redirect()->back()
                        ->with('message', 'Session completed successfully!');
    }

    /**
     * Extend session expiry time
     */
    public function extend(Request $request, TableSession $tableSession)
    {
        $request->validate([
            'hours' => 'required|integer|min:1|max:12'
        ]);

        $tableSession->extend($request->hours);

        return redirect()->back()
                        ->with('message', "Session extended by {$request->hours} hour(s)!");
    }

    /**
     * Generate new QR code for session
     */
    public function regenerateQR(TableSession $tableSession)
    {
        $tableSession->generateQRCode();

        return redirect()->back()
                        ->with('message', 'QR code regenerated successfully!');
    }

    /**
     * Get QR code data for display/download
     */
    public function qrCode(TableSession $tableSession)
    {
        if (!$tableSession->isActive()) {
            abort(404, 'Session not active');
        }

        return response()->json([
            'session_code' => $tableSession->session_code,
            'qr_url' => $tableSession->qr_code_url,
            'qr_data' => $tableSession->qr_code_data,
            'table_number' => $tableSession->table->table_number,
            'expires_at' => $tableSession->expires_at,
            'time_remaining' => $tableSession->time_remaining,
        ]);
    }

    /**
     * Expire old sessions (for cron job)
     */
    public function expireOldSessions()
    {
        $expiredCount = TableSession::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        // Update table status for tables with no active sessions
        $tablesWithExpiredSessions = Table::whereHas('sessions', function($query) {
            $query->where('status', 'expired')
                  ->where('expires_at', '<=', now());
        })->whereDoesntHave('sessions', function($query) {
            $query->active();
        })->get();

        foreach ($tablesWithExpiredSessions as $table) {
            $table->update(['status' => 'available']);
        }

        return response()->json([
            'message' => "Expired {$expiredCount} sessions",
            'expired_sessions' => $expiredCount,
            'tables_freed' => $tablesWithExpiredSessions->count()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableSession $tableSession)
    {
        // Can only delete if no orders associated
        if ($tableSession->orders()->count() > 0) {
            return redirect()->back()
                           ->withErrors(['error' => 'Cannot delete session with existing orders.']);
        }

        $table = $tableSession->table;
        $tableSession->delete();
        
        // Update table status if needed
        if (!$table->hasActiveSession()) {
            $table->update(['status' => 'available']);
        }

        return redirect()->route('admin.table-sessions.index')
                        ->with('message', 'Session deleted successfully!');
    }
}
