@extends('layouts.admin')

@section('title', 'Stock Item Details')
@section('page-title', $item->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <!-- Action Buttons -->
    <div class="section-header">
        <a href="{{ route('admin.stock.items.index') }}" class="admin-btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <div class="section-actions">
            <a href="{{ route('admin.stock.items.edit', $item) }}" class="admin-btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button type="button" class="admin-btn btn-success" onclick="adjustStockModal()">
                <i class="fas fa-exchange-alt"></i> Adjust Stock
            </button>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="admin-cards">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Current Stock</div>
                <div class="admin-card-icon icon-blue"><i class="fas fa-boxes"></i></div>
            </div>
            <div class="admin-card-value">{{ $item->current_quantity }} {{ $item->unit }}</div>
            <div class="admin-card-desc">
                @if($item->isCriticalStock())
                    <span class="text-danger"><i class="fas fa-exclamation-circle"></i> CRITICAL</span>
                @elseif($item->isLowStock())
                    <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> LOW STOCK</span>
                @else
                    <span class="text-success"><i class="fas fa-check-circle"></i> Good Level</span>
                @endif
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Reorder Point</div>
                <div class="admin-card-icon icon-orange"><i class="fas fa-bell"></i></div>
            </div>
            <div class="admin-card-value">{{ $item->reorder_point }} {{ $item->unit }}</div>
            <div class="admin-card-desc">Reorder Qty: {{ $item->reorder_quantity }} {{ $item->unit }}</div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Unit Price</div>
                <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <div class="admin-card-value">RM {{ number_format($item->unit_price, 2) }}</div>
            <div class="admin-card-desc">Total Value: RM {{ number_format($item->current_quantity * $item->unit_price, 2) }}</div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Lead Time</div>
                <div class="admin-card-icon icon-red"><i class="fas fa-clock"></i></div>
            </div>
            <div class="admin-card-value">{{ $item->lead_time_days ?? 0 }} days</div>
            <div class="admin-card-desc">From order to delivery</div>
        </div>
    </div>

    <!-- Item Details -->
    <div class="details-grid">
        <div class="details-section">
            <h3 class="details-title">
                <i class="fas fa-info-circle"></i> Basic Information
            </h3>
            <table class="details-table">
                <tr>
                    <th>SKU</th>
                    <td><code>{{ $item->sku }}</code></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><strong>{{ $item->name }}</strong></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $item->description ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($item->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Last Updated</th>
                    <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="details-section">
            <h3 class="details-title">
                <i class="fas fa-truck"></i> Supplier Information
            </h3>
            @if($item->supplier)
                <table class="details-table">
                    <tr>
                        <th>Supplier Name</th>
                        <td>{{ $item->supplier->name }}</td>
                    </tr>
                    <tr>
                        <th>Contact Person</th>
                        <td>{{ $item->supplier->contact_person ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $item->supplier->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $item->supplier->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{ $item->supplier->address ?? 'N/A' }}</td>
                    </tr>
                </table>
                <div class="mt-3">
                    <a href="{{ route('admin.stock.suppliers.edit', $item->supplier) }}" class="admin-btn btn-sm btn-secondary">
                        <i class="fas fa-eye"></i> View Supplier
                    </a>
                </div>
            @else
                <p class="text-muted">No supplier assigned</p>
            @endif
        </div>
    </div>

    <!-- Stock Transaction History -->
    <div class="admin-section mt-4">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-history"></i> Stock Transaction History
            </h3>
            <div class="section-actions">
                <select id="transactionFilter" class="admin-select">
                    <option value="">All Transactions</option>
                    <option value="in">Stock In</option>
                    <option value="out">Stock Out</option>
                </select>
            </div>
        </div>

        <table class="admin-table" id="transactionsTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Balance After</th>
                    <th>Reference</th>
                    <th>User</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr data-type="{{ $transaction->type }}">
                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="badge badge-{{ $transaction->type === 'in' ? 'success' : 'danger' }}">
                            {{ strtoupper($transaction->type) }}
                        </span>
                    </td>
                    <td class="{{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                        <strong>
                            {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }} {{ $item->unit }}
                        </strong>
                    </td>
                    <td>{{ $transaction->quantity_after }} {{ $item->unit }}</td>
                    <td>
                        @if($transaction->reference_type && $transaction->reference_id)
                            <span class="text-muted">
                                {{ class_basename($transaction->reference_type) }} #{{ $transaction->reference_id }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $transaction->user->name ?? 'System' }}</td>
                    <td>{{ Str::limit($transaction->notes ?? '-', 40) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No transactions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="pagination-container">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustStockModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Adjust Stock: {{ $item->name }}</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="adjustStockForm">
            @csrf

            <div class="form-group">
                <label>Adjustment Type</label>
                <select name="type" id="adjustmentType" class="form-control" required>
                    <option value="add">Add Stock (Purchase/Restock)</option>
                    <option value="reduce">Reduce Stock (Waste/Loss)</option>
                    <option value="adjust">Manual Adjustment</option>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity ({{ $item->unit }})</label>
                <input type="number" name="quantity" class="form-control"
                       min="0.01" step="0.01" required
                       placeholder="Enter quantity">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Reason for adjustment..." required></textarea>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Current stock: <strong>{{ $item->current_quantity }} {{ $item->unit }}</strong>
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
    // Adjust Stock Modal
    function adjustStockModal() {
        document.getElementById('adjustStockModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('adjustStockModal').style.display = 'none';
        document.getElementById('adjustStockForm').reset();
    }

    // Submit Adjust Stock Form
    document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("admin.stock.items.adjust-stock", $item) }}', {
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

    // Transaction Filter
    document.getElementById('transactionFilter')?.addEventListener('change', function() {
        const filterValue = this.value;
        const rows = document.querySelectorAll('#transactionsTable tbody tr');

        rows.forEach(row => {
            if (!filterValue) {
                row.style.display = '';
            } else {
                const rowType = row.dataset.type;
                row.style.display = rowType === filterValue ? '' : 'none';
            }
        });
    });

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('adjustStockModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection
