<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System - The Stag</title>
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm-modal.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #0f172a;
            color: #fff;
            overflow-x: hidden;
        }

        /* KDS Header */
        .kds-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #3b82f6;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .kds-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .kds-logo {
            font-size: 36px;
            font-weight: bold;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kds-subtitle {
            font-size: 14px;
            color: #94a3b8;
            font-weight: normal;
        }

        .kds-stats {
            display: flex;
            gap: 25px;
        }

        .kds-stat {
            text-align: center;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .kds-stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
        }

        .kds-stat-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .kds-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .kds-time {
            font-size: 24px;
            font-weight: 600;
            color: #e2e8f0;
            font-variant-numeric: tabular-nums;
        }

        .kds-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .kds-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .kds-btn-primary {
            background: #3b82f6;
            border-color: #3b82f6;
        }

        .kds-btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }

        /* Station Filter */
        .kds-station-filter {
            padding: 20px 30px;
            background: #1e293b;
            border-bottom: 1px solid #334155;
        }

        .station-filter-grid {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .station-filter-btn {
            padding: 12px 24px;
            background: #334155;
            border: 2px solid #475569;
            border-radius: 10px;
            color: #e2e8f0;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .station-filter-btn:hover {
            background: #475569;
            border-color: #64748b;
        }

        .station-filter-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        .station-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Orders Container */
        .kds-orders-container {
            padding: 30px;
            max-height: calc(100vh - 220px);
            overflow-y: auto;
        }

        .kds-status-section {
            margin-bottom: 30px;
        }

        .status-section-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #334155 0%, #475569 100%);
            border-radius: 12px;
            border-left: 4px solid;
        }

        .status-section-header.status-pending {
            border-left-color: #f59e0b;
        }

        .status-section-header.status-confirmed {
            border-left-color: #10b981;
        }

        .status-section-header.status-preparing {
            border-left-color: #3b82f6;
        }

        .status-section-header.status-ready {
            border-left-color: #8b5cf6;
        }

        .status-section-header.status-served {
            border-left-color: #6366f1;
        }

        .status-section-title {
            font-size: 22px;
            font-weight: 700;
            flex: 1;
        }

        .status-section-count {
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 16px;
        }

        /* Order Cards Grid */
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 20px;
        }

        .order-card {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: #3b82f6;
        }

        .order-card.rush-order::before {
            background: #ef4444;
        }

        .order-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.3);
        }

        .order-card.rush-order {
            border-color: #ef4444;
            background: #2d1515;
        }

        .order-card.rush-order:hover {
            box-shadow: 0 8px 30px rgba(239, 68, 68, 0.3);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .order-id {
            font-size: 26px;
            font-weight: 800;
            color: #3b82f6;
            line-height: 1;
        }

        .order-card.rush-order .order-id {
            color: #ef4444;
        }

        .order-badges {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-end;
        }

        .order-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rush-badge {
            background: #dc2626;
            color: white;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .confirmation-badge {
            background: #334155;
            color: #e2e8f0;
            font-family: monospace;
        }

        .order-info {
            margin-bottom: 16px;
        }

        .order-customer {
            font-size: 16px;
            color: #e2e8f0;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .order-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .order-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #94a3b8;
        }

        .order-meta-item i {
            width: 16px;
            color: #64748b;
        }

        .order-meta-item strong {
            color: #10b981;
        }

        .order-station-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 16px;
        }

        .station-tag {
            padding: 4px 10px;
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: 6px;
            font-size: 11px;
            color: #93c5fd;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .order-items {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
            max-height: 200px;
            overflow-y: auto;
        }

        .order-items-header {
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .order-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
            color: #e2e8f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-quantity {
            color: #3b82f6;
            font-weight: 700;
            margin-right: 8px;
        }

        .order-special-instructions {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 16px;
        }

        .instructions-header {
            font-size: 11px;
            color: #fbbf24;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .instructions-text {
            font-size: 13px;
            color: #fde68a;
        }

        .order-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-start {
            background: #10b981;
            color: white;
        }

        .btn-start:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-ready {
            background: #8b5cf6;
            color: white;
        }

        .btn-ready:hover {
            background: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }

        .btn-served {
            background: #6366f1;
            color: white;
        }

        .btn-served:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn-complete {
            background: #059669;
            color: white;
        }

        .btn-complete:hover {
            background: #047857;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        }

        .btn-delay {
            background: #f59e0b;
            color: white;
        }

        .btn-delay:hover {
            background: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }

        .empty-state i {
            font-size: 80px;
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-state-text {
            font-size: 16px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Auto Refresh Indicator */
        .auto-refresh-indicator {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid #475569;
            padding: 12px 20px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #94a3b8;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Call Manager Button */
        .call-manager-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: 2px solid #fca5a5;
            padding: 16px 28px;
            border-radius: 16px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 30px rgba(239, 68, 68, 0.4);
            transition: all 0.3s;
            z-index: 1000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .call-manager-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(239, 68, 68, 0.6);
        }

        .call-manager-btn:active {
            transform: translateY(-1px);
        }

        .call-manager-btn i {
            font-size: 20px;
            animation: ring 2s infinite;
        }

        @keyframes ring {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-15deg); }
            20% { transform: rotate(15deg); }
        }

        .refresh-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #475569;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .orders-grid {
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .kds-header {
                flex-direction: column;
                gap: 15px;
            }

            .orders-grid {
                grid-template-columns: 1fr;
            }

            .kds-stats {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- KDS Header -->
    <div class="kds-header">
        <div class="kds-brand">
            <div>
                <div class="kds-logo">
                    &#x1F525; The Stag KDS
                    @if($currentStation)
                        <span style="font-size: 24px; color: #fbbf24; margin-left: 15px;">
                            → {{ $currentStation->name }}
                        </span>
                    @endif
                </div>
                <div class="kds-subtitle">
                    @if($currentStation)
                        My Station - {{ $currentStation->name }}
                    @else
                        Kitchen Display System - All Stations
                    @endif
                </div>
            </div>
        </div>

        <div class="kds-stats">
            <div class="kds-stat">
                <div class="kds-stat-value">{{ $todayStats['pending'] }}</div>
                <div class="kds-stat-label">Pending</div>
            </div>
            <div class="kds-stat">
                <div class="kds-stat-value">{{ $todayStats['preparing'] }}</div>
                <div class="kds-stat-label">Preparing</div>
            </div>
            <div class="kds-stat">
                <div class="kds-stat-value">{{ $todayStats['ready'] }}</div>
                <div class="kds-stat-label">Ready</div>
            </div>
            <div class="kds-stat">
                <div class="kds-stat-value">{{ $todayStats['completed_today'] }}</div>
                <div class="kds-stat-label">Completed</div>
            </div>
        </div>

        <div class="kds-controls">
            <div class="kds-time" id="currentTime"></div>
            @if(!$isKitchenStaff)
            <a href="{{ route('admin.kitchen.index') }}" class="kds-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            @else
            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: inline;">
                @csrf
                <button type="button" onclick="confirmLogout()" class="kds-btn" style="border: none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Station Filter -->
    @if($stations->count() > 0 && !$isKitchenStaff)
    <div class="kds-station-filter">
        <div class="station-filter-grid">
            <button class="station-filter-btn {{ !$stationId ? 'active' : '' }}"
                    onclick="window.location.href='{{ route('admin.kitchen.kds') }}'">
                <i class="fas fa-th-large"></i>
                All Stations
            </button>
            @foreach($stations as $station)
            <button class="station-filter-btn {{ $stationId == $station->id ? 'active' : '' }}"
                    onclick="window.location.href='{{ route('admin.kitchen.kds', ['station_id' => $station->id]) }}'">
                <span>{!! $station->stationType->icon ?? '&#x1F372;' !!}</span>
                {{ $station->name }}
                <span class="station-badge">{{ $station->active_loads_count }}</span>
            </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Orders Container -->
    <div class="kds-orders-container">
        @foreach(['pending', 'confirmed', 'preparing', 'ready', 'completed'] as $status)
            @if($orders->has($status) && $orders->get($status)->count() > 0)
            <div class="kds-status-section">
                <div class="status-section-header status-{{ $status }}">
                    <h2 class="status-section-title">
                        @if($status == 'pending') ⏳ Pending Orders
                        @elseif($status == 'confirmed') ✅ Confirmed Orders
                        @elseif($status == 'preparing') &#x1F373; Preparing
                        @elseif($status == 'ready') &#x1F514; Ready for Service
                        @elseif($status == 'completed') ✔️ Completed
                        @endif
                    </h2>
                    <div class="status-section-count">{{ $orders->get($status)->count() }}</div>
                </div>

                <div class="orders-grid">
                    @foreach($orders->get($status)->sortBy('order_time') as $order)
                    <div class="order-card {{ $order->is_rush_order ? 'rush-order' : '' }}">
                        <div class="order-header">
                            <div class="order-id">#{{ $order->id }}</div>
                            <div class="order-badges">
                                @if($order->confirmation_code)
                                <span class="order-badge confirmation-badge">{{ $order->confirmation_code }}</span>
                                @endif
                                @if($order->is_rush_order)
                                <span class="order-badge rush-badge">
                                    <i class="fas fa-bolt"></i> RUSH
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="order-info">
                            <div class="order-customer">
                                <i class="fas fa-user"></i> {{ $order->user->name ?? 'Walk-in Customer' }}
                            </div>

                            <div class="order-meta">
                                <div class="order-meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $order->order_time->format('h:i A') }}
                                </div>
                                <div class="order-meta-item">
                                    <i class="fas fa-dollar-sign"></i>
                                    <strong>RM {{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                                @if($order->table || $order->table_number)
                                <div class="order-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    @if($order->table)
                                        Table {{ $order->table->table_number }}
                                    @else
                                        {{ $order->table_number }}
                                    @endif
                                </div>
                                @endif
                                <div class="order-meta-item">
                                    <i class="fas fa-tag"></i>
                                    {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                </div>
                            </div>
                        </div>

                        @if(!$stationId && $order->stationAssignments && $order->stationAssignments->count() > 0)
                        <div class="order-station-tags">
                            @foreach($order->stationAssignments->unique('station_id') as $assignment)
                                <span class="station-tag">
                                    {!! $assignment->station->stationType->icon ?? '&#x1F372;' !!}
                                    {{ $assignment->station->name }}
                                </span>
                            @endforeach
                        </div>
                        @endif

                        @php
                            // Filter items for current station only
                            if ($order->stationAssignments && $order->stationAssignments->count() > 0) {
                                $stationItems = $order->stationAssignments
                                    ->when($stationId, function($assignments) use ($stationId) {
                                        return $assignments->where('station_id', $stationId);
                                    })
                                    ->pluck('orderItem')
                                    ->filter();
                            } else {
                                // Fallback for orders without station assignments (e.g., QR/guest orders)
                                $stationItems = $order->items;
                            }
                        @endphp

                        @if($stationItems && $stationItems->count() > 0)
                        <div class="order-items">
                            <div class="order-items-header">
                                @if($stationId)
                                    My Items ({{ $stationItems->count() }})
                                @else
                                    Items ({{ $order->items->count() }})
                                @endif
                            </div>
                            @foreach($stationItems as $item)
                            <div class="order-item">
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                {{ $item->menuItem->name ?? 'Item' }}
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if($order->special_instructions && count($order->special_instructions) > 0)
                        <div class="order-special-instructions">
                            <div class="instructions-header">
                                <i class="fas fa-exclamation-triangle"></i> Special Instructions
                            </div>
                            <div class="instructions-text">
                                @foreach($order->special_instructions as $instruction)
                                    @if($instruction)
                                        • {{ $instruction }}<br>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="order-actions">
                            @if($status == 'pending' || $status == 'confirmed')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'preparing')" class="action-btn btn-start">
                                    <i class="fas fa-play"></i> Start Preparing
                                </button>
                                @if($isKitchenStaff)
                                <button onclick="needMoreTime({{ $order->id }})" class="action-btn btn-delay">
                                    <i class="fas fa-clock"></i> Need More Time
                                </button>
                                @endif
                            @elseif($status == 'preparing')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'ready')" class="action-btn btn-ready">
                                    <i class="fas fa-bell"></i> Mark Ready
                                </button>
                                @if($isKitchenStaff)
                                <button onclick="needMoreTime({{ $order->id }})" class="action-btn btn-delay">
                                    <i class="fas fa-clock"></i> Need More Time
                                </button>
                                @endif
                            @elseif($status == 'ready')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'completed')" class="action-btn btn-complete">
                                    <i class="fas fa-check"></i> Mark Complete
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach

        @if($orders->flatten()->count() == 0)
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <div class="empty-state-title">All caught up!</div>
            <div class="empty-state-text">No active orders at the moment.</div>
        </div>
        @endif
    </div>

    <!-- Auto Refresh Indicator -->
    <div class="auto-refresh-indicator">
        <div class="refresh-spinner"></div>
        Auto-refresh in <span id="countdown">{{ $isKitchenStaff ? 10 : 30 }}</span>s
    </div>

    <!-- Call Manager Button (Kitchen Staff Only) -->
    @if($isKitchenStaff)
    <button onclick="callManager()" class="call-manager-btn" title="Need help? Call the manager">
        <i class="fas fa-phone-alt"></i>
        <span>CALL MANAGER</span>
    </button>
    @endif

    <!-- Toast & Confirm Modal -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/confirm-modal.js') }}"></script>

    <!-- KDS Scroll Position Memory (Always Active) -->
    <script>
        (function() {
            const SCROLL_KEY = 'kds-scroll-position';
            const LAST_ORDER_COUNT_KEY = 'kds-last-order-count';

            // Get current order count
            function getOrderCount() {
                return document.querySelectorAll('.order-card').length;
            }

            // Track if we've restored scroll (don't save until after restore)
            let hasRestored = false;

            // Get the scrollable container
            const scrollContainer = document.querySelector('.kds-orders-container');

            // Save scroll position on scroll event (immediate)
            if (scrollContainer) {
                scrollContainer.addEventListener('scroll', () => {
                    if (hasRestored) {
                        console.log('💾 Saving scroll position:', scrollContainer.scrollTop);
                        sessionStorage.setItem(SCROLL_KEY, scrollContainer.scrollTop);
                        sessionStorage.setItem(LAST_ORDER_COUNT_KEY, getOrderCount());
                    }
                });

                // Also save periodically as backup
                setInterval(() => {
                    if (hasRestored && scrollContainer.scrollTop > 0) {
                        sessionStorage.setItem(SCROLL_KEY, scrollContainer.scrollTop);
                        sessionStorage.setItem(LAST_ORDER_COUNT_KEY, getOrderCount());
                    }
                }, 2000);
            }

            // Restore scroll position on load
            document.addEventListener('DOMContentLoaded', () => {
                const savedPosition = sessionStorage.getItem(SCROLL_KEY);
                const lastOrderCount = parseInt(sessionStorage.getItem(LAST_ORDER_COUNT_KEY) || '0');
                const currentOrderCount = getOrderCount();

                console.log('🔄 Restoring scroll position:', savedPosition);

                // Wait for container to be available
                const waitForContainer = () => {
                    const container = document.querySelector('.kds-orders-container');

                    if (!container) {
                        console.log('⏳ Container not ready, waiting...');
                        setTimeout(waitForContainer, 100);
                        return;
                    }

                    console.log('✓ Container found!');

                    if (savedPosition && savedPosition !== '0') {
                        const targetPosition = parseInt(savedPosition);

                        // Try multiple times with increasing delays to fight any auto-scroll
                        const scrollToPosition = () => {
                            const maxScroll = container.scrollHeight - container.clientHeight;
                            const validPosition = Math.min(targetPosition, Math.max(0, maxScroll));

                            console.log('📍 Attempting scroll to:', validPosition, 'Current:', container.scrollTop);
                            container.scrollTop = validPosition;
                        };

                        // Scroll immediately
                        scrollToPosition();

                        // Scroll again after 50ms
                        setTimeout(scrollToPosition, 50);

                        // Scroll again after 100ms
                        setTimeout(scrollToPosition, 100);

                        // Scroll again after 200ms
                        setTimeout(scrollToPosition, 200);

                        // Final scroll after 500ms
                        setTimeout(() => {
                            scrollToPosition();
                            console.log('✅ Final scroll position:', container.scrollTop);

                            // Notify if new orders arrived while scrolled down
                            if (currentOrderCount > lastOrderCount && targetPosition > 200) {
                                const newCount = currentOrderCount - lastOrderCount;
                                Toast.info('New Orders', `${newCount} new order(s) received above!`);
                            }
                        }, 500);
                    }

                    // Mark as restored so saving can begin
                    setTimeout(() => {
                        hasRestored = true;
                        console.log('✅ Scroll restoration complete, will now save positions');
                    }, 600);
                };

                // Start waiting for container
                waitForContainer();
            });

            // Save before page unload
            window.addEventListener('beforeunload', () => {
                if (scrollContainer && scrollContainer.scrollTop > 0) {
                    sessionStorage.setItem(SCROLL_KEY, scrollContainer.scrollTop);
                    sessionStorage.setItem(LAST_ORDER_COUNT_KEY, getOrderCount());
                }
            });
        })();
    </script>

    <!-- Laravel Echo for Real-time Updates (Optional - requires broadcasting setup) -->
    @if(config('broadcasting.default') !== 'null')
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>
    <script>
        // Initialize Laravel Echo with Pusher
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            encrypted: true
        });

        // Listen for order status updates
        Echo.channel('kitchen-display')
            .listen('.order.status.updated', (e) => {
                console.log('Order status updated:', e);
                Toast.info('Order Updated', `Order #${e.order_id} status changed to ${e.new_status}`);

                // Auto-reload after 2 seconds to show updated orders
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });

        console.log('✅ Real-time broadcasting connected');
    </script>
    @else
    <!-- Broadcasting not configured - using polling fallback -->
    <script>
        console.log('ℹ️ Broadcasting not configured. Using auto-refresh polling (30s).');
    </script>
    @endif

    <script>
        // Call Manager function (Kitchen Staff)
        async function callManager() {
            const confirmed = await showConfirm(
                '📞 Call Manager',
                'This will send an alert to the manager that you need assistance. Continue?',
                'warning',
                'Yes, Call Manager',
                'Cancel'
            );

            if (!confirmed) return;

            try {
                const response = await fetch('/admin/kitchen/call-manager', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        station_id: {{ $stationId ?? 'null' }},
                        station_name: '{{ $currentStation->name ?? "Unknown" }}',
                        chef_name: '{{ auth()->user()->name }}'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Toast.success('Manager Notified', 'The manager has been notified. Help is on the way!');
                } else {
                    Toast.error('Error', data.message || 'Failed to call manager');
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.error('Error', 'Failed to send alert to manager');
            }
        }

        // Need More Time function (Kitchen Staff)
        async function needMoreTime(orderId) {
            // Pause auto-refresh while user is interacting
            pauseCountdown();

            const confirmed = await showConfirm(
                '⏱ Need More Time?',
                'This will add 10 minutes to the estimated preparation time and notify the manager.',
                'warning',
                'Yes, Need More Time',
                'Cancel'
            );

            if (!confirmed) {
                // Resume countdown if user cancelled
                resumeCountdown();
                return;
            }

            try {
                const response = await fetch(`/admin/order/${orderId}/need-more-time`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        additional_minutes: 10
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Toast.success('Time Extended', 'Added 10 minutes to preparation time. Manager has been notified.');
                    // Give user 3 seconds to see the success message before reload
                    setTimeout(() => window.location.reload(), 3000);
                } else {
                    Toast.error('Error', data.message || 'Failed to extend time');
                    // Resume countdown if failed
                    resumeCountdown();
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.error('Error', 'Failed to extend preparation time');
                // Resume countdown if error
                resumeCountdown();
            }
        }

        // Logout confirmation for kitchen staff
        async function confirmLogout() {
            const confirmed = await showConfirm(
                'Logout Confirmation',
                'Are you sure you want to logout from the Kitchen Display System?',
                'warning',
                'Logout',
                'Cancel'
            );

            if (confirmed) {
                document.getElementById('logout-form').submit();
            }
        }

        // Update time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            document.getElementById('currentTime').textContent = timeString;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Update order status
        async function updateOrderStatus(orderId, status) {
            const statusLabels = {
                'preparing': 'Start Preparing',
                'ready': 'Mark as Ready',
                'completed': 'Mark as Completed'
            };

            // Pause auto-refresh while user is interacting
            pauseCountdown();

            const confirmed = await showConfirm(
                'Update Order Status?',
                `Are you sure you want to ${statusLabels[status] || status}?`,
                'info',
                'Confirm',
                'Cancel'
            );

            if (!confirmed) {
                // Resume countdown if user cancelled
                resumeCountdown();
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
                        order_status: status,
                        station_id: {{ $stationId ?? 'null' }}
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Toast.success('Success', `Order #${orderId} updated to ${status}`);
                    // Give user 3 seconds to see the success message before reload
                    setTimeout(() => window.location.reload(), 3000);
                } else {
                    Toast.error('Error', data.message || 'Failed to update order');
                    // Resume countdown if failed
                    resumeCountdown();
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.error('Error', 'Failed to update order status');
                // Resume countdown if error
                resumeCountdown();
            }
        }

        // Auto-refresh countdown and reload (10 seconds for kitchen staff, 30 for admin)
        let countdown = {{ $isKitchenStaff ? 10 : 30 }};
        const countdownEl = document.getElementById('countdown');
        let countdownInterval = null;
        let isPaused = false;
        let savedCountdown = countdown; // Save the countdown value when pausing

        function startCountdown() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            countdownInterval = setInterval(() => {
                if (!isPaused) {
                    countdown--;
                    countdownEl.textContent = countdown;

                    if (countdown <= 0) {
                        window.location.reload();
                    }
                }
            }, 1000);
        }

        function pauseCountdown() {
            isPaused = true;
            savedCountdown = countdown; // Save current countdown value
            countdownEl.parentElement.style.opacity = '0.5';
        }

        function resumeCountdown() {
            isPaused = false;
            countdown = savedCountdown; // Restore saved countdown value (not reset!)
            countdownEl.textContent = countdown;
            countdownEl.parentElement.style.opacity = '1';
        }

        startCountdown();

        // Flash messages from session
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                @if(session('success'))
                    Toast.success('Success', '{{ session('success') }}');
                @endif
                @if(session('error'))
                    Toast.error('Error', '{{ session('error') }}');
                @endif
            }, 100);
        });
    </script>
</body>
</html>
