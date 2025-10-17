@extends('layouts.admin')

@section('title', 'Stock Items')
@section('page-title', 'Stock Items Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">All Stock Items</h2>
        <a href="{{ route('admin.stock.items.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i> Add Stock Item
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <form method="GET" action="{{ route('admin.stock.items.index') }}" class="filters-form">
            <div class="filter-group">
                <label>Status</label>
                <select name="status" class="admin-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="critical" {{ request('status') === 'critical' ? 'selected' : '' }}>Critical Stock</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Category</label>
                <select name="category" class="admin-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="meat" {{ request('category') === 'meat' ? 'selected' : '' }}>Meat</option>
                    <option value="vegetables" {{ request('category') === 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                    <option value="dairy" {{ request('category') === 'dairy' ? 'selected' : '' }}>Dairy</option>
                    <option value="beverages" {{ request('category') === 'beverages' ? 'selected' : '' }}>Beverages</option>
                    <option value="dry_goods" {{ request('category') === 'dry_goods' ? 'selected' : '' }}>Dry Goods</option>
                    <option value="frozen" {{ request('category') === 'frozen' ? 'selected' : '' }}>Frozen</option>
                    <option value="others" {{ request('category') === 'others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Supplier</label>
                <select name="supplier" class="admin-select" onchange="this.form.submit()">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Search</label>
                <div class="search-box">
                    <input type="text" name="search" class="admin-input"
                           placeholder="Search by name or SKU..."
                           value="{{ request('search') }}">
                    <button type="submit" class="admin-btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            @if(request()->hasAny(['status', 'category', 'supplier', 'search']))
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="{{ route('admin.stock.items.index') }}" class="admin-btn btn-secondary">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Stock Items Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Current Qty</th>
                <th>Reorder Point</th>
                <th>Unit Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockItems as $item)
            <tr class="{{ $item->isCriticalStock() ? 'critical-row' : ($item->isLowStock() ? 'warning-row' : '') }}">
                <td>
                    <code>{{ $item->sku }}</code>
                </td>
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->isCriticalStock())
                        <span class="badge badge-danger ml-2">CRITICAL</span>
                    @elseif($item->isLowStock())
                        <span class="badge badge-warning ml-2">LOW</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                </td>
                <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                <td>
                    <span class="stock-quantity {{ $item->isCriticalStock() ? 'critical' : ($item->isLowStock() ? 'low' : 'good') }}">
                        {{ $item->current_quantity }} {{ $item->unit }}
                    </span>
                </td>
                <td>{{ $item->reorder_point }} {{ $item->unit }}</td>
                <td>RM {{ number_format($item->unit_price, 2) }}</td>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox"
                               class="toggle-status"
                               data-id="{{ $item->id }}"
                               {{ $item->is_active ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.stock.items.show', $item) }}"
                           class="admin-btn btn-sm btn-info"
                           title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.stock.items.edit', $item) }}"
                           class="admin-btn btn-sm btn-primary"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button"
                                class="admin-btn btn-sm btn-success"
                                onclick="adjustStockModal({{ $item->id }}, '{{ $item->name }}')"
                                title="Adjust Stock">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                        <button type="button"
                                class="admin-btn btn-sm btn-danger"
                                onclick="deleteItem({{ $item->id }})"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No stock items found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $stockItems->links() }}
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustStockModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adjust Stock: <span id="modalItemName"></span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="adjustStockForm">
            @csrf
            <input type="hidden" id="modalItemId">

            <div class="form-group">
                <label>Adjustment Type</label>
                <select name="type" id="adjustmentType" class="form-control" required>
                    <option value="add">Add Stock (Purchase/Restock)</option>
                    <option value="reduce">Reduce Stock (Waste/Loss)</option>
                    <option value="adjust">Manual Adjustment</option>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" min="0.01" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Reason for adjustment..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="admin-btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="admin-btn btn-primary">Submit Adjustment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle Status
    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const itemId = this.dataset.id;
            const isActive = this.checked;

            fetch(`/admin/stock/items/${itemId}/toggle-status`, {
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

    // Adjust Stock Modal
    function adjustStockModal(itemId, itemName) {
        document.getElementById('modalItemId').value = itemId;
        document.getElementById('modalItemName').textContent = itemName;
        document.getElementById('adjustStockModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('adjustStockModal').style.display = 'none';
        document.getElementById('adjustStockForm').reset();
    }

    // Submit Adjust Stock Form
    document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const itemId = document.getElementById('modalItemId').value;
        const formData = new FormData(this);

        fetch(`/admin/stock/items/${itemId}/adjust-stock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success!', data.message, 'success')
                    .then(() => window.location.reload());
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Failed to adjust stock', 'error');
        });
    });

    // Delete Item
    function deleteItem(itemId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This stock item will be soft deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/stock/items/${itemId}`, {
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
                    Swal.fire('Error!', 'Failed to delete item', 'error');
                });
            }
        });
    }

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('adjustStockModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection
