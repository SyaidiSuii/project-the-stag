<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableQrcode;
use App\Models\Table;
use App\Models\TableReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Events\QRSessionCompletedEvent;

class TableQrcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TableQrcode::with(['table', 'reservation', 'orders']);

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
                         ->paginate(10);

        $tables = Table::where('is_active', true)->get();
        $statuses = ['active', 'completed', 'expired'];

        return view('admin.table-qr_code.index', compact('sessions', 'tables', 'statuses'));
    }

    /**
     * Active sessions dashboard
     */
    public function active()
    {
        $activeSessions = TableQrcode::with(['table', 'reservation', 'orders'])
            ->active()
            ->orderBy('started_at', 'asc')
            ->get();

        return view('admin.table-qr_code.active', compact('activeSessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Exclude VVIP tables from QR code generation
        $tables = Table::available()
                    ->where('table_type', '!=', 'vip')
                    ->get();
        $tableQrcode = new TableQrcode();
        
        return view('admin.table-qr_code.create', compact('tables', 'tableQrcode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'started_at' => 'required|date|after_or_equal:' . now()->subMinutes(2)->toDateTimeString(),
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'guest_count' => 'nullable|integer|min:1|max:50',
            'expires_at' => 'nullable|date|after:started_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        $table = Table::findOrFail($request->table_id);
        
        // Prevent QR generation for VVIP tables
        if ($table->table_type === 'vip') {
            return redirect()->back()
                ->withErrors(['table_id' => 'QR menu generation is not available for VVIP tables.'])
                ->withInput();
        }
        
        // Create new session
        $session = $table->createSession([
            'started_at' => Carbon::parse($request->started_at),
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'guest_count' => $request->guest_count,
            'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : Carbon::parse($request->started_at)->addHours(4),
            'notes' => $request->notes,
        ]);

        // Note: Table status is NOT changed when QR session is created
        // Status 'occupied' is only set when booking is confirmed

        return redirect()->route('admin.table-qrcodes.create')
                        ->with('message', 'Table session created successfully!')
                        ->with('qr_session', $session);
    }

    /**
     * Display the specified resource.
     */
    public function show(TableQrcode $tableQrcode)
    {
        $tableQrcode->load(['table', 'reservation', 'orders.items.menuItem']);
        
        return view('admin.table-qr_code.show', compact('tableQrcode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableQrcode $tableQrcode)
    {
        $tables = Table::where('is_active', true)->get();
        
        return view('admin.table-qr_code.edit', compact('tableQrcode', 'tables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableQrcode $tableQrcode)
    {
        $request->validate([
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:20', 
            'guest_count' => 'nullable|integer|min:1|max:50',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $tableQrcode->update($request->only([
            'guest_name',
            'guest_phone',
            'guest_count',
            'expires_at',
            'notes'
        ]));

        return redirect()->route('admin.table-qrcodes.show', $tableQrcode)
                        ->with('message', 'Session updated successfully!');
    }

    /**
     * Complete a table session
     */
    public function complete(TableQrcode $tableQrcode)
    {
        $tableQrcode->complete();
        
        // Dispatch event for analytics tracking
        Log::info('Dispatching QRSessionCompletedEvent', ['session_id' => $tableQrcode->id]);
        event(new QRSessionCompletedEvent($tableQrcode));

        // Set table back to available if no other active sessions
        $table = $tableQrcode->table;
        if (!$table->hasActiveSession()) {
            $table->update(['status' => 'available']);
        }

        // Check if this is an AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Session completed successfully!'
            ]);
        }

        return redirect()->back()
                        ->with('message', 'Session completed successfully!');
    }

    /**
     * Extend session expiry time
     */
    public function extend(Request $request, TableQrcode $tableQrcode)
    {
        $request->validate([
            'hours' => 'required|integer|min:1|max:12'
        ]);

        $tableQrcode->extend($request->hours);

        // Check if this is an AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Session extended by {$request->hours} hour(s)!"
            ]);
        }

        return redirect()->back()
                        ->with('message', "Session extended by {$request->hours} hour(s)!");
    }

    /**
     * Generate new QR code for session
     */
    public function regenerateQR(TableQrcode $tableQrcode)
    {
        $success = $tableQrcode->regenerateQRCode();

        // Check if this is an AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'QR code regenerated successfully with new session code!',
                    'new_session_code' => $tableQrcode->session_code
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to regenerate QR code. Please try again.'
                ], 500);
            }
        }

        if ($success) {
            return redirect()->back()
                            ->with('message', 'QR code regenerated successfully with new session code!');
        } else {
            return redirect()->back()
                            ->with('error', 'Failed to regenerate QR code. Please try again.');
        }
    }

    /**
     * Get QR code data for display/download
     */
    public function qrCode(TableQrcode $tableQrcode)
    {
        if (!$tableQrcode->isActive()) {
            abort(404, 'Session not active');
        }

        return response()->json([
            'session_code' => $tableQrcode->session_code,
            'qr_url' => $tableQrcode->qr_code_url,
            'qr_data' => $tableQrcode->qr_code_data,
            'qr_png' => $tableQrcode->qr_code_png ? asset('storage/' . $tableQrcode->qr_code_png) : null,
            'qr_svg' => $tableQrcode->qr_code_svg ? asset('storage/' . $tableQrcode->qr_code_svg) : null,
            'table_number' => $tableQrcode->table->table_number,
            'expires_at' => $tableQrcode->expires_at,
            'time_remaining' => $tableQrcode->time_remaining,
        ]);
    }

    /**
     * Download QR code image (PNG or SVG) - Protected route for staff/admin only
     */
    public function downloadQR(TableQrcode $tableQrcode, $format)
    {
        // Validate format
        if (!in_array($format, ['png', 'svg'])) {
            abort(400, 'Invalid format. Use png or svg.');
        }

        // Check if session is active
        if (!$tableQrcode->isActive()) {
            abort(404, 'Session not active or expired');
        }

        // Get the file path based on format
        $filePath = $format === 'svg' ? $tableQrcode->qr_code_svg : $tableQrcode->qr_code_png;
        
        if (!$filePath || !\Storage::disk('public')->exists($filePath)) {
            abort(404, 'QR code file not found');
        }

        // Get full storage path
        $fullPath = storage_path('app/public/' . $filePath);
        
        // Generate download filename
        $filename = "table_{$tableQrcode->table->table_number}_qr.{$format}";
        
        return response()->download($fullPath, $filename);
    }

    /**
     * Print QR code flyer page
     */
    public function printQR(TableQrcode $tableQrcode)
    {
        // Check if session is active
        if (!$tableQrcode->isActive()) {
            abort(404, 'Session not active or expired');
        }

        return view('admin.table-qr_code.print', compact('tableQrcode'));
    }

    /**
     * Preview QR code image in browser
     */
    public function previewQR(TableQrcode $tableQrcode, $format = 'png')
    {
        // Validate format
        if (!in_array($format, ['png', 'svg'])) {
            $format = 'png';
        }

        // Check if session is active
        if (!$tableQrcode->isActive()) {
            abort(404, 'Session not active or expired');
        }

        // Get the file path based on format
        $filePath = $format === 'svg' ? $tableQrcode->qr_code_svg : $tableQrcode->qr_code_png;
        
        if (!$filePath || !\Storage::disk('public')->exists($filePath)) {
            abort(404, 'QR code file not found');
        }

        // Get file content
        $content = \Storage::disk('public')->get($filePath);
        
        // Set appropriate content type
        $contentType = $format === 'svg' ? 'image/svg+xml' : 'image/png';
        
        return response($content, 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Expire old sessions (for cron job)
     */
    public function expireOldSessions()
    {
        $expiredCount = TableQrcode::where('status', 'active')
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
    public function destroy(TableQrcode $tableQrcode)
    {
        // Can only delete if no orders associated
        if ($tableQrcode->orders()->count() > 0) {
            return redirect()->back()
                           ->withErrors(['error' => 'Cannot delete session with existing orders.']);
        }

        $table = $tableQrcode->table;
        $tableQrcode->delete();
        
        // Update table status if needed
        if (!$table->hasActiveSession()) {
            $table->update(['status' => 'available']);
        }

        return redirect()->route('admin.table-qrcodes.index')
                        ->with('message', 'Session deleted successfully!');
    }
}
