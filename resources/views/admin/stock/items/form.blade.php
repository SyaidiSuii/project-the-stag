@extends('layouts.admin')

@section('title', isset($item) ? 'Edit Stock Item' : 'Add Stock Item')
@section('page-title', isset($item) ? 'Edit Stock Item' : 'Add New Stock Item')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <form method="POST" action="{{ isset($item) ? route('admin.stock.items.update', $item) : route('admin.stock.items.store') }}">
        @csrf
        @if(isset($item))
            @method('PUT')
        @endif

        <div class="form-grid">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-info-circle"></i> Basic Information
                </h3>

                <div class="form-group">
                    <label for="name">Item Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $item->name ?? '') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sku">SKU <span class="required">*</span></label>
                    <input type="text" id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror"
                           value="{{ old('sku', $item->sku ?? '') }}" required
                           placeholder="e.g., BEEF-001">
                    @error('sku')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text">Unique identifier for this stock item</small>
                </div>

                <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        <option value="meat" {{ old('category', $item->category ?? '') === 'meat' ? 'selected' : '' }}>Meat</option>
                        <option value="vegetables" {{ old('category', $item->category ?? '') === 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                        <option value="dairy" {{ old('category', $item->category ?? '') === 'dairy' ? 'selected' : '' }}>Dairy</option>
                        <option value="beverages" {{ old('category', $item->category ?? '') === 'beverages' ? 'selected' : '' }}>Beverages</option>
                        <option value="dry_goods" {{ old('category', $item->category ?? '') === 'dry_goods' ? 'selected' : '' }}>Dry Goods</option>
                        <option value="frozen" {{ old('category', $item->category ?? '') === 'frozen' ? 'selected' : '' }}>Frozen</option>
                        <option value="others" {{ old('category', $item->category ?? '') === 'others' ? 'selected' : '' }}>Others</option>
                    </select>
                    @error('category')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $item->description ?? '') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Stock & Pricing -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-boxes"></i> Stock & Pricing
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="current_quantity">Current Quantity <span class="required">*</span></label>
                        <input type="number" id="current_quantity" name="current_quantity"
                               class="form-control @error('current_quantity') is-invalid @enderror"
                               value="{{ old('current_quantity', $item->current_quantity ?? 0) }}"
                               min="0" step="0.01" required>
                        @error('current_quantity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="unit">Unit <span class="required">*</span></label>
                        <input type="text" id="unit" name="unit"
                               class="form-control @error('unit') is-invalid @enderror"
                               value="{{ old('unit', $item->unit ?? '') }}"
                               placeholder="e.g., kg, pcs, liter" required>
                        @error('unit')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reorder_point">Reorder Point <span class="required">*</span></label>
                        <input type="number" id="reorder_point" name="reorder_point"
                               class="form-control @error('reorder_point') is-invalid @enderror"
                               value="{{ old('reorder_point', $item->reorder_point ?? '') }}"
                               min="0" step="0.01" required>
                        @error('reorder_point')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text">Alert when stock falls below this level</small>
                    </div>

                    <div class="form-group">
                        <label for="reorder_quantity">Reorder Quantity <span class="required">*</span></label>
                        <input type="number" id="reorder_quantity" name="reorder_quantity"
                               class="form-control @error('reorder_quantity') is-invalid @enderror"
                               value="{{ old('reorder_quantity', $item->reorder_quantity ?? '') }}"
                               min="0" step="0.01" required>
                        @error('reorder_quantity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text">Default quantity to reorder</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="unit_price">Unit Price (RM) <span class="required">*</span></label>
                    <input type="number" id="unit_price" name="unit_price"
                           class="form-control @error('unit_price') is-invalid @enderror"
                           value="{{ old('unit_price', $item->unit_price ?? '') }}"
                           min="0" step="0.01" required>
                    @error('unit_price')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-truck"></i> Supplier Information
                </h3>

                <div class="form-group">
                    <label for="supplier_id">Supplier <span class="required">*</span></label>
                    <select id="supplier_id" name="supplier_id"
                            class="form-control @error('supplier_id') is-invalid @enderror" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id', $item->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text">
                        <a href="{{ route('admin.stock.suppliers.create') }}" target="_blank">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </a>
                    </small>
                </div>

                <div class="form-group">
                    <label for="lead_time_days">Lead Time (Days)</label>
                    <input type="number" id="lead_time_days" name="lead_time_days"
                           class="form-control @error('lead_time_days') is-invalid @enderror"
                           value="{{ old('lead_time_days', $item->lead_time_days ?? 7) }}"
                           min="0">
                    @error('lead_time_days')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text">Time from order to delivery</small>
                </div>
            </div>

            <!-- Status -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-toggle-on"></i> Status
                </h3>

                <div class="form-group">
                    <label class="toggle-label">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
                        <span>Active Item</span>
                    </label>
                    <small class="form-text d-block mt-2">Inactive items won't be included in auto-reordering</small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('admin.stock.items.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="admin-btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($item) ? 'Update' : 'Create' }} Stock Item
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate SKU from name if SKU is empty
    document.getElementById('name').addEventListener('blur', function() {
        const skuInput = document.getElementById('sku');
        if (!skuInput.value) {
            const name = this.value.trim();
            if (name) {
                // Generate SKU: Take first 3 letters + random number
                const prefix = name.substring(0, 3).toUpperCase();
                const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                skuInput.value = `${prefix}-${randomNum}`;
            }
        }
    });

    // Calculate suggested reorder quantity based on reorder point
    document.getElementById('reorder_point').addEventListener('input', function() {
        const reorderQtyInput = document.getElementById('reorder_quantity');
        if (!reorderQtyInput.value || parseFloat(reorderQtyInput.value) === 0) {
            // Suggest 2x reorder point as default reorder quantity
            const suggestedQty = parseFloat(this.value) * 2;
            reorderQtyInput.value = suggestedQty;
        }
    });
</script>
@endsection
