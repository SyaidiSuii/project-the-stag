@extends('layouts.admin')

@section('title', 'Orders Management')
@section('page-title', 'Orders Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Orders</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalOrders ?? 0 }}</div>
        <div class="admin-card-desc">All orders</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Today's Revenue</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($todayRevenue ?? 0, 2) }}</div>
        <div class="admin-card-desc">Today's earnings</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Pending Orders</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">{{ $pendingOrders ?? 0 }}</div>
        <div class="admin-card-desc">Awaiting confirmation</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Completed</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $completedOrders ?? 0 }}</div>
        <div class="admin-card-desc">Successfully completed</div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Orders</h2>
        <div class="section-controls">
            <a href="{{ route('admin.order.today') }}" class="admin-btn btn-secondary">
                <div class="admin-nav-icon"><i class="fas fa-calendar-day"></i></div>
                Today's Orders
            </a>
        </div>
    </div>
    

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search orders by ID, customer, confirmation..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="orderStatusFilter">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="preparing" {{ request('order_status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="ready" {{ request('order_status') == 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="served" {{ request('order_status') == 'served' ? 'selected' : '' }}>Served</option>
                <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select class="filter-select" id="orderTypeFilter">
                <option value="">All Types</option>
                <option value="dine_in" {{ request('order_type') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                <option value="takeaway" {{ request('order_type') == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                <option value="delivery" {{ request('order_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="event" {{ request('order_type') == 'event' ? 'selected' : '' }}>Event</option>
            </select>
            <select class="filter-select" id="paymentStatusFilter">
                <option value="">All Payment Status</option>
                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <input type="date" class="filter-select" id="dateFilter" value="{{ request('date') }}">
        </div>
        {{-- <div class="filter-group">
            <button onclick="filterByStatus('pending')" class="status-filter-btn btn-yellow">
                <i class="fas fa-clock"></i> Pending
            </button>
            <button onclick="filterByStatus('preparing')" class="status-filter-btn btn-blue">
                <i class="fas fa-utensils"></i> Preparing
            </button>
            <button onclick="filterByStatus('ready')" class="status-filter-btn btn-green">
                <i class="fas fa-check"></i> Ready
            </button>
        </div> --}}
        <a href="{{ route('admin.order.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New Order
        </a>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-order">Order Details</th>
                    <th class="th-customer">Customer</th>
                    <th class="th-type">Type/Table</th>
                    <th class="th-amount">Amount</th>
                    <th class="th-status">Status</th>
                    <th class="th-eta">ETA</th>
                    <th class="th-time">Order Time</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <div class="order-info">
                            {{-- <div class="order-id">#{{ $order->id }}</div> --}}
                            @if($order->id)
                                <div class="confirmation-code">ORD-{{ $order->id }}</div>
                            @endif
                            @if($order->is_rush_order)
                                <span class="status status-rush">
                                    <i class="fas fa-bolt"></i> RUSH
                                </span>
                            @endif
                            @if($order->items_count)
                                <div class="order-meta">{{ $order->items_count }} items</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-name">{{ $order->user->name ?? 'Unknown' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="type-table-info">
                            <span class="status status-type status-{{ str_replace('_', '-', $order->order_type) }}">
                                {{ str_replace('_', ' ', ucfirst($order->order_type)) }}
                            </span>
                            {{-- <div class="order-source">{{ ucfirst(str_replace('_', ' ', $order->order_source)) }}</div> --}}
                            @if($order->table)
                                <div class="table-info">
                                    <strong>{{ $order->table->table_number }}</strong>
                                    <span class="table-type">{{ $order->table->table_type }}</span>
                                </div>
                            @elseif($order->table_number)
                                <div class="table-info">
                                    <strong>{{ $order->table_number }}</strong>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="amount">RM {{ number_format($order->total_amount, 2) }}</div>
                    </td>
                    <td class="cell-center">
                        <div class="status-group-vertical">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 4px; margin-bottom: 6px;">
                                <span class="status status-payment status-payment-{{ str_replace('_', '-', $order->payment_status) }}">
                                    <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : ($order->payment_status === 'unpaid' ? 'clock' : 'info-circle') }}"></i>
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                                @if($order->payment_method)
                                    <span class="status status-method" style="font-size: 9px; background: #e0e7ff; color: #4c51bf; padding: 3px 6px;">
                                        {{ $order->payment_method === 'online' ? 'Online' : 'Counter' }}
                                    </span>
                                @endif
                            </div>
                            <span class="status status-order status-order-{{ str_replace('_', '-', $order->order_status) }}">
                                <i class="fas fa-{{
                                    $order->order_status === 'pending' ? 'clock' :
                                    ($order->order_status === 'preparing' ? 'utensils' :
                                    ($order->order_status === 'ready' ? 'bell' :
                                    ($order->order_status === 'served' ? 'check' :
                                    ($order->order_status === 'completed' ? 'check-double' : 'times'))))
                                }}"></i>
                                {{ str_replace('_', ' ', ucfirst($order->order_status)) }}
                            </span>
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="eta-info">
                            @if($order->etas && $order->etas->count() > 0)
                                @php
                                    $latestEta = $order->etas->sortByDesc('created_at')->first();
                                    $estimatedCompletionTime = $order->order_time ? $order->order_time->addMinutes($latestEta->current_estimate) : null;
                                    $now = now();
                                    $isOverdue = $estimatedCompletionTime && $estimatedCompletionTime < $now && !in_array($order->order_status, ['completed', 'cancelled']);
                                @endphp
                                @if($estimatedCompletionTime && !in_array($order->order_status, ['completed', 'cancelled']))
                                    <div class="eta-time" style="font-size: 14px; font-weight: 600; color: {{ $isOverdue ? '#ef4444' : '#10b981' }};">
                                        {{ $estimatedCompletionTime->format('g:i A') }}
                                    </div>
                                    @if($isOverdue)
                                        <div style="font-size: 9px; color: #ef4444; font-weight: 600;">OVERDUE</div>
                                    @elseif($latestEta->is_delayed)
                                        <div style="font-size: 9px; color: #f59e0b; font-weight: 600;">DELAYED</div>
                                    @endif
                                @elseif(in_array($order->order_status, ['completed', 'cancelled']))
                                    <span style="color: #6b7280; font-size: 12px;">{{ ucfirst($order->order_status) }}</span>
                                @else
                                    <span class="text-muted" style="font-size: 12px;">No ETA</span>
                                @endif
                            @else
                                <span class="text-muted" style="font-size: 12px;">No ETA</span>
                            @endif
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="time-info">
                            <div class="order-date">{{ $order->order_time->format('M d') }}</div>
                            <div class="order-time">{{ $order->order_time->format('g:i A') }}</div>
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="table-actions">
                            <!-- Payment Status Button for Counter Payments -->
                            @if($order->payment_method === 'counter' && $order->payment_status === 'unpaid')
                                <button class="action-btn" title="Mark as Paid"
                                        onclick="updatePaymentStatus({{ $order->id }}, 'paid')"
                                        style="background: #10b981; color: white;">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                            @endif

                            <!-- Status Update Buttons -->
                            @if($order->order_status === 'pending')
                                <button class="action-btn confirm-btn" title="Confirm Order" onclick="updateOrderStatus({{ $order->id }}, 'preparing')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="action-btn cancel-btn" title="Cancel Order" onclick="updateOrderStatus({{ $order->id }}, 'cancelled')">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($order->order_status === 'preparing')
                                <button class="action-btn ready-btn" title="Mark as Ready" onclick="updateOrderStatus({{ $order->id }}, 'ready')">
                                    <i class="fas fa-bell"></i>
                                </button>
                            @elseif($order->order_status === 'ready')
                                @if($order->order_type === 'dine_in')
                                    <button class="action-btn serve-btn" title="Mark as Served" onclick="updateOrderStatus({{ $order->id }}, 'served')">
                                        <i class="fas fa-utensils"></i>
                                    </button>
                                @else
                                    <button class="action-btn complete-btn" title="Mark as Completed" onclick="updateOrderStatus({{ $order->id }}, 'completed')">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endif
                            @elseif($order->order_status === 'served')
                                <button class="action-btn complete-btn" title="Mark as Completed" onclick="updateOrderStatus({{ $order->id }}, 'completed')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            @elseif($order->order_status === 'completed' && $order->payment_method === 'counter' && $order->payment_status === 'unpaid')
                                <button class="action-btn" title="Mark as Paid"
                                        onclick="updatePaymentStatus({{ $order->id }}, 'paid')"
                                        style="background: #10b981; color: white;">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                            @endif

                            <!-- Default Action Buttons -->
                            <a href="{{ route('admin.order.show', $order->id) }}" 
                               class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.order.edit', $order->id) }}" 
                               class="action-btn edit-btn" title="Edit Order">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!in_array($order->order_status, ['completed', 'cancelled']))
                                <form method="POST" action="{{ route('admin.order.destroy', $order->id) }}" style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this order?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete Order">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="empty-state-title">No orders found</div>
                        <div class="empty-state-text">
                            @if(request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                                No orders match your current filters. Try adjusting your search criteria.
                            @else
                                No orders have been placed yet.
                            @endif
                        </div>
                        @if(!request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                            <div style="margin-top: 20px;">
                                <a href="{{ route('admin.order.create') }}" class="admin-btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Order
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="pagination">
            <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
                <span style="font-size: 14px; color: var(--text-2);">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
                </span>
            </div>
            
            @if($orders->onFirstPage())
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $orders->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                @if($page == $orders->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/order-management.js') }}"></script>
<script>
// Notification function
function showNotification(message, type) {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Check for session messages on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
});

// Auto-refresh page every 3 minutes to update ETA status
setInterval(() => {
    if (document.visibilityState === 'visible') {
        // Only refresh if no modals or forms are open
        if (!document.querySelector('.modal.show') && !document.querySelector('form:focus-within')) {
            location.reload();
        }
    }
}, 180000); // 3 minutes

// Function to update order status
function updateOrderStatus(orderId, newStatus) {
    // Status transition validation
    const statusTransitions = {
        'pending': ['preparing', 'cancelled'],
        'preparing': ['ready', 'cancelled'],
        'ready': ['served', 'completed', 'cancelled'],
        'served': ['completed']
    };

    // Confirmation messages
    const confirmMessages = {
        'preparing': 'Start preparing this order?',
        'ready': 'Mark this order as ready?',
        'served': 'Mark this order as served?',
        'completed': 'Mark this order as completed?',
        'cancelled': 'Cancel this order? This action cannot be undone.'
    };

    if (!confirm(confirmMessages[newStatus] || 'Update order status?')) {
        return;
    }

    // Use fetch API instead of form submit to handle JSON response
    fetch(`/admin/order/${orderId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and reload page
            console.log('Order status updated successfully');
            location.reload();
        } else {
            alert('Error updating order status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating order status. Please try again.');
    });
}

// Function to update payment status
function updatePaymentStatus(orderId, newStatus) {
    if (!confirm('Mark this order as paid?')) {
        return;
    }

    fetch(`/admin/order/${orderId}/update-payment-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Payment status updated successfully');
            location.reload();
        } else {
            alert('Error updating payment status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment status. Please try again.');
    });
}
</script>
@endsection