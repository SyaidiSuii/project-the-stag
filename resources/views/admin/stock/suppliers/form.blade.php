@extends('layouts.admin')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Add Supplier')
@section('page-title', isset($supplier) ? 'Edit Supplier' : 'Add New Supplier')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <form method="POST" action="{{ isset($supplier) ? route('admin.stock.suppliers.update', $supplier) : route('admin.stock.suppliers.store') }}">
        @csrf
        @if(isset($supplier))
            @method('PUT')
        @endif

        <div class="form-grid">
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-building"></i> Supplier Information
                </h3>

                <div class="form-group">
                    <label for="name">Supplier Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $supplier->name ?? '') }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person"
                           class="form-control @error('contact_person') is-invalid @enderror"
                           value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                    @error('contact_person')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-phone"></i> Contact Details
                </h3>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $supplier->phone ?? '') }}"
                           placeholder="+60123456789">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $supplier->email ?? '') }}"
                           placeholder="supplier@example.com">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website"
                           class="form-control @error('website') is-invalid @enderror"
                           value="{{ old('website', $supplier->website ?? '') }}"
                           placeholder="https://example.com">
                    @error('website')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section full-width">
                <h3 class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Address
                </h3>

                <div class="form-group">
                    <label for="address">Full Address</label>
                    <textarea id="address" name="address"
                              class="form-control @error('address') is-invalid @enderror"
                              rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section full-width">
                <h3 class="form-section-title">
                    <i class="fas fa-clipboard"></i> Additional Information
                </h3>

                <div class="form-group">
                    <label for="notes">Notes / Comments</label>
                    <textarea id="notes" name="notes"
                              class="form-control @error('notes') is-invalid @enderror"
                              rows="4"
                              placeholder="Any additional information about this supplier...">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
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
                               {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
                        <span>Active Supplier</span>
                    </label>
                    <small class="form-text d-block mt-2">Inactive suppliers won't appear in stock item selection</small>
                </div>
            </div>

            @if(isset($supplier))
            <!-- Statistics (Edit Only) -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-chart-bar"></i> Statistics
                </h3>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Stock Items</div>
                        <div class="stat-value">{{ $supplier->stockItems()->count() }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Purchase Orders</div>
                        <div class="stat-value">{{ $supplier->purchaseOrders()->count() }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Member Since</div>
                        <div class="stat-value">{{ $supplier->created_at->format('M Y') }}</div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.stock.items.index', ['supplier' => $supplier->id]) }}"
                       class="admin-btn btn-sm btn-secondary">
                        <i class="fas fa-boxes"></i> View Stock Items
                    </a>
                    <a href="{{ route('admin.stock.purchase-orders.index', ['supplier' => $supplier->id]) }}"
                       class="admin-btn btn-sm btn-secondary">
                        <i class="fas fa-file-invoice"></i> View Purchase Orders
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('admin.stock.suppliers.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="admin-btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($supplier) ? 'Update' : 'Create' }} Supplier
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Phone number formatting (optional)
    document.getElementById('phone')?.addEventListener('blur', function() {
        let phone = this.value.trim();
        // Add Malaysia country code if not present
        if (phone && !phone.startsWith('+')) {
            if (phone.startsWith('0')) {
                phone = '+6' + phone;
            } else if (!phone.startsWith('6')) {
                phone = '+60' + phone;
            } else {
                phone = '+' + phone;
            }
            this.value = phone;
        }
    });

    // Email validation helper
    document.getElementById('email')?.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        }
    });
</script>
@endsection
