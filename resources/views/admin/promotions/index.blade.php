@extends('layouts.admin')

@section('title', 'Promotions Management')
@section('page-title', 'Promotions Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
/* Additional styles for toggle button */
.toggle-btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.toggle-btn.active {
    background: #dcfce7;
    color: #16a34a;
}

.toggle-btn.inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.toggle-btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

/* Grid Actions Layout */
.action-btn {
    padding: 8px 10px;
    border-radius: 6px;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.confirm-btn {
    background: #dcfce7;
    color: #16a34a;
}

.view-btn {
    background: #dbeafe;
    color: #2563eb;
}

.edit-btn {
    background: #fef3c7;
    color: #d97706;
}

.delete-btn {
    background: #fee2e2;
    color: #dc2626;
}

.promo-code-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: #f3f4f6;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.promo-code-badge:hover {
    background: #e5e7eb;
}

.promo-code-badge.hidden .code-text {
    filter: blur(6px);
    user-select: none;
}

.promo-code-badge .reveal-icon {
    font-size: 0.85rem;
    color: #6b7280;
}

.promo-code-badge.hidden .reveal-icon::before {
    content: '\f070'; /* eye-slash */
}

.promo-code-badge:not(.hidden) .reveal-icon::before {
    content: '\f06e'; /* eye */
}

.discount-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
}

.discount-badge.percentage {
    background: #dbeafe;
    color: #2563eb;
}

.discount-badge.fixed {
    background: #fef3c7;
    color: #d97706;
}

.value-tooltip {
    position: relative;
    cursor: help;
}

.admin-tabs {
    display: flex;
    gap: 8px;
    background: white;
    padding: 12px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    border: 1px solid var(--muted);
}

.admin-tab {
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    background: transparent;
    color: var(--text-2);
    border: none;
    font-size: 14px;
}

.admin-tab.active {
    background: var(--brand);
    color: white;
}

.admin-tab:hover:not(.active) {
    background: var(--bg);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* ===== Empty State ===== */
   .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px 20px;
      text-align: center;
    }
    
    .empty-state-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }
    
    .empty-state-title {
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .empty-state-text {
      color: var(--text-3);
      font-size: 14px;
    }

/* ===== RESPONSIVE DESIGN - 4-TIER BREAKPOINT SYSTEM ===== */

/* Large Desktop (≥1600px) - 30-40% larger */
@media (min-width: 1600px) {
    .admin-cards {
        gap: 28px;
        margin-bottom: 40px;
    }
    .admin-card {
        padding: 32px;
    }
    .admin-card-title {
        font-size: 18px;
    }
    .admin-card-value {
        font-size: 36px;
    }
    .admin-tabs {
        padding: 16px;
        gap: 12px;
    }
    .admin-tab {
        padding: 14px 28px;
        font-size: 16px;
    }
    .section-title {
        font-size: 24px;
    }
    .admin-table th,
    .admin-table td {
        padding: 16px;
        font-size: 15px;
    }
}

/* Tablet (769px-1199px) - 20-25% smaller */
@media (max-width: 1199px) and (min-width: 769px) {
    .admin-cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 0;
    }
    .admin-card {
        padding: 18px;
    }
    .admin-card-title {
        font-size: 13px;
    }
    .admin-card-icon {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
    .admin-card-value {
        font-size: 22px;
    }
    .admin-card-desc {
        font-size: 12px;
    }
    .admin-tabs {
        padding: 10px;
        gap: 6px;
        flex-wrap: wrap;
    }
    .admin-tab {
        padding: 8px 16px;
        font-size: 13px;
    }
    .section-title {
        font-size: 16px;
    }
    .search-filter {
        gap: 12px;
    }
    .admin-table th,
    .admin-table td {
        padding: 10px;
        font-size: 13px;
    }
    .discount-badge,
    .promo-code-badge {
        font-size: 12px;
        padding: 5px 10px;
    }
    .action-btn {
        padding: 6px 8px;
        font-size: 12px;
    }
}

/* Mobile (≤768px) - 35-40% smaller, single column */
@media (max-width: 768px) {
    .admin-cards {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }
    .admin-card {
        padding: 16px;
    }
    .admin-card-title {
        font-size: 12px;
    }
    .admin-card-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    .admin-card-value {
        font-size: 20px;
    }
    .admin-card-desc {
        font-size: 11px;
    }
    .admin-tabs {
        padding: 8px;
        gap: 6px;
        flex-wrap: wrap;
    }
    .admin-tab {
        padding: 8px 14px;
        font-size: 12px;
        flex: 1 1 calc(50% - 6px);
        min-width: 120px;
        justify-content: center;
    }
    .admin-tab i {
        font-size: 11px;
    }
    .admin-section {
        padding: 16px;
    }
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    .section-title {
        font-size: 15px;
    }
    .search-filter {
        flex-direction: column;
        gap: 10px;
    }
    .admin-btn {
        width: 100%;
        justify-content: center;
        padding: 10px 16px;
        font-size: 13px;
    }
    /* Table Responsive - Horizontal scroll */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .admin-table {
        min-width: 900px;
    }
    .admin-table th,
    .admin-table td {
        padding: 8px;
        font-size: 12px;
    }
    .discount-badge,
    .promo-code-badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    .promo-code-badge .code-text {
        font-size: 11px;
    }
    /* Action buttons grid - stack on mobile */
    .admin-table td:last-child > div {
        grid-template-columns: repeat(2, 1fr);
        gap: 4px;
        max-width: 120px;
    }
    .action-btn {
        padding: 6px;
        font-size: 11px;
    }
    .toggle-btn {
        padding: 5px 10px;
        font-size: 11px;
    }
    /* Status badges */
    .status {
        font-size: 11px;
        padding: 4px 8px;
    }
    .status i {
        font-size: 10px;
    }
    /* Empty state */
    .empty-state {
        padding: 40px 16px;
    }
    .empty-state-icon {
        font-size: 36px;
    }
    .empty-state-title {
        font-size: 16px;
    }
    .empty-state-text {
        font-size: 12px;
    }
}

/* Small Mobile (≤480px) - Ultra compact */
@media (max-width: 480px) {
    .admin-cards {
        gap: 10px;
    }
    .admin-card {
        padding: 12px;
    }
    .admin-card-header {
        margin-bottom: 10px;
    }
    .admin-card-title {
        font-size: 11px;
    }
    .admin-card-icon {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    .admin-card-value {
        font-size: 18px;
    }
    .admin-card-desc {
        font-size: 10px;
    }
    .admin-tabs {
        padding: 6px;
        gap: 4px;
    }
    .admin-tab {
        padding: 6px 10px;
        font-size: 11px;
        flex: 1 1 100%;
    }
    .admin-section {
        padding: 12px;
    }
    .section-title {
        font-size: 14px;
    }
    .admin-btn {
        padding: 8px 12px;
        font-size: 12px;
    }
    .admin-table {
        min-width: 800px;
    }
    .admin-table th,
    .admin-table td {
        padding: 6px;
        font-size: 11px;
    }
    .discount-badge,
    .promo-code-badge {
        font-size: 10px;
        padding: 3px 6px;
    }
    .action-btn {
        padding: 5px;
        font-size: 10px;
    }
    .toggle-btn {
        padding: 4px 8px;
        font-size: 10px;
    }
}
</style>
@endsection

@section('content')
<!-- Analytics Summary Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Revenue</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($analyticsSummary['total_revenue'], 2) }}</div>
        <div class="admin-card-desc">From all promotions</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Discount Given</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-tags"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($analyticsSummary['total_discount'], 2) }}</div>
        <div class="admin-card-desc">Total savings for customers</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Usage</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="admin-card-value">{{ number_format($analyticsSummary['total_usage']) }}</div>
        <div class="admin-card-desc">{{ $analyticsSummary['unique_users'] }} unique users</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Top Performer</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-trophy"></i></div>
        </div>
        @if($analyticsSummary['top_promotion'])
            <div class="admin-card-value" style="font-size: 1rem;">{{ Str::limit($analyticsSummary['top_promotion']['name'], 20) }}</div>
            <div class="admin-card-desc">RM {{ number_format($analyticsSummary['top_promotion']['revenue'], 2) }} revenue</div>
        @else
            <div class="admin-card-value">-</div>
            <div class="admin-card-desc">No data yet</div>
        @endif
    </div>
</div>

