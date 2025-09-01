<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuickReorder;
use App\Models\CustomerProfile;
use App\Models\Order;
use Illuminate\Validation\Rule;

class QuickReorderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('admin.quick-reorder.index');
        }

        $quickReorders = QuickReorder::with(['customerProfile'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.quick-reorder.index', compact('quickReorders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $quickReorder = new QuickReorder;
        $customerProfiles = CustomerProfile::select('id', 'name')->get();
        
        return view('admin.quick-reorder.form', compact('quickReorder', 'customerProfiles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_profile_id' => 'required|exists:customer_profiles,id',
            'order_name' => 'required|string|max:255',
            'order_items' => 'required|array|min:1',
            'order_items.*.id' => 'required|integer',
            'order_items.*.name' => 'required|string|max:255',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'order_items.*.total' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0|max:999999.99',
            'order_frequency' => 'nullable|integer|min:1',
        ], [
            'customer_profile_id.required' => 'Customer profile is required.',
            'customer_profile_id.exists' => 'Selected customer profile does not exist.',
            'order_name.required' => 'Order name is required.',
            'order_name.max' => 'Order name cannot exceed 255 characters.',
            'order_items.required' => 'Order items are required.',
            'order_items.array' => 'Order items must be an array.',
            'order_items.min' => 'At least one order item is required.',
            'order_items.*.id.required' => 'Item ID is required.',
            'order_items.*.name.required' => 'Item name is required.',
            'order_items.*.quantity.required' => 'Item quantity is required.',
            'order_items.*.quantity.min' => 'Item quantity must be at least 1.',
            'order_items.*.price.required' => 'Item price is required.',
            'order_items.*.price.min' => 'Item price cannot be negative.',
            'order_items.*.total.required' => 'Item total is required.',
            'order_items.*.total.min' => 'Item total cannot be negative.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_amount.max' => 'Total amount exceeds maximum limit.',
            'order_frequency.min' => 'Order frequency must be at least 1.',
        ]);

        $quickReorder = new QuickReorder;
        $quickReorder->fill($request->all());

        // Set default order frequency if not provided
        if (empty($request->order_frequency)) {
            $quickReorder->order_frequency = 1;
        }

        $quickReorder->save();

        return redirect()->route('admin.quick-reorder.index')->with('message', 'Quick reorder has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(QuickReorder $quickReorder)
    {
        $quickReorder->load(['customerProfile']);
        return view('admin.quick-reorder.show', compact('quickReorder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuickReorder $quickReorder)
    {
        $customerProfiles = CustomerProfile::select('id', 'name')->get();
        
        return view('admin.quick-reorder.form', compact('quickReorder', 'customerProfiles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuickReorder $quickReorder)
    {
        $this->validate($request, [
            'customer_profile_id' => 'required|exists:customer_profiles,id',
            'order_name' => 'required|string|max:255',
            'order_items' => 'required|array|min:1',
            'order_items.*.id' => 'required|integer',
            'order_items.*.name' => 'required|string|max:255',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric|min:0',
            'order_items.*.total' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0|max:999999.99',
            'order_frequency' => 'nullable|integer|min:1',
        ], [
            'customer_profile_id.required' => 'Customer profile is required.',
            'customer_profile_id.exists' => 'Selected customer profile does not exist.',
            'order_name.required' => 'Order name is required.',
            'order_name.max' => 'Order name cannot exceed 255 characters.',
            'order_items.required' => 'Order items are required.',
            'order_items.array' => 'Order items must be an array.',
            'order_items.min' => 'At least one order item is required.',
            'order_items.*.id.required' => 'Item ID is required.',
            'order_items.*.name.required' => 'Item name is required.',
            'order_items.*.quantity.required' => 'Item quantity is required.',
            'order_items.*.quantity.min' => 'Item quantity must be at least 1.',
            'order_items.*.price.required' => 'Item price is required.',
            'order_items.*.price.min' => 'Item price cannot be negative.',
            'order_items.*.total.required' => 'Item total is required.',
            'order_items.*.total.min' => 'Item total cannot be negative.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_amount.max' => 'Total amount exceeds maximum limit.',
            'order_frequency.min' => 'Order frequency must be at least 1.',
        ]);

        $quickReorder->fill($request->all());

        // Set default order frequency if not provided
        if (empty($request->order_frequency)) {
            $quickReorder->order_frequency = 1;
        }

        $quickReorder->save();

        return redirect()->route('admin.quick-reorder.index')->with('message', 'Quick reorder has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuickReorder $quickReorder)
    {
        $quickReorder->delete();
        return redirect()->route('admin.quick-reorder.index')->with('message', 'Quick reorder has been deleted successfully!');
    }

    /**
     * Get quick reorders by customer
     */
    public function getByCustomer(Request $request)
    {
        $customerId = $request->get('customer_id');
        
        $quickReorders = QuickReorder::with(['customerProfile'])
            ->when($customerId, function ($query) use ($customerId) {
                return $query->where('customer_profile_id', $customerId);
            })
            ->orderBy('order_frequency', 'desc')
            ->orderBy('last_ordered_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'quick_reorders' => $quickReorders
        ]);
    }

    /**
     * Convert quick reorder to actual order
     */
    public function convertToOrder(QuickReorder $quickReorder, Request $request)
    {
        $this->validate($request, [
            'order_type' => 'required|in:dine_in,takeaway,delivery,event',
            'order_source' => 'required|in:counter,web,mobile,waiter,qr_scan',
            'table_id' => 'nullable|exists:tables,id',
            'reservation_id' => 'nullable|exists:table_reservations,id',
            'special_instructions' => 'nullable|array',
        ]);

        // Get customer profile and related user
        $customerProfile = $quickReorder->customerProfile;
        if (!$customerProfile || !$customerProfile->user) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile or user not found.'
            ], 400);
        }

        // Create new order from quick reorder
        $order = new Order();
        $order->user_id = $customerProfile->user->id;
        $order->table_id = $request->table_id;
        $order->reservation_id = $request->reservation_id;
        $order->order_type = $request->order_type;
        $order->order_source = $request->order_source;
        $order->order_status = 'pending';
        $order->total_amount = $quickReorder->total_amount;
        $order->payment_status = 'unpaid';
        $order->special_instructions = $request->special_instructions ?? [];
        $order->order_time = now();
        $order->confirmation_code = $this->generateConfirmationCode();

        $order->save();

        // Update quick reorder statistics
        $quickReorder->increment('order_frequency');
        $quickReorder->last_ordered_at = now();
        $quickReorder->save();

        return response()->json([
            'success' => true,
            'message' => 'Quick reorder converted to order successfully!',
            'order' => $order->load(['user', 'table', 'reservation'])
        ]);
    }

    /**
     * Duplicate quick reorder
     */
    public function duplicate(QuickReorder $quickReorder)
    {
        $newQuickReorder = $quickReorder->replicate();
        $newQuickReorder->order_name = $quickReorder->order_name . ' (Copy)';
        $newQuickReorder->order_frequency = 1;
        $newQuickReorder->last_ordered_at = null;
        $newQuickReorder->created_at = now();
        $newQuickReorder->updated_at = now();
        
        $newQuickReorder->save();

        return redirect()->route('admin.quick-reorder.edit', $newQuickReorder)->with('message', 'Quick reorder has been duplicated successfully!');
    }

    /**
     * Get popular quick reorders
     */
    public function getPopular(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $popularQuickReorders = QuickReorder::with(['customerProfile'])
            ->orderBy('order_frequency', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'popular_quick_reorders' => $popularQuickReorders
        ]);
    }

    /**
     * Get recent quick reorders
     */
    public function getRecent(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $recentQuickReorders = QuickReorder::with(['customerProfile'])
            ->whereNotNull('last_ordered_at')
            ->orderBy('last_ordered_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'recent_quick_reorders' => $recentQuickReorders
        ]);
    }

    /**
     * Update order frequency
     */
    public function updateFrequency(Request $request, QuickReorder $quickReorder)
    {
        $this->validate($request, [
            'order_frequency' => 'required|integer|min:1',
        ]);

        $quickReorder->order_frequency = $request->order_frequency;
        $quickReorder->save();

        return response()->json([
            'success' => true,
            'message' => 'Order frequency updated successfully!',
            'quick_reorder' => $quickReorder
        ]);
    }

    /**
     * Bulk delete quick reorders
     */
    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:quick_reorders,id',
        ]);

        $deletedCount = QuickReorder::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} quick reorders have been deleted successfully!",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Search quick reorders
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $customerId = $request->get('customer_id');
        
        $quickReorders = QuickReorder::with(['customerProfile'])
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('order_name', 'like', "%{$query}%");
            })
            ->when($customerId, function ($queryBuilder) use ($customerId) {
                return $queryBuilder->where('customer_profile_id', $customerId);
            })
            ->orderBy('order_frequency', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'quick_reorders' => $quickReorders
        ]);
    }

    /**
     * Generate unique confirmation code for orders
     */
    private function generateConfirmationCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (Order::where('confirmation_code', $code)->exists());

        return $code;
    }
}