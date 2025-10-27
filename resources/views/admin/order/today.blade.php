@extends('layouts.admin')

@section('title', "Today's Orders")
@section('page-title', "Today's Orders - " . now()->format('M d, Y'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
@endsection

@section('content')
@php
    $totalOrders = collect($orders)->flatten()->count();
    $pendingCount = $orders->get('pending', collect())->count();
    $preparingCount = $orders->get('preparing', collect())->count();
    $readyCount = $orders->get('ready', collect())->count();
    $servedCount = $orders->get('served', collect())->count();
    $completedCount = $orders->get('completed', collect())->count();
    $totalRevenue = collect($orders)->flatten()->where('payment_status', 'paid')->sum('total_amount');
@endphp

<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Today's Orders</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalOrders }}</div>
        <div class="admin-card-desc">Total orders today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Today's Revenue</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($totalRevenue, 2) }}</div>
        <div class="admin-card-desc">Revenue earned today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Pending Today</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">{{ $pendingCount }}</div>
        <div class="admin-card-desc">Awaiting confirmation</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">In Progress Today</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-utensils"></i></div>
        </div>
        <div class="admin-card-value">{{ $preparingCount + $readyCount }}</div>
        <div class="admin-card-desc">Preparing + Ready</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Served Today</div>
            <div class="admin-card-icon icon-purple"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $servedCount }}</div>
        <div class="admin-card-desc">Served orders today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Completed Today</div>
            <div class="admin-card-icon icon-teal"><i class="fas fa-flag-checkered"></i></div>
        </div>
        <div class="admin-card-value">{{ $completedCount }}</div>
        <div class="admin-card-desc">Finished orders today</div>
    </div>
</div>

