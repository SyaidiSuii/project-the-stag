@extends('layouts.admin')

@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Purchase Orders</h2>
        <div class="section-actions">
            <button type="button" class="admin-btn btn-success" onclick="generateAutoPO()">
                <i class="fas fa-magic"></i> Auto-Generate PO
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="admin-cards">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Pending Orders</div>
                <div class="admin-card-icon icon-orange"><i class="fas fa-clock"></i></div>
            </div>
            <div class="admin-card-value">{{ $summary['pending_count'] ?? 0 }}</div>
            <div class="admin-card-desc">Awaiting approval</div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Approved Orders</div>
                <div class="admin-card-icon icon-blue"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="admin-card-value">{{ $summary['approved_count'] ?? 0 }}</div>
            <div class="admin-card-desc">Ready to receive</div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Received This Month</div>
                <div class="admin-card-icon icon-green"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="admin-card-value">{{ $summary['received_count'] ?? 0 }}</div>
            <div class="admin-card-desc">Completed orders</div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Total Value</div>
                <div class="admin-card-icon icon-red"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <div class="admin-card-value">RM {{ number_format($summary['total_value'] ?? 0, 2) }}</div>
            <div class="admin-card-desc">Pending + Approved</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <form method="GET" action="{{ route('admin.stock.purchase-orders.index') }}" class="filters-form">
            <div class="filter-group">
                <label>Status</label>
                <select name="status" class="admin-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                <label>Type</label>
                <select name="auto_generated" class="admin-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="1" {{ request('auto_generated') === '1' ? 'selected' : '' }}>Auto-Generated</option>
                    <option value="0" {{ request('auto_generated') === '0' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Date From</label>
                <input type="date" name="date_from" class="filter-select"
                       value="{{ request('date_from') }}" onchange="this.form.submit()">
            </div>

            <div class="filter-group">
                <label>Date To</label>
                <input type="date" name="date_to" class="filter-select"
                       value="{{ request('date_to') }}" onchange="this.form.submit()">
            </div>

            @if(request()->hasAny(['status', 'supplier', 'auto_generated', 'date_from', 'date_to']))
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="{{ route('admin.stock.purchase-orders.index') }}" class="admin-btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Purchase Orders Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Date</th>
                <th>Items</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
            <tr class="{{ $po->status === 'cancelled' ? 'cancelled-row' : '' }}">
                <td>
                    <a href="{{ route('admin.stock.purchase-orders.show', $po) }}" class="po-number">
                        <strong>{{ $po->po_number }}</strong>
                    </a>
                </td>
                <td>{{ $po->supplier->name }}</td>
                <td>{{ $po->order_date->format('d M Y') }}</td>
                <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('d M Y') : 'N/A' }}</td>
                <td>
                    <span class="badge badge-info">{{ $po->items->count() }} items</span>
                </td>
                <td><strong>RM {{ number_format($po->total_amount, 2) }}</strong></td>
                <td>
                    @php
                        $statusClass = [
                            'pending' => 'warning',
                            'approved' => 'info',
                            'received' => 'success',
                            'cancelled' => 'danger'
                        ][$po->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $statusClass }}">
                        {{ strtoupper($po->status) }}
                    </span>
                </td>
                <td>
                    @if($po->auto_generated)
                        <span class="badge badge-primary" title="Auto-Generated">
                            <i class="fas fa-robot"></i> Auto
                        </span>
                    @else
                        <span class="badge badge-secondary" title="Manual">
                            <i class="fas fa-user"></i> Manual
                        </span>
                    @endif
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.stock.purchase-orders.show', $po) }}"
                           class="admin-btn btn-sm btn-info"
                           title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($po->status === 'pending')
                            <button type="button"
                                    class="admin-btn btn-sm btn-success"
                                    onclick="approvePO({{ $po->id }})"
                                    title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif

                        @if($po->status === 'approved')
                            <button type="button"
                                    class="admin-btn btn-sm btn-primary"
                                    onclick="markAsReceivedModal({{ $po->id }}, '{{ $po->po_number }}')"
                                    title="Mark as Received">
                                <i class="fas fa-box-open"></i>
                            </button>
                        @endif

                        @if(in_array($po->status, ['pending', 'approved']))
                            <button type="button"
                                    class="admin-btn btn-sm btn-danger"
                                    onclick="cancelPO({{ $po->id }})"
                                    title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No purchase orders found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $purchaseOrders->links() }}
    </div>
</div>

<!-- Mark as Received Modal -->
<div id="receivedModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Mark as Received: <span id="modalPoNumber"></span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="receivedForm">
            @csrf
            <input type="hidden" id="modalPoId">

            <div class="form-group">
                <label>Received Date</label>
                <input type="date" name="received_date" class="form-control"
                       value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Any notes about the delivery..."></textarea>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                This will automatically update stock quantities based on the PO items.
            </div>

            <div class="modal-footer">
                <button type="button" class="admin-btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="admin-btn btn-primary">Mark as Received</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Approve PO
    function approvePO(poId) {
        Swal.fire({
            title: 'Approve Purchase Order?',
            text: "This PO will be approved and ready for receiving",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve it!',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/stock/purchase-orders/${poId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Approved!', data.message, 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to approve PO', 'error');
                });
            }
        });
    }

    // Mark as Received Modal
    function markAsReceivedModal(poId, poNumber) {
        document.getElementById('modalPoId').value = poId;
        document.getElementById('modalPoNumber').textContent = poNumber;
        document.getElementById('receivedModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('receivedModal').style.display = 'none';
        document.getElementById('receivedForm').reset();
    }

    // Submit Received Form
    document.getElementById('receivedForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const poId = document.getElementById('modalPoId').value;
        const formData = new FormData(this);

        fetch(`/admin/stock/purchase-orders/${poId}/mark-received`, {
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
            Swal.fire('Error!', 'Failed to mark as received', 'error');
        });
    });

    // Cancel PO
    function cancelPO(poId) {
        Swal.fire({
            title: 'Cancel Purchase Order?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel it!',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/stock/purchase-orders/${poId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Cancelled!', data.message, 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to cancel PO', 'error');
                });
            }
        });
    }

    // Generate Auto PO
    function generateAutoPO() {
        Swal.fire({
            title: 'Generate Purchase Orders?',
            text: "This will automatically create POs for low stock items",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Generate',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                // TODO: Implement API endpoint for auto-generation
                Swal.fire('Coming Soon', 'Auto-generation feature will be implemented', 'info');
            }
        });
    }

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('receivedModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection
