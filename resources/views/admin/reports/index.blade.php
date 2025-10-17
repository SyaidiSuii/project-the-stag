@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('styles')
<style>
    .analytics-container {
        background: #f8f9fa;
        padding: 24px;
        min-height: 100vh;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }

    .stat-card.card-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.card-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-card.card-4 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .stat-card.card-5 {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .stat-card.card-6 {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }

    .stat-card.card-7 {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .stat-card.card-8 {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 8px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-change {
        font-size: 13px;
        opacity: 0.85;
    }

    .report-widget {
        background: #fff;
        padding: 28px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        height: 400px;
        transition: box-shadow 0.3s ease;
    }

    .report-widget:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .report-widget h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        font-weight: 600;
        color: #2d3748;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chart-icon {
        width: 24px;
        height: 24px;
        opacity: 0.6;
    }

    canvas {
        max-height: 320px;
    }

    .section-header {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
    }

    .section-subtitle {
        color: #718096;
        font-size: 14px;
        margin-top: 8px;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 24px;
    }

    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 24px;
        }
        
        .report-widget {
            height: auto;
            min-height: 350px;
        }
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }

    .spinner {
        border: 3px solid #f3f4f6;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Real-time Update Styles */
    .flash-update {
        animation: flashAnimation 1s ease-in-out;
    }

    @keyframes flashAnimation {
        0%, 100% { background-color: transparent; }
        50% { background-color: rgba(102, 126, 234, 0.2); }
    }

    .connection-status {
        position: fixed;
        top: 80px;
        right: 20px;
        background: white;
        padding: 10px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        z-index: 1000;
    }

    .refresh-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }

    .refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }

    .refresh-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .toast {
        min-width: 300px;
        margin-bottom: 10px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        overflow: hidden;
        animation: slideIn 0.3s ease;
    }

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

    .toast-header {
        padding: 12px 16px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .toast-body {
        padding: 12px 16px;
        font-size: 14px;
        color: #4a5568;
    }

    .toast-success { border-left: 4px solid #43e97b; }
    .toast-info { border-left: 4px solid #4facfe; }
    .toast-warning { border-left: 4px solid #fee140; }
    .toast-error { border-left: 4px solid #f5576c; }

    .btn-close {
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.5;
    }

    .btn-close:hover {
        opacity: 1;
    }
</style>
@endsection

@section('content')
<!-- Connection Status Indicator -->
<div class="connection-status" id="connection-status">
    <i class="fas fa-circle" style="color: orange;"></i>
    <span style="color: orange;">Initializing...</span>
</div>

<div class="analytics-container">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="section-title">Analytics Dashboard</h2>
            <p class="section-subtitle">Track your restaurant's performance and insights</p>
        </div>
        <button id="refresh-analytics-btn" class="refresh-btn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    <!-- Statistics Cards - Core Metrics -->
    <div class="stats-grid">
        <div class="stat-card" id="revenue-card">
            <div class="stat-label">💰 Total Revenue</div>
            <div class="stat-value" id="current-month-revenue">RM {{ number_format($currentMonthRevenue, 2) }}</div>
            <div class="stat-change">
                @if($revenueChangePercentage >= 0)
                    <span style="color: #34D399;">↑</span>
                @else
                    <span style="color: #F87171;">↓</span>
                @endif
                {{ abs($revenueChangePercentage) }}% from last month
            </div>
        </div>
        <div class="stat-card card-2" id="orders-card">
            <div class="stat-label">📦 Total Orders</div>
            <div class="stat-value" id="current-month-orders">{{ number_format($currentMonthOrders) }}</div>
            <div class="stat-change">
                @if($ordersChangePercentage >= 0)
                    <span style="color: #34D399;">↑</span>
                @else
                    <span style="color: #F87171;">↓</span>
                @endif
                {{ abs($ordersChangePercentage) }}% from last month
            </div>
        </div>
        <div class="stat-card card-3" id="avg-order-card">
            <div class="stat-label">📊 Avg Order Value</div>
            <div class="stat-value" id="avg-order-value">RM {{ number_format($currentMonthAvgOrderValue, 2) }}</div>
            <div class="stat-change">
                @if($avgOrderValueChangePercentage >= 0)
                    <span style="color: #34D399;">↑</span>
                @else
                    <span style="color: #F87171;">↓</span>
                @endif
                {{ abs($avgOrderValueChangePercentage) }}% from last month
            </div>
        </div>
        <div class="stat-card card-4">
            <div class="stat-label">🍽️ Active Items</div>
            <div class="stat-value">{{ $activeItems }}</div>
            <div class="stat-change">{{ $newItemsThisMonth }} new items added this month</div>
        </div>
    </div>

    <!-- New Statistics Cards - Additional Metrics -->
    <div class="stats-grid" style="margin-top: 20px;">
        <div class="stat-card card-5" id="qr-card">
            <div class="stat-label">📱 QR Orders</div>
            <div class="stat-value" id="qr-orders-count">{{ number_format($qrOrders ?? 0) }}</div>
            <div class="stat-change">RM <span id="qr-revenue">{{ number_format($qrRevenue ?? 0, 2) }}</span> revenue from QR</div>
        </div>
        <div class="stat-card card-6" id="bookings-card">
            <div class="stat-label">📅 Table Bookings</div>
            <div class="stat-value" id="table-bookings-count">{{ number_format($tableBookings ?? 0) }}</div>
            <div class="stat-change">Reservations this month</div>
        </div>
        <div class="stat-card card-7" id="promotions-card">
            <div class="stat-label">🎁 Promotions Used</div>
            <div class="stat-value" id="promotions-used-count">{{ number_format($promotionsUsed ?? 0) }}</div>
            <div class="stat-change">RM <span id="total-discounts">{{ number_format($promotionDiscounts ?? 0, 2) }}</span> total discounts</div>
        </div>
        <div class="stat-card card-8" id="rewards-card">
            <div class="stat-label">⭐ Rewards Redeemed</div>
            <div class="stat-value" id="rewards-redeemed-count">{{ number_format($rewardsRedeemed ?? 0) }}</div>
            <div class="stat-change">Loyalty program redemptions</div>
        </div>
    </div>

    <!-- Customer Metrics Section -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 24px; border-radius: 12px; margin: 30px 0; color: white;">
        <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">👥 Customer Insights (Last 30 Days)</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 4px;">New Customers</div>
                <div style="font-size: 28px; font-weight: 700;" id="new-customers">{{ number_format($customerRetention['new_customers'] ?? 0) }}</div>
            </div>
            <div>
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 4px;">Returning Customers</div>
                <div style="font-size: 28px; font-weight: 700;" id="returning-customers">{{ number_format($customerRetention['returning_customers'] ?? 0) }}</div>
            </div>
            <div>
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 4px;">Retention Rate</div>
                <div style="font-size: 28px; font-weight: 700;" id="retention-rate">{{ number_format($customerRetention['retention_rate'] ?? 0, 1) }}%</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="report-widget">
            <h3>
                Sales Overview (Last 30 Days)
                <svg class="chart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </h3>
            <canvas id="salesChart"></canvas>
        </div>
        
        <div class="report-widget">
            <h3>
                Top 10 Selling Products
                <svg class="chart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </h3>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>

    <div class="charts-grid" style="margin-top: 24px;">
        <div class="report-widget">
            <h3>
                Sales by Category
                <svg class="chart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
            </h3>
            <canvas id="salesByCategoryChart"></canvas>
        </div>

        <div class="report-widget">
            <h3>
                Order Types Distribution
                <svg class="chart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </h3>
            <canvas id="orderTypesChart"></canvas>
        </div>
    </div>

    <!-- New Charts Section -->
    <div class="charts-grid" style="margin-top: 24px;">
        <div class="report-widget">
            <h3>
                QR vs Web Orders (Last 30 Days)
                <svg class="chart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </h3>
            <canvas id="qrVsWebChart"></canvas>
        </div>

        <div class="report-widget">
            <h3>
                Promotion Effectiveness
                <svg class="chart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                </svg>
            </h3>
            <div style="padding: 20px 0;">
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; color: #718096; margin-bottom: 4px;">Total Promotions Used</div>
                    <div style="font-size: 28px; font-weight: 700; color: #2d3748;">{{ number_format($promotionStats['total_usage'] ?? 0) }}</div>
                </div>
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; color: #718096; margin-bottom: 4px;">Total Discounts Given</div>
                    <div style="font-size: 28px; font-weight: 700; color: #667eea;">RM {{ number_format($promotionStats['total_discounts'] ?? 0, 2) }}</div>
                </div>
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; color: #718096; margin-bottom: 4px;">Revenue Impact</div>
                    <div style="font-size: 28px; font-weight: 700; color: #f5576c;">{{ number_format($promotionStats['revenue_impact_percentage'] ?? 0, 2) }}%</div>
                </div>
                <div>
                    <div style="font-size: 13px; color: #718096; margin-bottom: 4px;">Avg Discount Per Use</div>
                    <div style="font-size: 28px; font-weight: 700; color: #43e97b;">RM {{ number_format($promotionStats['avg_discount_per_use'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const salesSummary = @json($salesSummary);
        const topSellingProducts = @json($topSellingProducts);
        const salesByCategory = @json($salesByCategory);
        const orderTypeBreakdown = @json($orderTypeBreakdown);
        const qrVsWeb = @json($qrVsWeb);

        // Global chart defaults
        Chart.defaults.font.family = "'Inter', 'system-ui', sans-serif";
        Chart.defaults.color = '#4a5568';

        // Sales Overview Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesSummary.labels,
                datasets: [{
                    label: 'Revenue (RM)',
                    data: salesSummary.revenue,
                    fill: true,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: 'rgb(102, 126, 234)',
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'RM ' + value.toLocaleString();
                            },
                            padding: 10
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                }
            }
        });

        // Top Selling Products Chart
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topSellingProducts.map(p => p.menu_item.name),
                datasets: [{
                    label: 'Quantity Sold',
                    data: topSellingProducts.map(p => p.total_quantity),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                }
            }
        });

        // Sales by Category Chart
        const salesByCategoryCtx = document.getElementById('salesByCategoryChart').getContext('2d');
        new Chart(salesByCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: salesByCategory.labels,
                datasets: [{
                    label: 'Revenue',
                    data: salesByCategory.revenue,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.9)',
                        'rgba(237, 100, 166, 0.9)',
                        'rgba(255, 159, 64, 0.9)',
                        'rgba(75, 192, 192, 0.9)',
                        'rgba(153, 102, 255, 0.9)',
                        'rgba(255, 205, 86, 0.9)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'RM ' + context.parsed.toLocaleString();
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Order Types Distribution Chart (with REAL data)
        const orderTypesCtx = document.getElementById('orderTypesChart').getContext('2d');
        new Chart(orderTypesCtx, {
            type: 'doughnut',
            data: {
                labels: orderTypeBreakdown.labels,
                datasets: [{
                    label: 'Orders',
                    data: orderTypeBreakdown.data,
                    backgroundColor: [
                        'rgba(67, 233, 123, 0.9)',
                        'rgba(79, 172, 254, 0.9)',
                        'rgba(245, 87, 108, 0.9)',
                        'rgba(250, 112, 154, 0.9)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const revenue = orderTypeBreakdown.revenue[context.dataIndex];
                                return [
                                    label + ': ' + value + ' orders',
                                    'Revenue: RM ' + revenue.toLocaleString()
                                ];
                            }
                        }
                    }
                }
            }
        });

        // QR vs Web Orders Chart
        const qrVsWebCtx = document.getElementById('qrVsWebChart').getContext('2d');
        new Chart(qrVsWebCtx, {
            type: 'pie',
            data: {
                labels: qrVsWeb.labels,
                datasets: [{
                    label: 'Orders',
                    data: qrVsWeb.data,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.9)',
                        'rgba(250, 112, 154, 0.9)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = context.dataIndex === 0 ? qrVsWeb.percentage.web : qrVsWeb.percentage.qr;
                                return [
                                    label + ': ' + value + ' orders',
                                    'Percentage: ' + percentage + '%'
                                ];
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<!-- Real-time Analytics JavaScript -->
<script src="{{ asset('js/admin/realtime-analytics.js') }}"></script>

<!-- Laravel Echo for WebSockets (Reverb) -->
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.0.1/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
<script>
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env('REVERB_APP_KEY') }}',
        wsHost: '{{ env('REVERB_HOST') }}',
        wsPort: {{ env('REVERB_PORT', 8080) }},
        wssPort: {{ env('REVERB_PORT', 8080) }},
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        cluster: 'mt1',
        disableStats: true
    });
</script>
@endsection