<!-- Kitchen Display Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Kitchen Display - Orders by Status</h2>
        <div class="section-controls" style="display: flex; gap: 12px;">
            <a href="{{ route('admin.order.create') }}" class="admin-btn btn-primary">
                <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
                New Order
            </a>
            <a href="{{ route('admin.order.index') }}" class="admin-btn btn-secondary">
                <div class="admin-nav-icon"><i class="fas fa-list"></i></div>
                All Orders
            </a>
        </div>
    </div>

    {{-- Flash messages handled by layout toast system --}}

    <!-- Active Orders Display -->
    @foreach(['pending', 'preparing', 'ready', 'served'] as $status)
        @if($orders->has($status) && $orders->get($status)->count() > 0)
        <div class="admin-section-card">
            <div class="section-card-header
                @if($status == 'pending') status-pending
                @elseif($status == 'preparing') status-preparing
                @elseif($status == 'ready') status-ready
                @elseif($status == 'served') status-served
                @endif">
                <h3 class="section-card-title">
                    {{ str_replace('_', ' ', ucfirst($status)) }} Orders
                    <span class="section-card-count">({{ $orders->get($status)->count() }})</span>
                </h3>
                @if(in_array($status, ['preparing', 'ready']))
                    <div class="section-card-meta">
                        <span class="priority-badge">
                            <i class="fas fa-star"></i> Kitchen Priority
                        </span>
                    </div>
                @endif
            </div>
            
            <div class="kitchen-orders-grid">
                @foreach($orders->get($status)->sortBy('order_time') as $order)
                <div class="kitchen-order-card {{ $order->is_rush_order ? 'rush-order' : '' }}">
                    
                    <div class="order-card-header">
                        <div class="order-identity">
                            <h4 class="order-number">#{{ $order->id }}</h4>
                            <p class="customer-name">{{ $order->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="order-badges">
                            @if($order->confirmation_code)
                                <span class="confirmation-badge">{{ $order->confirmation_code }}</span>
                            @endif
                            @if($order->is_rush_order)
                                <span class="rush-badge">
                                    <i class="fas fa-bolt"></i> RUSH
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="order-details">
                        <div class="detail-row">
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $order->order_time->format('h:i A') }}</span>
                            </div>
                            <div class="detail-item amount">
                                <strong>RM {{ number_format($order->total_amount, 2) }}</strong>
                            </div>
                        </div>

                        <div class="detail-row">
                            @if($order->table || $order->table_number)
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                @if($order->table)
                                    Table {{ $order->table->table_number }}
                                @else
                                    {{ $order->table_number }}
                                @endif
                            </div>
                            @endif
                            <div class="detail-item">
                                <i class="fas fa-tag"></i>
                                {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                            </div>
                        </div>

                        @if($order->estimated_completion_time)
                            @php
                                $isOverdue = $order->estimated_completion_time < now() && !$order->actual_completion_time;
                            @endphp
                            <div class="detail-row {{ $isOverdue ? 'overdue' : '' }}">
                                <div class="detail-item">
                                    <i class="fas fa-hourglass-half"></i>
                                    ETA: {{ $order->estimated_completion_time->format('h:i A') }}
                                    @if($isOverdue)
                                        <span class="overdue-badge">OVERDUE</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($order->special_instructions && count($order->special_instructions) > 0)
                    <div class="special-instructions">
                        <strong>Special Instructions:</strong>
                        @foreach($order->special_instructions as $instruction)
                            @if($instruction)
                                <div>• {{ $instruction }}</div>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    @if($order->items && $order->items->count() > 0)
                    <div class="order-items-preview">
                        <strong>Items ({{ $order->items->count() }}):</strong>
                        @foreach($order->items as $item)
                            <div class="item-preview">{{ $item->quantity ?? 1 }}x {{ $item->menuItem->name ?? 'Item' }}</div>
                        @endforeach
                    </div>
                    @endif

                    <div class="order-card-actions">
                        <div class="status-actions">
                            @if($status == 'pending')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'preparing')"
                                        class="action-btn btn-confirm">
                                    <i class="fas fa-check"></i> Start Preparing
                                </button>
                            @elseif($status == 'preparing')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'ready')"
                                        class="action-btn btn-ready">
                                    <i class="fas fa-bell"></i> Ready
                                </button>
                            @elseif($status == 'ready')
                                @if($order->order_type === 'dine_in')
                                    <button onclick="updateOrderStatus({{ $order->id }}, 'served')"
                                            class="action-btn btn-served">
                                        <i class="fas fa-utensils"></i> Served
                                    </button>
                                @else
                                    <button onclick="updateOrderStatus({{ $order->id }}, 'completed')"
                                            class="action-btn btn-complete">
                                        <i class="fas fa-flag-checkered"></i> Complete
                                    </button>
                                @endif
                            @elseif($status == 'served')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'completed')"
                                        class="action-btn btn-complete">
                                    <i class="fas fa-flag-checkered"></i> Complete
                                </button>
                            @endif

                            @if($order->payment_method === 'counter' && $order->payment_status === 'unpaid')
                                <button onclick="updatePaymentStatus({{ $order->id }}, 'paid')"
                                        class="action-btn btn-payment"
                                        title="Mark as Paid">
                                    <i class="fas fa-dollar-sign"></i> Mark as Paid
                                </button>
                            @endif

                            <span class="payment-status status-{{ str_replace('_', '-', $order->payment_status) }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        
                        <div class="order-actions">
                            <a href="{{ route('admin.order.show', $order->id) }}" 
                               class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.order.edit', $order->id) }}" 
                               class="action-btn edit-btn" title="Edit Order">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    <!-- Completed and Cancelled Orders -->
    @php
        $finishedOrders = collect();
        if ($orders->has('completed')) {
            $finishedOrders = $finishedOrders->merge($orders->get('completed'));
        }
        if ($orders->has('cancelled')) {
            $finishedOrders = $finishedOrders->merge($orders->get('cancelled'));
        }
    @endphp

    @if($finishedOrders->count() > 0)
    <div class="admin-section-card">
        <div class="section-card-header status-finished">
            <h3 class="section-card-title">
                Completed & Cancelled Orders
                <span class="section-card-count">({{ $finishedOrders->count() }})</span>
            </h3>
        </div>
        
        <div class="finished-orders-list">
            @foreach($finishedOrders->sortByDesc('order_time') as $order)
            <div class="finished-order-item">
                <div class="finished-order-status">
                    <span class="status status-{{ str_replace('_', '-', $order->order_status) }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
                <div class="finished-order-info">
                    <div class="order-identity">
                        <strong>#{{ $order->id }}</strong> - {{ $order->user->name ?? 'Unknown' }}
                    </div>
                    <div class="order-meta">
                        {{ $order->order_time->format('h:i A') }} - 
                        RM {{ number_format($order->total_amount, 2) }}
                        @if($order->table)
                            - Table {{ $order->table->table_number }}
                        @elseif($order->table_number)
                            - {{ $order->table_number }}
                        @endif
                    </div>
                </div>
                <div class="finished-order-actions">
                    <a href="{{ route('admin.order.show', $order->id) }}" 
                       class="action-btn view-btn" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if($totalOrders == 0)
    <div class="admin-section-card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="empty-state-title">No orders today</div>
            <div class="empty-state-text">Get started by creating a new order.</div>
            <div class="empty-state-actions">
                <a href="{{ route('admin.order.create') }}" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> New Order
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/order-management.js') }}"></script>
<script>
    async function updateOrderStatus(orderId, status) {
        const statusLabels = {
            'preparing': 'Start Preparing',
            'ready': 'Mark as Ready',
            'served': 'Mark as Served',
            'completed': 'Mark as Completed'
        };

        const confirmed = await Confirm.show(
            'Update Order Status?',
            `Are you sure you want to ${statusLabels[status] || status}?`
        );

        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch(`/admin/order/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_status: status
                })
            });

            const data = await response.json();

            if (data.success) {
                Toast.success('Success', `Order updated to ${status}`);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                Toast.error('Error', data.message || 'Failed to update order status');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to update order status');
        }
    }

    async function updatePaymentStatus(orderId, status) {
        const confirmed = await Confirm.show(
            'Mark as Paid?',
            'Are you sure you want to mark this order as paid?'
        );

        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch(`/admin/order/${orderId}/update-payment-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_status: status
                })
            });

            const data = await response.json();

            if (data.success) {
                Toast.success('Success', 'Payment status updated');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                Toast.error('Error', data.message || 'Failed to update payment status');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to update payment status');
        }
    }

    // Auto-refresh page every 60 seconds for kitchen display
    setTimeout(function() {
        window.location.reload();
    }, 60000);

    // Visual timer for rush orders
    function updateRushOrderTimers() {
        const rushOrders = document.querySelectorAll('[data-rush-order]');
        rushOrders.forEach(order => {
            const orderTime = new Date(order.dataset.orderTime);
            const now = new Date();
            const diffMinutes = Math.floor((now - orderTime) / 60000);
            
            if (diffMinutes > 15) {
                order.classList.add('severe-overdue');
                const timerEl = order.querySelector('.rush-timer');
                if (timerEl) timerEl.textContent = `${diffMinutes} min ago`;
            }
        });
    }

    setInterval(updateRushOrderTimers, 60000);
    updateRushOrderTimers();
