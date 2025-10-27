@extends('layouts.admin')

@section('title', 'Kitchen Active Orders')
@section('page-title', 'Kitchen Active Orders')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.order-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-left: 4px solid #6366f1;
    transition: all 0.3s ease;
}

.order-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateX(2px);
}

.order-card.priority-high {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.order-card.priority-medium {
    border-left-color: #f59e0b;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.order-info {
    display: flex;
    gap: 16px;
    align-items: center;
}

.order-items-list {
    margin: 16px 0;
    padding-left: 0;
    list-style: none;
}

.order-items-list li {
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
}

.station-filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.station-tab {
    padding: 10px 20px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
    text-decoration: none;
    color: #64748b;
}

.station-tab:hover {
    border-color: #6366f1;
    color: #6366f1;
    text-decoration: none;
}

.station-tab.active {
    background: #6366f1;
    border-color: #6366f1;
    color: white;
}

/* Pagination Styles */
.pagination {
    display: flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-item {
    display: inline-block;
}

.page-link {
    position: relative;
    display: block;
    padding: 8px 12px;
    font-size: 14px;
    line-height: 1.5;
    color: #475569;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.15s ease-in-out;
}

.page-link:hover {
    z-index: 2;
    color: #6366f1;
    background-color: #f8fafc;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #6366f1;
    border-color: #6366f1;
}

.page-item.disabled .page-link {
    color: #cbd5e1;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link svg {
    width: 14px;
    height: 14px;
    vertical-align: middle;
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.badge-primary {
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
}

.badge-secondary {
    background: #fbbf24;
    color: white;
    box-shadow: 0 2px 6px rgba(251, 191, 36, 0.2);
}

.badge-warning {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(251, 191, 36, 0.2);
}

.badge-success {
    background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);
}

.badge-danger {
    background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
}

.badge-info {
    background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(6, 182, 212, 0.2);
}

/* Text Color Utilities */
.text-muted {
    color: #64748b !important;
}

.text-danger {
    color: #f87171 !important;
    font-weight: 600;
}

.text-success {
    color: #34d399 !important;
    font-weight: 600;
}
</style>
@endsection

@section('content')

<div class="kitchen-page">

    {{-- Summary Stats - Always show stats for ALL orders, not filtered by station --}}
    @if($allOrders->isNotEmpty())
    <div class="admin-section">
        <div class="admin-cards">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Total Orders</div>
                    <div class="admin-card-icon icon-blue"><i class="fas fa-receipt"></i></div>
                </div>
                <div class="admin-card-value">{{ $allOrders->count() }}</div>
                <div class="admin-card-desc">Currently in kitchen</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Rush Orders</div>
                    <div class="admin-card-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
                <div class="admin-card-value">{{ $allOrders->where('is_rush_order', true)->count() }}</div>
                <div class="admin-card-desc">Need priority attention</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Avg Order Value</div>
                    <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
                </div>
                <div class="admin-card-value">RM {{ number_format($allOrders->avg('total_amount'), 2) }}</div>
                <div class="admin-card-desc">Average order amount</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Total Items</div>
                    <div class="admin-card-icon icon-orange"><i class="fas fa-utensils"></i></div>
                </div>
                <div class="admin-card-value">{{ $allOrders->sum(fn($o) => $o->items->sum('quantity')) }}</div>
                <div class="admin-card-desc">Items being prepared</div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Header Actions --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <h2 class="section-title" style="margin: 0 0 4px 0;">Active Kitchen Orders</h2>
                    <p class="text-muted" style="margin: 0; font-size: 14px;">Currently being prepared in kitchen stations</p>
                </div>
                <div class="section-controls">
                    <a href="{{ route('admin.kitchen.index') }}" class="admin-btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Station Filter Tabs --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
    <div class="station-filter-tabs">
        <a href="{{ route('admin.kitchen.orders') }}"
           class="station-tab {{ !request('station_id') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> All Stations ({{ $totalOrdersCount ?? $orders->count() }})
        </a>
        @foreach($stations as $station)
        <a href="{{ route('admin.kitchen.orders', ['station_id' => $station->id]) }}"
           class="station-tab {{ request('station_id') == $station->id ? 'active' : '' }}">
            @if($station->station_type == 'hot_kitchen')
                <i class="fas fa-fire"></i>
            @elseif($station->station_type == 'cold_kitchen')
                <i class="fas fa-leaf"></i>
            @elseif($station->station_type == 'drinks')
                <i class="fas fa-glass-martini"></i>
            @else
                <i class="fas fa-birthday-cake"></i>
            @endif
            {{ $station->name }}
            ({{ $station->active_loads_count ?? 0 }})
        </a>
        @endforeach
    </div>
</div>

{{-- Orders List --}}
<div class="admin-section">
    @forelse($orders as $order)
    <div class="order-card {{ $order->is_rush_order ? 'priority-high' : '' }}">
        {{-- Order Header --}}
        <div class="order-header">
            <div class="order-info">
                <div>
                    <strong style="font-size: 18px;">Order #{{ $order->confirmation_code }}</strong>
                    @if($order->is_rush_order)
                        <span class="badge badge-danger" style="margin-left: 8px;"><i class="fas fa-fire"></i> RUSH</span>
                    @endif
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> {{ $order->order_time->format('h:i A') }}
                        @if($order->table_number)
                            | <i class="fas fa-chair"></i> Table {{ $order->table_number }}
                        @endif
                        | <i class="fas fa-user"></i> {{ $order->customer_name }}
                    </small>
                </div>
            </div>

            <div style="text-align: right;">
                @php
                    $statusColor = match($order->order_status) {
                        'pending' => 'secondary',
                        'confirmed' => 'primary',
                        'preparing' => 'warning',
                        'ready' => 'success',
                        default => 'info'
                    };

                    // Softer background colors
                    $statusBg = match($order->order_status) {
                        'pending' => 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)',
                        'confirmed' => 'linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%)',
                        'preparing' => 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)',
                        'ready' => 'linear-gradient(135deg, #34d399 0%, #10b981 100%)',
                        default => 'linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%)'
                    };
                @endphp
                <div class="badge badge-{{ $statusColor }}" style="font-size: 13px; padding: 8px 12px; background: {{ $statusBg }} !important; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;">
                    {{ ucfirst($order->order_status) }}
                </div>
                @if($order->estimated_completion_time)
                    <br>
                    <small class="text-muted">
                        ETA: {{ $order->estimated_completion_time->format('h:i A') }}
                        @php
                            $minutesRemaining = now()->diffInMinutes($order->estimated_completion_time, false);
                        @endphp
                        @if($minutesRemaining > 0)
                            ({{ $minutesRemaining }} min)
                        @else
                            <span class="text-danger" style="color: #f87171 !important; font-weight: 600;">(Overdue)</span>
                        @endif
                    </small>
                @endif
            </div>
        </div>

        {{-- Station Assignments --}}
        @if($order->stationAssignments->isNotEmpty())
        <div style="margin: 12px 0; padding: 12px; background: #f8fafc; border-radius: 8px;">
            <strong style="font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                Assigned Stations:
            </strong>
            <div style="display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap;">
                @foreach($order->stationAssignments->unique('station_id') as $assignment)
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: white; border-radius: 6px; font-size: 13px;">
                    @if($assignment->station->station_type == 'hot_kitchen')
                        <i class="fas fa-fire" style="color: #ef4444;"></i>
                    @elseif($assignment->station->station_type == 'cold_kitchen')
                        <i class="fas fa-leaf" style="color: #10b981;"></i>
                    @elseif($assignment->station->station_type == 'drinks')
                        <i class="fas fa-glass-martini" style="color: #f59e0b;"></i>
                    @else
                        <i class="fas fa-birthday-cake" style="color: #a855f7;"></i>
                    @endif
                    <span style="font-weight: 500;">{{ $assignment->station->name }}</span>
                    <span class="badge badge-{{ $assignment->status == 'started' ? 'warning' : 'secondary' }}" style="font-size: 11px;">
                        {{ ucfirst($assignment->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Order Items --}}
        <ul class="order-items-list">
            @foreach($order->items as $item)
                @php
                    // If filtering by station, only show items assigned to that station
                    if ($stationId) {
                        $isAssignedToStation = $order->stationAssignments
                            ->where('order_item_id', $item->id)
                            ->where('station_id', $stationId)
                            ->isNotEmpty();

                        if (!$isAssignedToStation) {
                            continue; // Skip this item
                        }
                    }
                @endphp
            <li>
                <div>
                    <strong>{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Unknown Item' }}</strong>
                    @if($item->menuItem)
                        <span style="margin-left: 8px; font-size: 20px;">
                            {{ $item->menuItem->station_icon }}
                        </span>
                        <span style="margin-left: 4px; font-size: 12px; color: #94a3b8;">
                            ({{ $item->menuItem->station_display_name }})
                        </span>
                    @endif
                    @if($item->special_note)
                        <br>
                        <small style="color: #f59e0b;">
                            <i class="fas fa-sticky-note"></i> {{ $item->special_note }}
                        </small>
                    @endif
                </div>
                <div style="text-align: right;">
                    @php
                        // Use current menu price for kitchen display
                        $displayPrice = $item->menuItem ? ($item->menuItem->price * $item->quantity) : $item->total_price;
                    @endphp
                    <span style="font-weight: 600; color: #64748b;">RM {{ number_format($displayPrice, 2) }}</span>
                    @if($item->menuItem && $item->menuItem->preparation_time)
                        <br>
                        <small class="text-muted"><i class="fas fa-clock"></i> {{ $item->menuItem->preparation_time }} min</small>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>

        {{-- Order Footer --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 2px solid #e5e7eb;">
            <div>
                @php
                    // Calculate station-specific total if filtering using CURRENT menu prices
                    $displayTotal = 0;
                    $fullOrderTotal = 0;

                    foreach ($order->items as $item) {
                        $itemPrice = $item->menuItem ? ($item->menuItem->price * $item->quantity) : $item->total_price;
                        $fullOrderTotal += $itemPrice;

                        if ($stationId) {
                            $isAssignedToStation = $order->stationAssignments
                                ->where('order_item_id', $item->id)
                                ->where('station_id', $stationId)
                                ->isNotEmpty();

                            if ($isAssignedToStation) {
                                $displayTotal += $itemPrice;
                            }
                        }
                    }

                    // If not filtering by station, show full order total
                    if (!$stationId) {
                        $displayTotal = $fullOrderTotal;
                    }
                @endphp
                <strong style="font-size: 16px;">
                    @if($stationId)
                        Station Total: RM {{ number_format($displayTotal, 2) }}
                        <small style="font-size: 12px; color: #64748b; font-weight: normal;">(Full Order: RM {{ number_format($fullOrderTotal, 2) }})</small>
                    @else
                        Total: RM {{ number_format($displayTotal, 2) }}
                    @endif
                </strong>
                @if($order->special_instructions)
                    <br>
                    <small style="color: #ef4444;">
                        <i class="fas fa-comment-dots"></i> <strong>Note:</strong> {{ $order->special_instructions }}
                    </small>
                @endif
            </div>

            <div style="display: flex; gap: 8px;">
                @if($order->order_status == 'confirmed')
                <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="order_status" value="preparing">
                    <button type="submit" class="admin-btn btn-sm btn-warning">
                        <i class="fas fa-play"></i> Start Preparing
                    </button>
                </form>
                @endif

                @if($order->order_status == 'preparing')
                <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="order_status" value="ready">
                    <button type="submit" class="admin-btn btn-sm btn-success">
                        <i class="fas fa-check"></i> Mark Ready
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.kitchen.orders.detail', ['order' => $order->id, 'station_id' => $stationId]) }}" class="admin-btn btn-sm btn-secondary">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>
        </div>

        {{-- Load Information --}}
        @if($order->kitchenLoads->isNotEmpty())
        <div style="margin-top: 12px; padding: 8px 12px; background: #eff6ff; border-radius: 6px; font-size: 12px;">
            <strong>Load Info:</strong>
            @foreach($order->kitchenLoads as $load)
                {{ $load->station->name }}: {{ $load->load_points }} points
                @if($load->estimated_completion_time)
                    (Est: {{ $load->estimated_completion_time->format('h:i A') }})
                @endif
                @if(!$loop->last) | @endif
            @endforeach
        </div>
        @endif
    </div>
    @empty
    <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 12px;">
        <i class="fas fa-clipboard-list" style="font-size: 64px; color: #cbd5e1; margin-bottom: 16px;"></i>
        <h3 style="color: #64748b; margin-bottom: 8px;">No Active Orders</h3>
        <p style="color: #94a3b8;">
            @if(request('station_id'))
                No orders currently assigned to this station.
            @else
                All orders have been completed! Kitchen is clear.
            @endif
        </p>
    </div>
    @endforelse

    {{-- Pagination Links --}}
    @if($orders->hasPages())
    <div style="margin-top: 24px; display: flex; justify-content: center;">
        {{ $orders->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

</div>
    </div>
    </div>
</div>
{{-- End kitchen-page --}}

@endsection

@section('scripts')
<script>
// Auto-refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);

// Show notification for overdue orders
document.addEventListener('DOMContentLoaded', () => {
    const overdueOrders = document.querySelectorAll('.text-danger');
    if (overdueOrders.length > 0) {
        console.log(`� ${overdueOrders.length} overdue order(s) detected`);
    }
});
</script>
@endsection