<!-- Additional Stats Row -->
<div class="admin-cards" style="margin-top: 10px;">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Active Promotions</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $analyticsSummary['active_promotions'] }}</div>
        <div class="admin-card-desc">Currently active</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Avg Order Value</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-receipt"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($analyticsSummary['average_order_value'], 2) }}</div>
        <div class="admin-card-desc">Per promotion order</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Avg Discount</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-percentage"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($analyticsSummary['average_discount'], 2) }}</div>
        <div class="admin-card-desc">Per transaction</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Promotions</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-tags"></i></div>
        </div>
        <div class="admin-card-value">{{ $promotions->total() }}</div>
        <div class="admin-card-desc">All promotions</div>
    </div>
</div>

{{-- Notifications will be shown via JavaScript --}}

<!-- Type Filter Tabs -->
<div class="admin-tabs">
    <button class="admin-tab {{ !request('type') ? 'active' : '' }}" onclick="filterByType('')">
        <i class="fas fa-th"></i> All Types
    </button>
    <button class="admin-tab {{ request('type') == 'promo_code' ? 'active' : '' }}" onclick="filterByType('promo_code')">
        <i class="fas fa-ticket-alt"></i> Promo Codes
    </button>
    <button class="admin-tab {{ request('type') == 'combo_deal' ? 'active' : '' }}" onclick="filterByType('combo_deal')">
        <i class="fas fa-layer-group"></i> Combo Deals
    </button>
    <button class="admin-tab {{ request('type') == 'item_discount' ? 'active' : '' }}" onclick="filterByType('item_discount')">
        <i class="fas fa-percent"></i> Item Discounts
    </button>
    <button class="admin-tab {{ request('type') == 'buy_x_free_y' ? 'active' : '' }}" onclick="filterByType('buy_x_free_y')">
        <i class="fas fa-gift"></i> Buy X Free Y
    </button>
    <button class="admin-tab {{ request('type') == 'bundle' ? 'active' : '' }}" onclick="filterByType('bundle')">
        <i class="fas fa-box-open"></i> Bundles
    </button>
    <button class="admin-tab {{ request('type') == 'seasonal' ? 'active' : '' }}" onclick="filterByType('seasonal')">
        <i class="fas fa-calendar-alt"></i> Seasonal
    </button>
</div>

