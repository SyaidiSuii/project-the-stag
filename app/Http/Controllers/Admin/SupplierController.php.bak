<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        $query = Supplier::withCount('stockItems');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        } else {
            $query->where('is_active', true);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->latest()->paginate(15);

        return view('admin.stock.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        return view('admin.stock.suppliers.form', ['supplier' => null]);
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        Supplier::create($validated);

        return redirect()->route('admin.stock.suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Display the specified supplier
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['stockItems' => function ($query) {
            $query->with('transactions')->latest();
        }]);

        return view('admin.stock.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.stock.suppliers.form', compact('supplier'));
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.stock.suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified supplier
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has stock items
        if ($supplier->stockItems()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with existing stock items!');
        }

        $supplier->delete();

        return redirect()->route('admin.stock.suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }

    /**
     * Toggle supplier status
     */
    public function toggleStatus(Supplier $supplier)
    {
        $supplier->is_active = !$supplier->is_active;
        $supplier->save();

        return response()->json([
            'success' => true,
            'is_active' => $supplier->is_active,
            'message' => 'Supplier status updated successfully!'
        ]);
    }
}
