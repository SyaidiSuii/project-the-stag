<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.stockItem']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        // Filter by auto-generated
        if ($request->has('auto_generated')) {
            $query->where('is_auto_generated', $request->auto_generated === '1');
        }

        $purchaseOrders = $query->latest('order_date')->paginate(15);

        // Get suppliers for filter
        $suppliers = Supplier::active()->get();

        return view('admin.stock.purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.stockItem', 'creator', 'approver']);

        return view('admin.stock.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Approve a pending purchase order
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending' && $purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only pending/draft purchase orders can be approved!');
        }

        $purchaseOrder->approve(Auth::id());

        return back()->with('success', 'Purchase order approved successfully!');
    }

    /**
     * Mark purchase order as received and update stock
     */
    public function markAsReceived(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'approved' && $purchaseOrder->status !== 'ordered') {
            return back()->with('error', 'Only approved/ordered purchase orders can be marked as received!');
        }

        // Validate received quantities
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update received quantities
            foreach ($validated['items'] as $itemData) {
                $item = $purchaseOrder->items()->findOrFail($itemData['item_id']);
                $item->quantity_received = $itemData['quantity_received'];
                $item->save();
            }

            // Mark PO as received and update stock
            $purchaseOrder->markAsReceived();

            DB::commit();

            return redirect()->route('admin.stock.purchase-orders.index')
                ->with('success', 'Purchase order received and stock updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to process received order: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow updating draft/pending POs
        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return back()->with('error', 'Cannot edit approved/received purchase orders!');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'expected_delivery_date' => 'nullable|date',
        ]);

        $purchaseOrder->update($validated);

        return back()->with('success', 'Purchase order updated successfully!');
    }

    /**
     * Cancel/Delete the specified purchase order
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Only allow deleting draft/pending POs
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Cannot delete received purchase orders!');
        }

        if ($purchaseOrder->status === 'approved') {
            // Change to cancelled instead of delete
            $purchaseOrder->status = 'cancelled';
            $purchaseOrder->save();
            $message = 'Purchase order cancelled successfully!';
        } else {
            $purchaseOrder->delete();
            $message = 'Purchase order deleted successfully!';
        }

        return redirect()->route('admin.stock.purchase-orders.index')
            ->with('success', $message);
    }
}