</script>

<style>
/* Additional styles for today's orders specific to kitchen display */
.kitchen-orders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.kitchen-order-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.2s ease;
}

.kitchen-order-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.kitchen-order-card.rush-order {
    border-color: #ef4444;
    background-color: #fef2f2;
}

.order-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.order-number {
    font-size: 1.25rem;
    font-weight: bold;
    margin: 0;
}

.customer-name {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

.order-badges {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.confirmation-badge {
    font-family: monospace;
    background: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
}

.rush-badge {
    background: #dc2626;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: bold;
}

.order-details {
    margin-bottom: 1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.detail-row.overdue {
    color: #dc2626;
    font-weight: bold;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-item.amount {
    color: #059669;
    font-weight: bold;
}

.detail-item i {
    width: 16px;
    color: #6b7280;
}

.overdue-badge {
    background: #dc2626;
    color: white;
    padding: 0.125rem 0.25rem;
    border-radius: 4px;
    font-size: 0.625rem;
}

.special-instructions {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 4px;
    padding: 0.75rem;
    margin-bottom: 1rem;
    font-size: 0.75rem;
}

.order-items-preview {
    background: #f9fafb;
    border-radius: 4px;
    padding: 0.75rem;
    margin-bottom: 1rem;
    font-size: 0.75rem;
}

.item-preview {
    margin-bottom: 0.25rem;
}

.items-more {
    color: #6b7280;
    font-style: italic;
}

.order-card-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
}

.status-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-actions {
    display: flex;
    gap: 0.25rem;
}

.action-btn {
    padding: 0.5rem 0.75rem;
    border: none;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: background-color 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-confirm {
    background: #10b981;
    color: white;
}

.btn-preparing {
    background: #3b82f6;
    color: white;
}

.btn-ready {
    background: #8b5cf6;
    color: white;
}

.btn-served {
    background: #6366f1;
    color: white;
}

.btn-complete {
    background: #059669;
    color: white;
}

.btn-payment {
    background: #10b981;
    color: white;
}

.payment-status {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
}

.status-paid {
    background: #d1fae5;
    color: #065f46;
}

.status-unpaid {
    background: #fee2e2;
    color: #991b1b;
}

.status-partial {
    background: #fef3c7;
    color: #92400e;
}

.finished-orders-list {
    padding: 1rem;
}

.finished-order-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}

.finished-order-info {
    flex: 1;
    margin-left: 1rem;
}

.order-meta {
    font-size: 0.875rem;
    color: #6b7280;
}

.status-pending { background: #fef3c7; }
.status-preparing { background: #dbeafe; }
.status-ready { background: #e9d5ff; }
.status-served { background: #e0e7ff; }
.status-finished { background: #f3f4f6; }

.section-card-count {
    font-weight: normal;
    color: #6b7280;
}

.priority-badge {
    background: #f59e0b;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
}
</style>
@endsection