<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderEtas;
use App\Models\OrderTracking;
use App\Models\User;
use App\Models\Table;
use App\Models\TableReservation;
use App\Models\MenuItem;
use Illuminate\Validation\Rule;
use App\Events\AnalyticsRefreshEvent;
use App\Services\Kitchen\OrderDistributionService;
use App\Services\Kitchen\KitchenLoadService;

class OrderController extends Controller
{
    /**
     * Show today's orders
     */
    public function today()
    {
        $orders = Order::with(['user', 'table', 'reservation', 'items'])
            ->whereDate('order_time', today())
            ->orderBy('order_time', 'desc')
            ->get()
            ->groupBy('order_status');

        return view('admin.order.today', compact('orders'));
    }

    /**
     * Kitchen Display System - Full screen view for kitchen staff
     */
    public function kds(Request $request)
    {
        $user = $request->user();
        $stationId = $request->get('station_id');

        // Check if user is kitchen staff (restrict access)
        $isKitchenStaff = $user->isKitchenStaff();

        // Kitchen staff MUST use their assigned station only
        if ($isKitchenStaff) {
            // Force kitchen staff to their assigned station
            if ($user->assigned_station_id) {
                $stationId = $user->assigned_station_id;
            } else {
                // If kitchen staff has no assigned station, redirect to error
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You must be assigned to a station to access the KDS. Please contact an administrator.');
            }
        } else {
            // For admin/manager, allow station selection or auto-assign if they have one
            if (!$stationId && $user->assigned_station_id) {
                $stationId = $user->assigned_station_id;
            }
        }

        // Get active orders (not completed or cancelled)
        $query = Order::with([
            'user',
            'table',
            'reservation',
            'items.menuItem.category',
            'stationAssignments.station',
            'kitchenLoads.station'
        ])
        ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])
        ->orderBy('order_time', 'asc');

        // Filter by station if specified or auto-assigned
        if ($stationId) {
            $query->whereHas('stationAssignments', function ($q) use ($stationId) {
                $q->where('station_id', $stationId);
            });
        }

        $orders = $query->get()->groupBy('order_status');

        // Get all active kitchen stations
        // Kitchen staff can only see their assigned station
        if ($isKitchenStaff && $user->assigned_station_id) {
            $stations = \App\Models\KitchenStation::where('is_active', true)
                ->where('id', $user->assigned_station_id)
                ->ordered()
                ->withCount(['activeLoads', 'pendingAssignments'])
                ->get();
        } else {
            $stations = \App\Models\KitchenStation::where('is_active', true)
                ->ordered()
                ->withCount(['activeLoads', 'pendingAssignments'])
                ->get();
        }

        // Get today's stats (scoped to station for kitchen staff)
        if ($stationId) {
            $todayStats = [
                'total_orders' => Order::whereDate('order_time', today())
                    ->whereHas('stationAssignments', function ($q) use ($stationId) {
                        $q->where('station_id', $stationId);
                    })->count(),
                'pending' => $orders->get('pending', collect())->count(),
                'preparing' => $orders->get('preparing', collect())->count(),
                'ready' => $orders->get('ready', collect())->count(),
                'completed_today' => Order::whereDate('order_time', today())
                    ->where('order_status', 'completed')
                    ->whereHas('stationAssignments', function ($q) use ($stationId) {
                        $q->where('station_id', $stationId);
                    })->count(),
            ];
        } else {
            $todayStats = [
                'total_orders' => Order::whereDate('order_time', today())->count(),
                'pending' => $orders->get('pending', collect())->count(),
                'preparing' => $orders->get('preparing', collect())->count(),
                'ready' => $orders->get('ready', collect())->count(),
                'completed_today' => Order::whereDate('order_time', today())
                    ->where('order_status', 'completed')
                    ->count(),
            ];
        }

        // Get current station name if filtered
        $currentStation = $stationId ? \App\Models\KitchenStation::find($stationId) : null;

        return view('admin.kitchen.kds', compact('orders', 'stations', 'stationId', 'todayStats', 'currentStation', 'isKitchenStaff'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request('cancel')) {
            return redirect()->route('admin.order.index');
        }

        $query = Order::with(['user', 'table', 'reservation', 'items', 'etas', 'trackings']);

        // Filter by order status
        if ($request->has('order_status') && $request->order_status != '') {
            $query->where('order_status', $request->order_status);
        }

        // Filter by order type
        if ($request->has('order_type') && $request->order_type != '') {
            $query->where('order_type', $request->order_type);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('order_time', $request->date);
        }

        // Search by order ID, customer name, confirmation code
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('confirmation_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Default sort by creation time
        $query->orderBy('created_at', 'desc');

        $orders = $query->paginate(10)->appends($request->query());

        // Get statistics for dashboard cards
        $totalOrders = Order::count();
        $totalRevenue = Order::where('order_status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $completedOrders = Order::where('order_status', 'completed')->count();

        return view('admin.order.index', compact(
            'orders',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'completedOrders'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $order = new Order;

        // Separate customers from staff
        $customers = User::whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'manager', 'kitchen_staff']);
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $users = $customers; // For backward compatibility

        $tables = Table::where('is_active', true)->select('id', 'table_number', 'status', 'capacity')->orderBy('table_number')->get();
        $reservations = TableReservation::with('table')->whereDate('booking_date', '>=', now())->get();

        // Get menu items grouped by category
        $menuItems = MenuItem::with('category')
            ->where('availability', true)
            ->select('id', 'name', 'price', 'preparation_time', 'category_id')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        // Group menu items by category
        $menuItemsByCategory = $menuItems->groupBy(function($item) {
            return $item->category->name ?? 'Uncategorized';
        });

        return view('admin.order.form-improved', compact('order', 'users', 'customers', 'tables', 'reservations', 'menuItems', 'menuItemsByCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_id' => 'nullable|exists:table_reservations,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery,event',
            'order_source' => 'required|in:counter,web,mobile,waiter,qr_scan',
            'order_status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'table_number' => 'nullable|string|max:10',
            'total_amount' => 'required|numeric|min:0|max:999999.99',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
            'special_instructions' => 'nullable|array',
            'estimated_completion_time' => 'nullable|date|after:now',
            'actual_completion_time' => 'nullable|date',
            'is_rush_order' => 'nullable|boolean',
            'confirmation_code' => 'nullable|string|max:20|unique:orders,confirmation_code',
        ], [
            'user_id.required' => 'Customer is required.',
            'user_id.exists' => 'Selected customer does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'reservation_id.exists' => 'Selected reservation does not exist.',
            'order_type.required' => 'Order type is required.',
            'order_type.in' => 'Invalid order type selected.',
            'order_source.required' => 'Order source is required.',
            'order_source.in' => 'Invalid order source selected.',
            'order_status.required' => 'Order status is required.',
            'order_status.in' => 'Invalid order status selected.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_amount.max' => 'Total amount exceeds maximum limit.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'estimated_completion_time.after' => 'Estimated completion time must be in the future.',
            'confirmation_code.unique' => 'Confirmation code already exists.',
        ]);

        $order = new Order;
        $order->fill($request->all());

        // Handle boolean fields
        $order->is_rush_order = $request->has('is_rush_order');

        // Generate confirmation code if not provided
        if (empty($request->confirmation_code)) {
            $order->confirmation_code = Order::generateConfirmationCode();
        }

        // Set order_time to current timestamp
        $order->order_time = now();

        $order->save();

        // Handle order items creation
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (!empty($itemData['menu_item_id']) && !empty($itemData['price'])) {
                    $quantity = $itemData['quantity'] ?? 1;
                    $unitPrice = $itemData['price'];
                    $totalPrice = $unitPrice * $quantity;

                    $order->items()->create([
                        'menu_item_id' => $itemData['menu_item_id'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'special_note' => $itemData['notes'] ?? null,
                    ]);
                }
            }
        }

        // Auto-create ETA based on order items
        $order->load('items.menuItem'); // Load items with menu item data
        if ($order->items->count() > 0) {
            $order->autoCreateETA();
        }

        // 🔥 KITCHEN LOAD BALANCING: Auto-distribute order to stations
        // This happens IMMEDIATELY when order is created (regardless of status)
        // Orders need to go to kitchen right away so chefs can start preparing
        if ($order->items->count() > 0 && !in_array($order->order_status, ['cancelled', 'completed'])) {
            try {
                $distributionService = app(OrderDistributionService::class);
                $distributionService->distributeOrder($order);

                \Log::info('✅ Order distributed to kitchen stations', [
                    'order_id' => $order->id,
                    'confirmation_code' => $order->confirmation_code,
                    'items_count' => $order->items->count(),
                    'order_status' => $order->order_status
                ]);
            } catch (\Exception $e) {
                \Log::error('❌ Failed to distribute order to kitchen', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the order creation, just log the error
            }
        }

        // 🔥 DISPATCH REAL-TIME EVENT if order created with "paid" status
        if ($request->payment_status === 'paid') {
            // Fire generic analytics refresh event (will recalculate and broadcast)
            event(new AnalyticsRefreshEvent(today(), [], 'order_created'));
        }

        return redirect()->route('admin.order.index')->with('message', 'Order has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'table', 'reservation', 'items', 'etas', 'trackings.updatedBy']);
        return view('admin.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Separate customers from staff
        $customers = User::whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['admin', 'manager', 'kitchen_staff']);
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $users = $customers; // For backward compatibility

        $tables = Table::where('is_active', true)->select('id', 'table_number', 'status', 'capacity')->orderBy('table_number')->get();
        $reservations = TableReservation::with('table')->whereDate('booking_date', '>=', now())->get();

        // Get menu items grouped by category
        $menuItems = MenuItem::with('category')
            ->where('availability', true)
            ->select('id', 'name', 'price', 'preparation_time', 'category_id')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        // Group menu items by category
        $menuItemsByCategory = $menuItems->groupBy(function($item) {
            return $item->category->name ?? 'Uncategorized';
        });

        return view('admin.order.form-improved', compact('order', 'users', 'customers', 'tables', 'reservations', 'menuItems', 'menuItemsByCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_id' => 'nullable|exists:table_reservations,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery,event',
            'order_source' => 'required|in:counter,web,mobile,waiter,qr_scan',
            'order_status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'table_number' => 'nullable|string|max:10',
            'total_amount' => 'required|numeric|min:0|max:999999.99',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
            'special_instructions' => 'nullable|array',
            'estimated_completion_time' => 'nullable|date',
            'actual_completion_time' => 'nullable|date',
            'is_rush_order' => 'nullable|boolean',
            'confirmation_code' => 'nullable|string|max:20|unique:orders,confirmation_code,' . $order->id,
        ], [
            'user_id.required' => 'Customer is required.',
            'user_id.exists' => 'Selected customer does not exist.',
            'table_id.exists' => 'Selected table does not exist.',
            'reservation_id.exists' => 'Selected reservation does not exist.',
            'order_type.required' => 'Order type is required.',
            'order_type.in' => 'Invalid order type selected.',
            'order_source.required' => 'Order source is required.',
            'order_source.in' => 'Invalid order source selected.',
            'order_status.required' => 'Order status is required.',
            'order_status.in' => 'Invalid order status selected.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_amount.max' => 'Total amount exceeds maximum limit.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status selected.',
            'confirmation_code.unique' => 'Confirmation code already exists.',
        ]);

        // Store old values BEFORE updating (for detecting changes)
        $oldPaymentStatus = $order->payment_status;
        $oldOrderStatus = $order->order_status;
        $oldTotalAmount = $order->total_amount;

        $order->fill($request->all());

        // Handle boolean fields
        $order->is_rush_order = $request->has('is_rush_order');

        // Handle JSON fields
        if ($request->has('special_instructions')) {
            $order->special_instructions = $request->special_instructions;
        }

        // Set actual completion time if status is completed
        if ($request->order_status === 'completed' && !$order->actual_completion_time) {
            $order->actual_completion_time = now();
        }

        // Generate confirmation code if payment status is paid and no code exists
        if ($request->payment_status === 'paid' && empty($order->confirmation_code)) {
            $order->confirmation_code = Order::generateConfirmationCode();
        }

        $order->save();

        // 🔥 DISPATCH ANALYTICS REFRESH EVENT if any revenue-affecting field changed
        $needsRefresh = false;
        $reason = [];

        // Check payment status change
        if ($oldPaymentStatus !== $request->payment_status) {
            $needsRefresh = true;
            $reason[] = "payment_status:{$oldPaymentStatus}→{$request->payment_status}";
        }

        // Check order status change (affects whether order counts in revenue)
        if ($oldOrderStatus !== $request->order_status) {
            $needsRefresh = true;
            $reason[] = "order_status:{$oldOrderStatus}→{$request->order_status}";
        }

        // Check total amount change
        if ($oldTotalAmount != $request->total_amount) {
            $needsRefresh = true;
            $reason[] = "amount:{$oldTotalAmount}→{$request->total_amount}";
        }

        if ($needsRefresh) {
            event(new AnalyticsRefreshEvent(today(), [], 'order_updated:' . implode(',', $reason)));
        }

        // Handle order items updates
        if ($request->has('items') && is_array($request->items)) {
            // Delete existing items
            $order->items()->delete();
            
            // Create new items
            foreach ($request->items as $itemData) {
                if (!empty($itemData['menu_item_id']) && !empty($itemData['price'])) {
                    $quantity = $itemData['quantity'] ?? 1;
                    $unitPrice = $itemData['price'];
                    $totalPrice = $unitPrice * $quantity;
                    
                    $order->items()->create([
                        'menu_item_id' => $itemData['menu_item_id'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'special_note' => $itemData['notes'] ?? null,
                    ]);
                }
            }
            
            // Recalculate ETA if items changed
            $order->load('items.menuItem');
            if ($order->items->count() > 0) {
                $order->updateAutoETA();
            }
        }


        return redirect()->route('admin.order.index')->with('message', 'Order has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Store order details before deletion for event reason
        $orderStatus = $order->order_status;
        $paymentStatus = $order->payment_status;

        $order->delete();

        // 🔥 DISPATCH ANALYTICS REFRESH EVENT when order is deleted
        event(new AnalyticsRefreshEvent(
            today(),
            [],
            "order_deleted:status={$orderStatus},payment={$paymentStatus}"
        ));

        return redirect()->route('admin.order.index')->with('message', 'Order has been deleted successfully!');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->validate($request, [
            'order_status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'station_id' => 'nullable|exists:kitchen_stations,id', // Optional: specific station
        ]);

        $oldOrderStatus = $order->order_status;
        $newStatus = $request->order_status;
        $stationId = $request->station_id;

        // Kitchen Load Balancing: Distribute order when confirmed
        if ($newStatus === 'confirmed' && $oldOrderStatus !== 'confirmed') {
            try {
                $distributionService = app(\App\Services\Kitchen\OrderDistributionService::class);
                $distributionService->distributeOrder($order);
            } catch (\Exception $e) {
                \Log::error('Failed to distribute order to kitchen', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Handle station-specific completion
        if ($newStatus === 'completed' && $stationId) {
            // Mark station assignments as completed for this specific station
            $assignments = $order->stationAssignments()
                ->where('station_id', $stationId)
                ->whereIn('status', ['assigned', 'started'])
                ->get();

            foreach ($assignments as $assignment) {
                $assignment->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }

            // Release kitchen load for this station only
            $kitchenLoadService = app(\App\Services\Kitchen\KitchenLoadService::class);
            $kitchenLoadService->releaseLoad($stationId, $order->id);

            // Check if ALL station assignments are completed
            $remainingAssignments = $order->stationAssignments()
                ->whereIn('status', ['assigned', 'started'])
                ->count();

            if ($remainingAssignments === 0) {
                // All stations completed - mark entire order as completed
                $order->order_status = 'completed';
                $order->actual_completion_time = now();
                $order->save();
            } else {
                // Some stations still working - keep order in preparing/ready status
                if ($oldOrderStatus === 'pending' || $oldOrderStatus === 'confirmed') {
                    $order->order_status = 'preparing';
                    $order->save();
                }
                // Don't change status if already preparing/ready
            }
        }
        // Handle ready status (when station finishes but not yet served to customer)
        elseif ($newStatus === 'ready' && $stationId) {
            // Mark station assignments as completed for this specific station
            $assignments = $order->stationAssignments()
                ->where('station_id', $stationId)
                ->whereIn('status', ['assigned', 'started'])
                ->get();

            foreach ($assignments as $assignment) {
                $assignment->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }

            // Release kitchen load for this station
            $kitchenLoadService = app(\App\Services\Kitchen\KitchenLoadService::class);
            $kitchenLoadService->releaseLoad($stationId, $order->id);

            // Check if ALL stations are done
            $remainingAssignments = $order->stationAssignments()
                ->whereIn('status', ['assigned', 'started'])
                ->count();

            if ($remainingAssignments === 0) {
                // All stations ready - mark order as ready
                $order->order_status = 'ready';
                $order->save();
            } elseif ($oldOrderStatus !== 'ready' && $oldOrderStatus !== 'preparing') {
                $order->order_status = 'preparing';
                $order->save();
            }
        }
        // Handle order-level status updates (no specific station)
        else {
            $order->order_status = $newStatus;

            // Set actual completion time if status is completed
            if ($newStatus === 'completed' && !$order->actual_completion_time) {
                $order->actual_completion_time = now();

                // Mark ALL station assignments as completed
                $order->stationAssignments()
                    ->whereIn('status', ['assigned', 'started'])
                    ->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);

                // Release all loads
                $kitchenLoadService = app(\App\Services\Kitchen\KitchenLoadService::class);
                foreach ($order->kitchenLoads as $load) {
                    $kitchenLoadService->releaseLoad($load->station_id, $order->id);
                }
            }

            $order->save();
        }

        // Auto-create or update ETA when status changes to preparing
        if ($order->order_status === 'preparing' && $oldOrderStatus !== 'preparing') {
            $order->load('items.menuItem');
            if ($order->items->count() > 0) {
                // Check if ETA already exists
                if ($order->etas()->count() > 0) {
                    $order->updateAutoETA();
                } else {
                    $order->autoCreateETA();
                }
            }
        }

        // 🔥 DISPATCH ANALYTICS REFRESH EVENT when order status changes
        if ($oldOrderStatus !== $request->order_status) {
            event(new AnalyticsRefreshEvent(
                today(),
                [],
                "order_status_ajax:{$oldOrderStatus}→{$request->order_status}"
            ));

            // 🔥 BROADCAST ORDER STATUS UPDATE FOR KDS
            event(new \App\Events\OrderStatusUpdatedEvent(
                $order,
                $oldOrderStatus,
                auth()->user()->name ?? 'System'
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!',
            'order' => $order
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $this->validate($request, [
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
        ]);

        $oldPaymentStatus = $order->payment_status;
        $order->payment_status = $request->payment_status;
        $order->save();

        // Update loyalty tier if payment is marked as paid
        if ($request->payment_status === 'paid' && $order->user_id) {
            $customerProfile = \App\Models\CustomerProfile::where('user_id', $order->user_id)->first();
            if ($customerProfile) {
                $customerProfile->updateLoyaltyTier();
            }
        }

        // 🔥 DISPATCH ANALYTICS REFRESH EVENT when payment status changes
        if ($oldPaymentStatus !== $request->payment_status) {
            event(new AnalyticsRefreshEvent(
                today(),
                [],
                "payment_status_ajax:{$oldPaymentStatus}→{$request->payment_status}"
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully!',
            'order' => $order
        ]);
    }

    /**
     * Request more time for order preparation (Kitchen Staff)
     */
    public function needMoreTime(Request $request, Order $order)
    {
        $this->validate($request, [
            'additional_minutes' => 'required|integer|min:5|max:60',
        ]);

        $additionalMinutes = $request->additional_minutes ?? 10;

        // Update the estimated completion time
        if ($order->estimated_completion_time) {
            $order->estimated_completion_time = \Carbon\Carbon::parse($order->estimated_completion_time)
                ->addMinutes($additionalMinutes);
        } else {
            $order->estimated_completion_time = now()->addMinutes($additionalMinutes);
        }

        // Update ETA if exists
        $eta = $order->etas()->latest()->first();
        if ($eta) {
            $eta->estimated_time = \Carbon\Carbon::parse($eta->estimated_time)->addMinutes($additionalMinutes);
            $eta->delay_reason = 'Chef requested more time (+' . $additionalMinutes . ' minutes)';
            $eta->save();
        }

        $order->save();

        // Log the delay request
        \Log::info('Order delay requested', [
            'order_id' => $order->id,
            'requested_by' => auth()->user()->name,
            'additional_minutes' => $additionalMinutes,
            'new_estimated_time' => $order->estimated_completion_time,
        ]);

        // TODO: Send notification to manager about the delay
        // This can be implemented with broadcasting or push notifications

        return response()->json([
            'success' => true,
            'message' => "Added {$additionalMinutes} minutes to preparation time. Manager has been notified.",
            'order' => $order->fresh(['etas']),
            'new_estimated_time' => $order->estimated_completion_time?->format('h:i A'),
        ]);
    }

    /**
     * Get orders by status
     */
    public function getByStatus(Request $request)
    {
        $status = $request->get('status');
        
        $orders = Order::with(['user', 'table', 'items'])
            ->when($status, function ($query) use ($status) {
                return $query->where('order_status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        if (in_array($order->order_status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Cannot cancel this order.');
        }

        $oldOrderStatus = $order->order_status;
        $order->order_status = 'cancelled';
        $order->save();

        // 🔥 DISPATCH ANALYTICS REFRESH EVENT when order is cancelled
        event(new AnalyticsRefreshEvent(
            today(),
            [],
            "order_cancelled:{$oldOrderStatus}→cancelled"
        ));

        return redirect()->back()->with('message', 'Order has been cancelled successfully!');
    }

    /**
     * Duplicate order
     */
    public function duplicate(Order $order)
    {
        $newOrder = $order->replicate();
        $newOrder->order_status = 'pending';
        $newOrder->payment_status = 'unpaid';
        $newOrder->confirmation_code = $this->generateConfirmationCode();
        $newOrder->order_time = now();
        $newOrder->actual_completion_time = null;
        $newOrder->created_at = now();
        $newOrder->updated_at = now();
        
        $newOrder->save();

        // Copy order items if they exist
        foreach ($order->items as $item) {
            $newItem = $item->replicate();
            $newItem->order_id = $newOrder->id;
            $newItem->save();
        }

        return redirect()->route('admin.order.edit', $newOrder)->with('message', 'Order has been duplicated successfully!');
    }

    /**
     * Calculate ETA for given order items (used for AJAX calls)
     */
    public function calculateETA(Request $request)
    {
        $this->validate($request, [
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrepTime = 0;
        
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            if ($menuItem) {
                $prepTime = $menuItem->preparation_time ?? 15;
                $totalPrepTime += ($prepTime * $item['quantity']);
            }
        }
        
        // Add buffer time (10% or minimum 5 minutes)
        $bufferTime = max(5, round($totalPrepTime * 0.1));
        $totalTime = $totalPrepTime + $bufferTime;
        
        $estimatedTime = now()->addMinutes($totalTime);
        
        return response()->json([
            'success' => true,
            'total_prep_time' => $totalTime,
            'estimated_time' => $estimatedTime->format('Y-m-d H:i:s'),
            'estimated_time_formatted' => $estimatedTime->format('M d, Y h:i A'),
            'human_readable' => "Estimated {$totalTime} minutes from now"
        ]);
    }

    /**
     * Get menu item preparation time (used for AJAX calls)
     */
    public function getMenuItemPrepTime(Request $request)
    {
        $this->validate($request, [
            'menu_item_id' => 'required|exists:menu_items,id'
        ]);

        $menuItem = MenuItem::find($request->menu_item_id);
        
        return response()->json([
            'success' => true,
            'preparation_time' => $menuItem->preparation_time ?? 15,
            'name' => $menuItem->name,
            'price' => $menuItem->price
        ]);
    }
}