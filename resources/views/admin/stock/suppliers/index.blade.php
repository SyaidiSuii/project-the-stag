@extends('layouts.admin')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">All Suppliers</h2>
        <a href="{{ route('admin.stock.suppliers.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i> Add Supplier
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <form method="GET" action="{{ route('admin.stock.suppliers.index') }}" class="filters-form">
            <div class="filter-group">
                <label>Status</label>
                <select name="status" class="admin-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Search</label>
                <div class="search-box">
                    <input type="text" name="search" class="admin-input"
                           placeholder="Search by name, email, or phone..."
                           value="{{ request('search') }}">
                    <button type="submit" class="admin-btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            @if(request()->hasAny(['status', 'search']))
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="{{ route('admin.stock.suppliers.index') }}" class="admin-btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Suppliers Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Stock Items</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
            <tr>
                <td>#{{ $supplier->id }}</td>
                <td><strong>{{ $supplier->name }}</strong></td>
                <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                <td>
                    @if($supplier->phone)
                        <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($supplier->email)
                        <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">
                        {{ $supplier->stockItems()->count() }} items
                    </span>
                </td>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox"
                               class="toggle-status"
                               data-id="{{ $supplier->id }}"
                               {{ $supplier->is_active ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.stock.suppliers.edit', $supplier) }}"
                           class="admin-btn btn-sm btn-primary"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button"
                                class="admin-btn btn-sm btn-danger"
                                onclick="deleteSupplier({{ $supplier->id }})"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No suppliers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle Status
    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const supplierId = this.dataset.id;
            const isActive = this.checked;

            fetch(`/admin/stock/suppliers/${supplierId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    this.checked = !isActive;
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                this.checked = !isActive;
                Swal.fire('Error!', 'Failed to update status', 'error');
            });
        });
    });

    // Delete Supplier
    function deleteSupplier(supplierId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This supplier will be deleted! Stock items linked to this supplier will remain.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/stock/suppliers/${supplierId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to delete supplier', 'error');
                });
            }
        });
    }
</script>
@endsection
