@extends('layouts.admin')

@section('title', 'Stock Management Dashboard')
@section('page-title', 'Stock Management Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<!-- Summary Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Stock Items</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-boxes"></i></div>
        </div>
        <div class="admin-card-value">{{ $summary['total_stock_items'] }}</div>
        <div class="admin-card-desc">{{ $summary['good_stock_items'] }} active items</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Low Stock Items</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
        <div class="admin-card-value">{{ $summary['low_stock_items'] }}</div>
        <div class="admin-card-desc">Needs attention</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Critical Stock</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-exclamation-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $summary['critical_stock_items'] }}</div>
        <div class="admin-card-desc">Urgent reorder required</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Stock Value</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ $summary['total_stock_value'] }}</div>
        <div class="admin-card-desc">Current inventory value</div>
    </div>
</div>

<!-- Pending Purchase Orders -->
@if($summary['pending_purchase_orders'] > 0)
<div class="admin-section">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        You have <strong>{{ $summary['pending_purchase_orders'] }}</strong> pending purchase orders
        (Total: RM {{ number_format($summary['pending_orders_total'], 2) }})
        <a href="{{ route('admin.stock.purchase-orders.index', ['status' => 'pending']) }}" class="alert-link">View Orders</a>
    </div>
</div>
@endif

<!-- Low Stock Alerts -->
@if(count($lowStockItems) > 0)
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-exclamation-triangle text-warning"></i> Low Stock Alerts
        </h2>
        <a href="{{ route('admin.stock.items.index', ['status' => 'low']) }}" class="admin-btn btn-secondary">
            <i class="fas fa-eye"></i> View All
        </a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Current Qty</th>
                <th>Reorder Point</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStockItems as $item)
            <tr class="{{ $item->isCriticalStock() ? 'critical-row' : '' }}">
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->isCriticalStock())
                        <span class="badge badge-danger ml-2">CRITICAL</span>
                    @endif
                </td>
                <td>{{ $item->sku }}</td>
                <td>
                    <span class="stock-quantity {{ $item->isCriticalStock() ? 'critical' : 'low' }}">
                        {{ $item->current_quantity }} {{ $item->unit }}
                    </span>
                </td>
                <td>{{ $item->reorder_point }} {{ $item->unit }}</td>
                <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                <td>
                    @if($item->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.stock.items.show', $item) }}" class="admin-btn btn-sm btn.primary">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button type="button" class="admin-btn btn-sm btn-success"
                            onclick="quickAdjustStock({{ $item->id }}, '{{ $item->name }}')">
                        <i class="fas fa-plus"></i> Add Stock
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Recent Stock Transactions -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-history"></i> Recent Stock Transactions
        </h2>
        <div class="section-actions">
            <select id="transactionTypeFilter" class="admin-select">
                <option value="">All Types</option>
                <option value="purchase">Purchase</option>
                <option value="sale">Sale</option>
                <option value="adjustment">Adjustment</option>
                <option value="waste">Waste</option>
            </select>
        </div>
    </div>
    <table class="admin-table" id="transactionsTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Reference</th>
                <th>User</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td>{{ $transaction->stockItem->name }}</td>
                <td>
                    <span class="badge badge-{{
                        $transaction->type === 'in' ? 'success' : 'danger'
                    }}">
                        {{ strtoupper($transaction->type) }}
                    </span>
                </td>
                <td class="{{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                    {{ $transaction->stockItem->unit }}
                </td>
                <td>
                    @if($transaction->reference_type && $transaction->reference_id)
                        <span class="text-muted">{{ class_basename($transaction->reference_type) }} #{{ $transaction->reference_id }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $transaction->user->name ?? 'System' }}</td>
                <td>{{ Str::limit($transaction->notes ?? '-', 30) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No recent transactions</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Quick Actions -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-bolt"></i> Quick Actions
        </h2>
    </div>
    <div class="quick-actions-grid">
        <a href="{{ route('admin.stock.items.create') }}" class="quick-action-card">
            <i class="fas fa-plus-circle"></i>
            <span>Add New Stock Item</span>
        </a>
        <a href="{{ route('admin.stock.suppliers.create') }}" class="quick-action-card">
            <i class="fas fa-truck"></i>
            <span>Add New Supplier</span>
        </a>
        <a href="{{ route('admin.stock.purchase-orders.index') }}" class="quick-action-card">
            <i class="fas fa-file-invoice"></i>
            <span>View Purchase Orders</span>
        </a>
        <button type="button" onclick="generateAutoPurchaseOrders()" class="quick-action-card">
            <i class="fas fa-magic"></i>
            <span>Generate Auto PO</span>
        </button>
    </div>
</div>

<!-- Quick Adjust Stock Modal -->
<div id="quickAdjustModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adjust Stock: <span id="adjustItemName"></span></h3>
            <button type="button" class="modal-close" onclick="closeAdjustModal()">&times;</button>
        </div>
        <form id="quickAdjustForm">
            @csrf
            <input type="hidden" id="adjustItemId" name="item_id">
            <div class="form-group">
                <label>Quantity to Add</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>
            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="admin-btn btn-secondary" onclick="closeAdjustModal()">Cancel</button>
                <button type="submit" class="admin-btn btn-primary">Add Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Quick Adjust Stock Modal
    function quickAdjustStock(itemId, itemName) {
        document.getElementById('adjustItemId').value = itemId;
        document.getElementById('adjustItemName').textContent = itemName;
        document.getElementById('quickAdjustModal').style.display = 'flex';
    }

    function closeAdjustModal() {
        document.getElementById('quickAdjustModal').style.display = 'none';
        document.getElementById('quickAdjustForm').reset();
    }

    // Submit Quick Adjust Form
    document.getElementById('quickAdjustForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const itemId = document.getElementById('adjustItemId').value;

        fetch(`/admin/stock/items/${itemId}/adjust-stock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
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

    // Transaction Type Filter
    document.getElementById('transactionTypeFilter')?.addEventListener('change', function() {
        const filterValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#transactionsTable tbody tr');

        rows.forEach(row => {
            if (!filterValue) {
                row.style.display = '';
            } else {
                const typeCell = row.cells[2]?.textContent.toLowerCase();
                row.style.display = typeCell.includes(filterValue) ? '' : 'none';
            }
        });
    });

    // Generate Auto Purchase Orders
    function generateAutoPurchaseOrders() {
        Swal.fire({
            title: 'Generate Purchase Orders?',
            text: 'This will automatically create purchase orders for low stock items',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Generate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // TODO: Implement API call to generate POs
                Swal.fire('Coming Soon', 'This feature will be implemented soon', 'info');
            }
        });
    }

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('quickAdjustModal');
        if (event.target === modal) {
            closeAdjustModal();
        }
    }
</script>
@endsection