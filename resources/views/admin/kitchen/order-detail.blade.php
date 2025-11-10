@extends('layouts.admin')

@section('title', 'Order Details - Kitchen')
@section('page-title', 'Kitchen Order Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.detail-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.detail-section {
    margin-bottom: 24px;
}

.detail-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.detail-value {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.item-row {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
}

.item-row:hover {
    background: #f9fafb;
}

.item-row:last-child {
    border-bottom: none;
}

.station-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f3f4f6;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    margin-right: 8px;
    margin-bottom: 8px;
}

.station-badge.hot {
    background: #fee2e2;
    color: #991b1b;
}

.station-badge.cold {
    background: #d1fae5;
    color: #065f46;
}

.station-badge.drinks {
    background: #fef3c7;
    color: #92400e;
}

.station-badge.desserts {
    background: #f3e8ff;
    color: #6b21a8;
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.timeline {
    position: relative;
    padding-left: 32px;
}

.timeline-item {
    position: relative;
    padding-bottom: 24px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 6px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #d1d5db;
}

.timeline-item.active::before {
    background: #6366f1;
}

.timeline-item.completed::before {
    background: #10b981;
}

.timeline-item::after {
    content: '';
    position: absolute;
    left: -21px;
    top: 18px;
    width: 2px;
    height: calc(100% - 12px);
    background: #e5e7eb;
}

.timeline-item:last-child::after {
    display: none;
}
</style>
@endsection

@section('content')

<div class="kitchen-page">
    {{-- Summary Stats Cards --}}
    <div class="kitchen-section">
        <div class="admin-cards" style="margin-bottom: 0;">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Order Status</div>
                    <div class="admin-card-icon icon-blue"><i class="fas fa-info-circle"></i></div>
                </div>
                <div class="admin-card-value">{{ ucfirst($order->order_status) }}</div>
                <div class="admin-card-desc">Current status</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Total Items</div>
                    <div class="admin-card-icon icon-green"><i class="fas fa-utensils"></i></div>
                </div>
                <div class="admin-card-value">{{ $order->items->sum('quantity') }}</div>
                <div class="admin-card-desc">
                    @if($stationId && $currentStation)
                        {{ $order->items->filter(fn($item) => $order->stationAssignments->where('order_item_id', $item->id)->where('station_id', $stationId)->isNotEmpty())->sum('quantity') }} for {{ $currentStation->name }}
                    @else
                        Total items in order
                    @endif
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">Order Time</div>
                    <div class="admin-card-icon icon-orange"><i class="fas fa-clock"></i></div>
                </div>
                <div class="admin-card-value">{{ $order->order_time->format('h:i A') }}</div>
                <div class="admin-card-desc">{{ $order->order_time->diffForHumans() }}</div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        @if($stationId && $currentStation)
                            Station Total
                        @else
                            Order Total
                        @endif
                    </div>
                    <div class="admin-card-icon icon-purple"><i class="fas fa-dollar-sign"></i></div>
                </div>
                @php
                    $displayTotal = 0;
                    foreach ($order->items as $item) {
                        $itemPrice = $item->menuItem ? ($item->menuItem->price * $item->quantity) : $item->total_price;

                        if ($stationId) {
                            $isAssignedToStation = $order->stationAssignments
                                ->where('order_item_id', $item->id)
                                ->where('station_id', $stationId)
                                ->isNotEmpty();
                            if ($isAssignedToStation) {
                                $displayTotal += $itemPrice;
                            }
                        } else {
                            $displayTotal += $itemPrice;
                        }
                    }
                @endphp
                <div class="admin-card-value">RM {{ number_format($displayTotal, 2) }}</div>
                <div class="admin-card-desc">Current menu prices</div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <a href="{{ route('admin.kitchen.orders', ['station_id' => $stationId]) }}" class="admin-btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>

                <div style="display: flex; gap: 12px;">
                    @if($order->order_status == 'confirmed')
                    <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="order_status" value="preparing">
                        <button type="submit" class="admin-btn btn-warning">
                            <i class="fas fa-play"></i> Start Preparing
                        </button>
                    </form>
                    @endif

                    @if($order->order_status == 'preparing')
                    <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="order_status" value="ready">
                        <button type="submit" class="admin-btn btn-success">
                            <i class="fas fa-check"></i> Mark Ready
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('admin.order.show', $order->id) }}" class="admin-btn btn-primary">
                        <i class="fas fa-file-alt"></i> Full Order Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            {{-- Left Column --}}
            <div>
                {{-- Order Information --}}
                <div class="detail-card">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1f2937;">
                        <i class="fas fa-receipt"></i> Order Information
                    </h3>

                    <div class="detail-section">
                        <div class="detail-label">Order Number</div>
                        <div class="detail-value">#{{ $order->confirmation_code }}</div>
                    </div>

                    <div class="detail-section">
                        <div class="detail-label">Customer</div>
                        <div class="detail-value">
                            <i class="fas fa-user"></i> {{ $order->customer_name }}
                        </div>
                    </div>

                    @if($order->table_number)
                    <div class="detail-section">
                        <div class="detail-label">Table</div>
                        <div class="detail-value">
                            <i class="fas fa-chair"></i> Table {{ $order->table_number }}
                        </div>
                    </div>
                    @endif

                    <div class="detail-section">
                        <div class="detail-label">Order Type</div>
                        <div class="detail-value">
                            <i class="fas fa-{{ $order->order_type == 'dine_in' ? 'utensils' : 'shopping-bag' }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'N/A')) }}
                        </div>
                    </div>

                    @if($order->is_rush_order)
                    <div class="detail-section">
                        <div class="detail-label">Priority</div>
                        <div class="detail-value" style="color: #dc2626;">
                            <i class="fas fa-fire"></i> RUSH ORDER
                        </div>
                    </div>
                    @endif

                    @if($order->estimated_completion_time)
                    <div class="detail-section">
                        <div class="detail-label">Estimated Completion</div>
                        <div class="detail-value">
                            <i class="fas fa-clock"></i> {{ $order->estimated_completion_time->format('h:i A') }}
                            @php
                                $minutesRemaining = now()->diffInMinutes($order->estimated_completion_time, false);
                            @endphp
                            @if($minutesRemaining > 0)
                                <span style="font-size: 14px; color: #6b7280;">({{ $minutesRemaining }} min remaining)</span>
                            @else
                                <span style="font-size: 14px; color: #dc2626; font-weight: 700;">(OVERDUE by {{ abs($minutesRemaining) }} min)</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($order->special_instructions)
                    <div class="detail-section">
                        <div class="detail-label">Special Instructions</div>
                        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 8px; margin-top: 8px;">
                            <i class="fas fa-comment-dots" style="color: #f59e0b;"></i>
                            <span style="font-weight: 600; color: #92400e;">{{ is_array($order->special_instructions) ? implode(', ', $order->special_instructions) : $order->special_instructions }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Station Assignments --}}
                @if(!$stationId)
                <div class="detail-card">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1f2937;">
                        <i class="fas fa-map-marked-alt"></i> Station Assignments
                    </h3>

                    @php
                        $uniqueStations = $order->stationAssignments->unique('station_id');
                    @endphp

                    @forelse($uniqueStations as $assignment)
                        @php
                            $stationType = $assignment->station->station_type ?? 'general';
                            $badgeClass = match($stationType) {
                                'hot_kitchen' => 'hot',
                                'cold_kitchen' => 'cold',
                                'drinks' => 'drinks',
                                'desserts' => 'desserts',
                                default => ''
                            };
                            $icon = match($stationType) {
                                'hot_kitchen' => 'fire',
                                'cold_kitchen' => 'leaf',
                                'drinks' => 'glass-martini',
                                'desserts' => 'birthday-cake',
                                default => 'utensils'
                            };
                        @endphp
                        <div class="station-badge {{ $badgeClass }}">
                            <i class="fas fa-{{ $icon }}"></i>
                            {{ $assignment->station->name }}
                            <span class="badge badge-{{ $assignment->status == 'started' ? 'warning' : 'secondary' }}" style="font-size: 10px; margin-left: 4px;">
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </div>
                    @empty
                        <p style="color: #6b7280; font-size: 14px;">No station assignments yet.</p>
                    @endforelse
                </div>
                @endif
            </div>

            {{-- Right Column --}}
            <div>
                {{-- Order Items --}}
                <div class="detail-card">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1f2937;">
                        <i class="fas fa-list"></i>
                        @if($stationId && $currentStation)
                            Items for {{ $currentStation->name }}
                        @else
                            All Order Items
                        @endif
                    </h3>

                    @php
                        $itemsToShow = $order->items;

                        if ($stationId) {
                            $itemsToShow = $order->items->filter(function($item) use ($order, $stationId) {
                                return $order->stationAssignments
                                    ->where('order_item_id', $item->id)
                                    ->where('station_id', $stationId)
                                    ->isNotEmpty();
                            });
                        }
                    @endphp

                    @forelse($itemsToShow as $item)
                    <div class="item-row">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 16px; color: #1f2937; margin-bottom: 4px;">
                                {{ $item->quantity }}x {{ $item->menuItem->name ?? 'Unknown Item' }}
                            </div>

                            @if($item->menuItem)
                            <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">
                                {{ $item->menuItem->station_icon }}
                                <span style="margin-left: 4px;">{{ $item->menuItem->station_display_name }}</span>

                                @if($item->menuItem->preparation_time)
                                    <span style="margin-left: 12px;">
                                        <i class="fas fa-clock"></i> {{ $item->menuItem->preparation_time }} min
                                    </span>
                                @endif
                            </div>
                            @endif

                            @if($item->special_note)
                            <div style="font-size: 12px; color: #f59e0b; margin-top: 6px;">
                                <i class="fas fa-sticky-note"></i> {{ $item->special_note }}
                            </div>
                            @endif

                            @php
                                $addons = $item->customizations()->where('customization_type', 'addon')->get();
                            @endphp
                            @if($addons->count() > 0)
                            <div style="font-size: 12px; color: #3b82f6; margin-top: 6px; font-style: italic;">
                                <i class="fas fa-puzzle-piece"></i> {{ $addons->pluck('customization_value')->join(', ') }}
                            </div>
                            @endif

                            {{-- Show which station this item is assigned to (only in All Stations view) --}}
                            @if(!$stationId)
                                @php
                                    $itemStations = $order->stationAssignments->where('order_item_id', $item->id);
                                @endphp
                                @if($itemStations->isNotEmpty())
                                <div style="margin-top: 8px;">
                                    @foreach($itemStations as $assignment)
                                        @php
                                            $stationType = $assignment->station->station_type ?? 'general';
                                            $badgeClass = match($stationType) {
                                                'hot_kitchen' => 'hot',
                                                'cold_kitchen' => 'cold',
                                                'drinks' => 'drinks',
                                                'desserts' => 'desserts',
                                                default => ''
                                            };
                                        @endphp
                                        <span class="station-badge {{ $badgeClass }}" style="font-size: 11px; padding: 4px 8px;">
                                            {{ $assignment->station->name }}
                                        </span>
                                    @endforeach
                                </div>
                                @endif
                            @endif
                        </div>

                        <div style="text-align: right;">
                            @php
                                $displayPrice = $item->menuItem ? ($item->menuItem->price * $item->quantity) : $item->total_price;
                            @endphp
                            <div style="font-weight: 700; font-size: 18px; color: #6366f1;">
                                RM {{ number_format($displayPrice, 2) }}
                            </div>
                            @if($item->menuItem)
                            <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                                RM {{ number_format($item->menuItem->price, 2) }} each
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p style="color: #6b7280; text-align: center; padding: 40px;">
                        @if($stationId)
                            No items assigned to this station.
                        @else
                            No items in this order.
                        @endif
                    </p>
                    @endforelse

                    {{-- Total --}}
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 18px; font-weight: 700; color: #1f2937;">
                            @if($stationId && $currentStation)
                                Station Total:
                            @else
                                Order Total:
                            @endif
                        </span>
                        <span style="font-size: 24px; font-weight: 900; color: #6366f1;">
                            RM {{ number_format($displayTotal, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Kitchen Load Info --}}
                @if($order->kitchenLoads->isNotEmpty())
                <div class="detail-card">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1f2937;">
                        <i class="fas fa-tachometer-alt"></i> Kitchen Load Information
                    </h3>

                    @foreach($order->kitchenLoads as $load)
                    <div style="margin-bottom: 16px; padding: 12px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #6366f1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-weight: 600; color: #1f2937;">{{ $load->station->name }}</span>
                            <span style="font-weight: 700; color: #6366f1;">{{ $load->load_points }} points</span>
                        </div>

                        @if($load->estimated_completion_time)
                        <div style="font-size: 13px; color: #6b7280;">
                            <i class="fas fa-clock"></i> Est. completion: {{ $load->estimated_completion_time->format('h:i A') }}
                        </div>
                        @endif

                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">
                            Status: <span style="font-weight: 600;">{{ ucfirst($load->status) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Auto-refresh every 30 seconds
setTimeout(() => {
    location.reload();
}, 30000);
</script>
@endsection
