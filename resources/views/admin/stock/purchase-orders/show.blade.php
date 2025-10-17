@extends('layouts.admin')

@section('title', 'Purchase Order Details')
@section('page-title', $purchaseOrder->po_number)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/stock-management.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <!-- Action Buttons -->
    <div class="section-header">
        <a href="{{ route('admin.stock.purchase-orders.index') }}" class="admin-btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <div class="section-actions">
            @if($purchaseOrder->status === 'pending')
                <button type="button" class="admin-btn btn-success" onclick="approvePO()">
                    <i class="fas fa-check"></i> Approve
                </button>
            @endif

            @if($purchaseOrder->status === 'approved')
                <button type="button" class="admin-btn btn-primary" onclick="markAsReceivedModal()">
                    <i class="fas fa-box-open"></i> Mark as Received
                </button>
            @endif

            @if(in_array($purchaseOrder->status, ['pending', 'approved']))
                <button type="button" class="admin-btn btn-danger" onclick="cancelPO()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            @endif

            <button type="button" class="admin-btn btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- PO Status Badge -->
    <div class="po-status-banner status-{{ $purchaseOrder->status }}">
        @php
            $statusClass = [
                'pending' => 'warning',
                'approved' => 'info',
                'received' => 'success',
                'cancelled' => 'danger'
            ][$purchaseOrder->status] ?? 'secondary';
        @endphp
        <span class="badge badge-{{ $statusClass }} badge-lg">
            {{ strtoupper($purchaseOrder->status) }}
        </span>
        @if($purchaseOrder->auto_generated)
            <span class="badge badge-primary badge-lg ml-2">
                <i class="fas fa-robot"></i> AUTO-GENERATED
            </span>
        @endif
    </div>

    <!-- PO Details Grid -->
    <div class="details-grid">
        <!-- Purchase Order Information -->
        <div class="details-section">
            <h3 class="details-title">
                <i class="fas fa-file-invoice"></i> Purchase Order Information
            </h3>
            <table class="details-table">
                <tr>
                    <th>PO Number</th>
                    <td><strong>{{ $purchaseOrder->po_number }}</strong></td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td>{{ $purchaseOrder->order_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Expected Delivery</th>
                    <td>{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('d M Y') : 'N/A' }}</td>
                </tr>
                @if($purchaseOrder->status === 'received' && $purchaseOrder->received_at)
                <tr>
                    <th>Received Date</th>
                    <td>{{ $purchaseOrder->received_at->format('d M Y H:i') }}</td>
                </tr>
                @endif
                <tr>
                    <th>Created By</th>
                    <td>{{ $purchaseOrder->createdBy->name ?? 'System' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $purchaseOrder->created_at->format('d M Y H:i') }}</td>
                </tr>
                @if($purchaseOrder->approved_at)
                <tr>
                    <th>Approved At</th>
                    <td>{{ $purchaseOrder->approved_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Approved By</th>
                    <td>{{ $purchaseOrder->approvedBy->name ?? 'N/A' }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Supplier Information -->
        <div class="details-section">
            <h3 class="details-title">
                <i class="fas fa-truck"></i> Supplier Information
            </h3>
            <table class="details-table">
                <tr>
                    <th>Supplier Name</th>
                    <td><strong>{{ $purchaseOrder->supplier->name }}</strong></td>
                </tr>
                <tr>
                    <th>Contact Person</th>
                    <td>{{ $purchaseOrder->supplier->contact_person ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>
                        @if($purchaseOrder->supplier->phone)
                            <a href="tel:{{ $purchaseOrder->supplier->phone }}">{{ $purchaseOrder->supplier->phone }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>
                        @if($purchaseOrder->supplier->email)
                            <a href="mailto:{{ $purchaseOrder->supplier->email }}">{{ $purchaseOrder->supplier->email }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $purchaseOrder->supplier->address ?? 'N/A' }}</td>
                </tr>
            </table>
            <div class="mt-3">
                <a href="{{ route('admin.stock.suppliers.edit', $purchaseOrder->supplier) }}" class="admin-btn btn-sm btn-secondary">
                    <i class="fas fa-eye"></i> View Supplier
                </a>
            </div>
        </div>
    </div>

    <!-- PO Items -->
    <div class="admin-section mt-4">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-boxes"></i> Order Items
            </h3>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Stock Item</th>
                    <th>SKU</th>
                    <th>Quantity Ordered</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->stockItem->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $item->stockItem->category }}</small>
                    </td>
                    <td><code>{{ $item->stockItem->sku }}</code></td>
                    <td>
                        <strong>{{ $item->quantity }} {{ $item->stockItem->unit }}</strong>
                    </td>
                    <td>RM {{ number_format($item->unit_price, 2) }}</td>
                    <td><strong>RM {{ number_format($item->quantity * $item->unit_price, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                    <td><strong class="total-amount">RM {{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Notes -->
    @if($purchaseOrder->notes)
    <div class="admin-section mt-4">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-sticky-note"></i> Notes
            </h3>
        </div>
        <div class="notes-box">
            {{ $purchaseOrder->notes }}
        </div>
    </div>
    @endif

    <!-- Related Stock Transactions (if received) -->
    @if($purchaseOrder->status === 'received')
    <div class="admin-section mt-4">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-history"></i> Stock Transactions
            </h3>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Stock Item</th>
                    <th>Quantity Added</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrder->stockTransactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $transaction->stockItem->name }}</td>
                    <td class="text-success">
                        <strong>+{{ $transaction->quantity }} {{ $transaction->stockItem->unit }}</strong>
                    </td>
                    <td>{{ $transaction->notes ?? 'From PO: ' . $purchaseOrder->po_number }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No stock transactions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Mark as Received Modal -->
<div id="receivedModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Mark as Received: {{ $purchaseOrder->po_number }}</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="receivedForm">
            @csrf

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
                This will automatically update stock quantities for all {{ $purchaseOrder->items->count() }} items in this PO.
            </div>

            <div class="modal-footer">
                <button type="button" class="admin-btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="admin-btn btn-primary">Confirm Received</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Approve PO
    function approvePO() {
        Swal.fire({
            title: 'Approve this Purchase Order?',
            text: "The PO will be approved and ready for receiving",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve it!',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.stock.purchase-orders.approve", $purchaseOrder) }}', {
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
    function markAsReceivedModal() {
        document.getElementById('receivedModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('receivedModal').style.display = 'none';
        document.getElementById('receivedForm').reset();
    }

    // Submit Received Form
    document.getElementById('receivedForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("admin.stock.purchase-orders.mark-received", $purchaseOrder) }}', {
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
    function cancelPO() {
        Swal.fire({
            title: 'Cancel this Purchase Order?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel it!',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.stock.purchase-orders.destroy", $purchaseOrder) }}', {
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
                            .then(() => window.location.href = '{{ route("admin.stock.purchase-orders.index") }}');
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

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('receivedModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Print styles
    window.addEventListener('beforeprint', function() {
        document.querySelector('.section-header .section-actions')?.style.setProperty('display', 'none', 'important');
        document.querySelector('.po-status-banner')?.style.setProperty('page-break-after', 'avoid');
    });

    window.addEventListener('afterprint', function() {
        document.querySelector('.section-header .section-actions')?.style.removeProperty('display');
    });
</script>
@endsection