<!-- Promotions Section -->
<div id="promotions-tab" class="tab-content active">
    <div class="admin-section">
        <div class="section-header">
            <h2 class="section-title">
                @if(request('type'))
                    {{ ucwords(str_replace('_', ' ', request('type'))) }} Promotions
                @else
                    All Promotions
                @endif
            </h2>
        </div>

        <div class="search-filter">
            <a href="{{ route('admin.promotions.create') }}" class="admin-btn btn-primary">
                <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
                Create Promotion
            </a>
        </div>

        @if($promotions->count() > 0)
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Promotion Name</th>
                            <th class="cell-center">Type</th>
                            <th>Promo Code</th>
                            <th class="cell-center">Value/Price</th>
                            <th class="cell-center">Min. Order</th>
                            <th>Valid Period</th>
                            <th class="cell-center">Status</th>
                            <th class="cell-center">Usage</th>
                            <th class="cell-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promo)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: var(--text);">{{ $promo->name }}</div>
                                @if($promo->description)
                                    <div style="font-size: 12px; color: var(--text-3); margin-top: 2px;">{{ Str::limit($promo->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="cell-center">
                                @php
                                    $typeConfig = [
                                        'promo_code' => ['icon' => 'ticket-alt', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'label' => 'Promo Code'],
                                        'combo_deal' => ['icon' => 'layer-group', 'color' => '#8b5cf6', 'bg' => '#ede9fe', 'label' => 'Combo'],
                                        'item_discount' => ['icon' => 'percent', 'color' => '#10b981', 'bg' => '#d1fae5', 'label' => 'Discount'],
                                        'buy_x_free_y' => ['icon' => 'gift', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => 'BOGO'],
                                        'bundle' => ['icon' => 'box-open', 'color' => '#ef4444', 'bg' => '#fee2e2', 'label' => 'Bundle'],
                                        'seasonal' => ['icon' => 'calendar-alt', 'color' => '#ec4899', 'bg' => '#fce7f3', 'label' => 'Seasonal'],
                                    ];
                                    $config = $typeConfig[$promo->promotion_type] ?? ['icon' => 'tag', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'label' => 'Other'];
                                @endphp
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                                    <i class="fas fa-{{ $config['icon'] }}"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($promo->promo_code)
                                    <span class="promo-code-badge hidden"
                                          data-code="{{ $promo->promo_code }}"
                                          onclick="togglePromoCode(this)"
                                          title="Click to reveal">
                                        <span class="code-text">*****</span>
                                        <i class="fas reveal-icon"></i>
                                    </span>
                                @else
                                    <span style="color: var(--text-3); font-style: italic;">—</span>
                                @endif
                            </td>
                            <td class="cell-center">
                                @php
                                    // Intelligent display based on promotion type
                                    $displayValue = '';
                                    $badgeClass = '';

                                    switch($promo->promotion_type) {
                                        case 'combo_deal':
                                            $comboPrice = $promo->getComboPrice();
                                            if ($comboPrice) {
                                                $displayValue = 'RM ' . number_format($comboPrice, 2);
                                                $badgeClass = 'fixed';
                                            } else {
                                                $displayValue = 'Combo Deal';
                                                $badgeClass = 'percentage';
                                            }
                                            break;

                                        case 'bundle':
                                            $bundlePrice = $promo->getBundlePrice();
                                            if ($bundlePrice) {
                                                $displayValue = 'RM ' . number_format($bundlePrice, 2);
                                                $badgeClass = 'fixed';
                                            } else {
                                                $displayValue = 'Bundle Deal';
                                                $badgeClass = 'percentage';
                                            }
                                            break;

                                        case 'buy_x_free_y':
                                            $config = $promo->getBuyXGetYConfig();
                                            if ($config && isset($config['buy_quantity']) && isset($config['get_quantity'])) {
                                                $displayValue = 'Buy ' . $config['buy_quantity'] . ' Get ' . $config['get_quantity'];
                                                $badgeClass = 'percentage';
                                            } else {
                                                $displayValue = 'BOGO';
                                                $badgeClass = 'percentage';
                                            }
                                            break;

                                        default:
                                            // For promo_code, item_discount, seasonal - show discount
                                            if ($promo->discount_type === 'percentage') {
                                                $displayValue = number_format($promo->discount_value, 0) . '%';
                                                $badgeClass = 'percentage';
                                            } else {
                                                $displayValue = 'RM ' . number_format($promo->discount_value, 2);
                                                $badgeClass = 'fixed';
                                            }
                                            break;
                                    }
                                @endphp

                                @if($displayValue)
                                    <span class="discount-badge {{ $badgeClass }}">
                                        {{ $displayValue }}
                                    </span>
                                @else
                                    <span style="color: var(--text-3); font-style: italic;">—</span>
                                @endif
                            </td>
                            <td class="cell-center">
                                @if($promo->minimum_order_value)
                                    RM {{ number_format($promo->minimum_order_value, 2) }}
                                @else
                                    <span style="color: var(--text-3); font-style: italic;">No min</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 13px;">{{ $promo->start_date->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: var(--text-3);">to {{ $promo->end_date->format('M d, Y') }}</div>
                            </td>
                            <td class="cell-center">
                                @php
                                    $now = now();
                                    $isExpired = $promo->end_date < $now;
                                    $isUpcoming = $promo->start_date > $now;
                                    $isLive = $promo->start_date <= $now && $promo->end_date >= $now && $promo->is_active;
                                @endphp

                                @if($isLive)
                                    <span class="status status-active">
                                        <i class="fas fa-check-circle"></i> Live
                                    </span>
                                @elseif($isExpired)
                                    <span class="status" style="background: #fee2e2; color: #dc2626;">
                                        <i class="fas fa-calendar-times"></i> Expired
                                    </span>
                                @elseif($isUpcoming)
                                    <span class="status" style="background: #dbeafe; color: #2563eb;">
                                        <i class="fas fa-clock"></i> Upcoming
                                    </span>
                                @elseif(!$promo->is_active)
                                    <span class="status status-inactive">
                                        <i class="fas fa-times-circle"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="cell-center">
                                @php
                                    $currentUses = $promo->current_usage_count ?? 0;
                                    $totalLimit = $promo->total_usage_limit;
                                    $perUserLimit = $promo->usage_limit_per_customer;

                                    $percentageUsed = 0;
                                    if ($totalLimit && $totalLimit > 0) {
                                        $percentageUsed = ($currentUses / $totalLimit) * 100;
                                    }

                                    // Determine color based on usage
                                    $usageColor = '#10b981'; // Green (< 50%)
                                    if ($percentageUsed >= 80) {
                                        $usageColor = '#ef4444'; // Red (>= 80%)
                                    } elseif ($percentageUsed >= 50) {
                                        $usageColor = '#f59e0b'; // Yellow (50-80%)
                                    }
                                @endphp

                                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                    <div style="font-weight: 600; color: {{ $usageColor }}; font-size: 0.95rem;">
                                        {{ number_format($currentUses) }} /
                                        @if($totalLimit)
                                            {{ number_format($totalLimit) }}
                                        @else
                                            <span style="font-size: 1.2rem;">∞</span>
                                        @endif
                                    </div>

                                    @if($totalLimit)
                                        {{-- Progress Bar --}}
                                        <div style="width: 80px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                                            <div style="width: {{ min(100, $percentageUsed) }}%; height: 100%; background: {{ $usageColor }}; transition: width 0.3s;"></div>
                                        </div>

                                        {{-- Warning Badge if almost depleted --}}
                                        @if($percentageUsed >= 90)
                                            <span style="font-size: 0.7rem; color: #dc2626; font-weight: 600;">
                                                <i class="fas fa-exclamation-triangle"></i> Almost depleted
                                            </span>
                                        @endif
                                    @else
                                        <span style="font-size: 0.75rem; color: #6b7280;">Unlimited</span>
                                    @endif

                                    @if($perUserLimit)
                                        <span style="font-size: 0.7rem; color: #6b7280;" title="Maximum per customer">
                                            <i class="fas fa-user"></i> Max: {{ $perUserLimit }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="cell-center">
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; width: 100%; max-width: 180px;">
                                    <!-- Top Row -->
                                    <a href="{{ route('admin.promotions.show', $promo->id) }}"
                                       class="action-btn view-btn"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.promotions.analytics', $promo->id) }}"
                                       class="action-btn"
                                       style="background: #e0e7ff; color: #4f46e5;"
                                       title="View Analytics">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <button class="toggle-btn {{ $promo->is_active ? 'active' : 'inactive' }}"
                                            onclick="toggleStatus({{ $promo->id }}, 'promotion')"
                                            title="{{ $promo->is_active ? 'Active - Click to Deactivate' : 'Inactive - Click to Activate' }}">
                                        <i class="fas fa-toggle-{{ $promo->is_active ? 'on' : 'off' }}"></i>
                                    </button>

                                    <!-- Second Row -->
                                    <a href="{{ route('admin.promotions.edit', $promo->id) }}"
                                       class="action-btn edit-btn"
                                       title="Edit Promotion">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.promotions.destroy', $promo->id) }}"
                                          method="POST"
                                          style="display: contents;"
                                          onsubmit="return confirm('Are you sure you want to delete this promotion?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="Delete Promotion">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.promotions.duplicate', $promo->id) }}"
                                       class="action-btn"
                                       style="background: #f3e8ff; color: #7c3aed;"
                                       title="Duplicate Promotion">
                                        <i class="fas fa-copy"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $promotions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-tags"></i></div>
                <div class="empty-state-title">No Promotions Yet</div>
                <div class="empty-state-text">Start creating promotions to attract more customers!</div>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background-color: #10b981;' : 'background-color: #ef4444;'}
    `;

    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Toggle promo code visibility
function togglePromoCode(element) {
    const codeText = element.querySelector('.code-text');
    const fullCode = element.dataset.code;
    const isHidden = element.classList.contains('hidden');

    if (isHidden) {
        // Reveal the full code
        codeText.textContent = fullCode;
    } else {
        // Hide the code and show placeholder
        codeText.textContent = '*****';
    }
    element.classList.toggle('hidden');
}

// Filter promotions by type
function filterByType(type) {
    const url = new URL(window.location.href);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location.href = url.toString();
}

// Toggle status
function toggleStatus(id, type) {
    const url = `/admin/promotions/${id}/toggle-status`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Show notifications from session
// document.addEventListener('DOMContentLoaded', function() {
//     @if(session('success'))
//         showNotification('{{ session('success') }}', 'success');
//     @endif

//     @if(session('error'))
//         showNotification('{{ session('error') }}', 'error');
//     @endif

//     @if(session('message'))
//         showNotification('{{ session('message') }}', 'success');
//     @endif
// });
</script>
@endsection
