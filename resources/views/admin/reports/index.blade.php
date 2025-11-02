@extends('layouts.admin')

@section('title', 'Modern Analytics Dashboard')
@section('page-title', 'Modern Analytics Dashboard')

@section('styles')
<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #8b5cf6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --light: #f8fafc;
        --dark: #1e293b;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
        --border-radius: 16px;
        --transition: all 0.3s ease;
    }

    .analytics-container {
        background: var(--gray-100);
        padding: 24px;
        min-height: 100vh;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title-wrapper {
        flex: 1;
    }

    .page-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--gray-900);
        margin: 0 0 8px 0;
        line-height: 1.2;
    }

    .page-subtitle {
        color: var(--gray-500);
        font-size: 15px;
        margin: 0;
        font-weight: 400;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .pdf-btn {
        background: linear-gradient(135deg, var(--danger), #dc2626);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        height: fit-content;
        text-decoration: none;
    }

    .pdf-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
    }
    
    .pdf-btn:nth-child(2):hover {
        box-shadow: 0 4px 8px rgba(31, 41, 55, 0.4);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }

    .large-widget, .medium-widget, .small-widget, .full-width-widget {
        background: white;
        border-radius: var(--border-radius);
        padding: 28px;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        border: 1px solid var(--gray-200);
        display: flex;
        flex-direction: column;
    }
    .large-widget:hover, .medium-widget:hover, .small-widget:hover, .full-width-widget:hover, .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow-hover);
    }

    .large-widget {
        grid-column: span 8;
        height: 450px;
    }

    .medium-widget {
        grid-column: span 4;
        height: 450px;
    }

    .small-widget {
        grid-column: span 3;
        padding: 24px;
        justify-content: center;
    }

    .full-width-widget {
        grid-column: span 12;
        height: 450px;
    }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--gray-200);
    }

    .widget-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .widget-content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chart-container {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }

    .stat-label {
        font-size: 14px;
        color: var(--gray-500);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--gray-900);
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .customer-insights {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: var(--border-radius);
        padding: 25px;
        margin: 15px 0;
        color: white;
        box-shadow: var(--card-shadow);
        grid-column: span 12;
    }

    .customer-insights-header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .customer-insights-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: white;
    }

    .customer-insights-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin: 0;
        font-weight: 400;
    }

    .customer-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
    }

    .customer-stat {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        backdrop-filter: blur(10px);
    }

    .customer-stat-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .customer-stat-value {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .customer-stat-desc {
        font-size: 12px;
        opacity: 0.8;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .kpi-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
    }
    
    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 4px;
    }
    
    .kpi-label {
        font-size: 14px;
        color: var(--gray-500);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
    }

    .change-positive {
        color: var(--success);
    }

    .change-negative {
        color: var(--danger);
    }
    
    @media (max-width: 1400px) {
        .large-widget {
            grid-column: span 12;
        }
        .medium-widget {
            grid-column: span 6;
        }
        .small-widget {
            grid-column: span 3;
        }
    }

    @media (max-width: 992px) {
        .kpi-grid, .dashboard-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .large-widget, .medium-widget, .full-width-widget {
            grid-column: span 2;
        }
        .small-widget {
            grid-column: span 1;
        }
    }

    @media (max-width: 768px) {
        .analytics-container {
            padding: 16px;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .kpi-grid, .dashboard-grid {
            grid-template-columns: 1fr;
        }
        .large-widget, .medium-widget, .small-widget, .full-width-widget {
            grid-column: span 1 !important;
        }
        .customer-stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    /* ApexCharts Custom Styles */
    .apexcharts-tooltip {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
        border: 1px solid var(--gray-200) !important;
    }
    
    .apexcharts-menu {
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
    
    .apexcharts-xaxistooltip {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05) !important;
        border-radius: 6px !important;
    }
</style>
@endsection

@section('content')
<div class="analytics-container">
    <div class="page-header">
        <div class="page-title-wrapper">
            <h2 class="page-title">Modern Analytics Dashboard</h2>
            <p class="page-subtitle">Advanced data visualization with ApexCharts</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.reports.generate-pdf') }}" class="pdf-btn">
                <i class="fas fa-file-pdf"></i> View PDF Report
            </a>
            <a href="{{ route('admin.reports.download-pdf') }}" class="pdf-btn" style="background: linear-gradient(135deg, #4b5563, #1f2937);">
                <i class="fas fa-download"></i> Download PDF Report
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-value">RM {{ number_format($currentMonthRevenue, 2) }}</div>
            <div class="stat-change {{ $revenueChangePercentage >= 0 ? 'change-positive' : 'change-negative' }}">
                @if($revenueChangePercentage >= 0)
                    <i class="fas fa-arrow-up"></i>
                @else
                    <i class="fas fa-arrow-down"></i>
                @endif
                {{ abs($revenueChangePercentage) }}% from last month
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Total Orders</div>
            <div class="kpi-value">{{ number_format($currentMonthOrders) }}</div>
            <div class="stat-change {{ $ordersChangePercentage >= 0 ? 'change-positive' : 'change-negative' }}">
                @if($ordersChangePercentage >= 0)
                    <i class="fas fa-arrow-up"></i>
                @else
                    <i class="fas fa-arrow-down"></i>
                @endif
                {{ abs($ordersChangePercentage) }}% from last month
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Avg Order Value</div>
            <div class="kpi-value">RM {{ number_format($currentMonthAvgOrderValue, 2) }}</div>
            <div class="stat-change {{ $avgOrderValueChangePercentage >= 0 ? 'change-positive' : 'change-negative' }}">
                @if($avgOrderValueChangePercentage >= 0)
                    <i class="fas fa-arrow-up"></i>
                @else
                    <i class="fas fa-arrow-down"></i>
                @endif
                {{ abs($avgOrderValueChangePercentage) }}% from last month
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Active Menu Items</div>
            <div class="kpi-value">{{ $activeItems }}</div>
            <div class="stat-change text-info">
                <i class="fas fa-plus-circle"></i>
                {{ $newItemsThisMonth }} new items
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Sales Overview Chart -->
        <div class="large-widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="fas fa-chart-line"></i>
                    Sales Overview (Last 30 Days)
                </h3>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <div id="salesChart"></div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="medium-widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="fas fa-chart-bar"></i>
                    Top 10 Selling Products
                </h3>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <div id="topProductsChart"></div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="small-widget">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Revenue Trend</div>
                    <div class="stat-value">RM {{ number_format($currentMonthRevenue, 2) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="small-widget">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Orders Trend</div>
                    <div class="stat-value">{{ number_format($currentMonthOrders) }}</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger);">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>

        <div class="small-widget">
            <div class="stat-header">
                <div>
                    <div class="stat-label">QR Orders</div>
                    <div class="stat-value">{{ number_format($qrOrders ?? 0) }}</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
        </div>

        <div class="small-widget">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Bookings</div>
                    <div class="stat-value">{{ number_format($tableBookings ?? 0) }}</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="medium-widget" style="grid-column: span 6;">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="fas fa-chart-pie"></i>
                    Sales by Category
                </h3>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <div id="salesByCategoryChart"></div>
                </div>
            </div>
        </div>

        <!-- Order Types Distribution -->
        <div class="medium-widget" style="grid-column: span 6;">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="fas fa-chart-pie"></i>
                    Order Types Distribution
                </h3>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <div id="orderTypesChart"></div>
                </div>
            </div>
        </div>

        <!-- QR vs Web Orders -->
        <div class="full-width-widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="fas fa-qrcode"></i>
                    QR vs Web Orders (Last 30 Days)
                </h3>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <div id="qrVsWebChart"></div>
                </div>
            </div>
        </div>

        <!-- Customer Insights Section -->
        <div class="customer-insights">
            <div class="customer-insights-header">
                <div>
                    <h3 class="customer-insights-title">👥 Customer Insights (Last 30 Days)</h3>
                    <p class="customer-insights-subtitle">Understand your customer behavior and retention</p>
                </div>
            </div>
            <div class="customer-stats-grid">
                <div class="customer-stat">
                    <div class="customer-stat-label">New Customers</div>
                    <div class="customer-stat-value" id="new-customers">{{ number_format($customerRetention['new_customers'] ?? 0) }}</div>
                    <div class="customer-stat-desc">First-time visitors</div>
                </div>
                <div class="customer-stat">
                    <div class="customer-stat-label">Returning Customers</div>
                    <div class="customer-stat-value" id="returning-customers">{{ number_format($customerRetention['returning_customers'] ?? 0) }}</div>
                    <div class="customer-stat-desc">Repeat visitors</div>
                </div>
                <div class="customer-stat">
                    <div class="customer-stat-label">Retention Rate</div>
                    <div class="customer-stat-value" id="retention-rate">{{ number_format($customerRetention['retention_rate'] ?? 0, 1) }}%</div>
                    <div class="customer-stat-desc">Customer loyalty</div>
                </div>
                <div class="customer-stat">
                    <div class="customer-stat-label">Avg Visit Frequency</div>
                    <div class="customer-stat-value">{{ number_format(2.3, 1) }}</div>
                    <div class="customer-stat-desc">Times per month</div>
                </div>
            </div>
        </div>

        <!-- Promotion Effectiveness -->
        <div class="full-width-widget">
            <div class="widget-header">
                <h3 class="widget-title">
                    <i class="fas fa-percentage"></i>
                    Promotion Effectiveness
                </h3>
            </div>
            <div class="widget-content">
                <div class="chart-container">
                    <div id="promotionEffectivenessChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Real data from Laravel controller
        const salesSummary = @json($salesSummary);
        const topSellingProducts = @json($topSellingProducts);
        const salesByCategory = @json($salesByCategory);
        const orderTypeBreakdown = @json($orderTypeBreakdown);
        const qrVsWeb = @json($qrVsWeb);

        // Sales Overview Chart (Area Chart)
        var salesOptions = {
            chart: {
                type: 'area',
                height: '100%',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                sparkline: {
                    enabled: false
                }
            },
            series: [{
                name: 'Revenue (RM)',
                data: salesSummary.revenue
            }],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 100]
                }
            },
            xaxis: {
                categories: salesSummary.labels,
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return 'RM ' + value.toLocaleString();
                    },
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return 'RM ' + value.toLocaleString();
                    }
                },
                theme: 'light',
                style: {
                    fontSize: '12px'
                }
            },
            colors: ['#6366f1'],
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
                padding: {
                    right: 10
                }
            }
        };

        var salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
        salesChart.render();

        // Top Selling Products Chart (Column Chart)
        var topProductsOptions = {
            chart: {
                type: 'bar',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Quantity Sold',
                data: topSellingProducts.map(p => p.total_quantity)
            }],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: topSellingProducts.map(p => p.menu_item.name),
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px'
                }
            },
            colors: ['#8b5cf6'],
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            }
        };

        var topProductsChart = new ApexCharts(document.querySelector("#topProductsChart"), topProductsOptions);
        topProductsChart.render();

        // Sales by Category Chart (Donut Chart)
        var salesByCategoryOptions = {
            chart: {
                type: 'donut',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: salesByCategory.revenue,
            labels: salesByCategory.labels,
            colors: ['#6366f1', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
            legend: {
                position: 'bottom',
                fontSize: '12px',
                markers: {
                    width: 12,
                    height: 12
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return 'RM ' + value.toLocaleString();
                    }
                },
                theme: 'light',
                style: {
                    fontSize: '12px'
                }
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            }
        };

        var salesByCategoryChart = new ApexCharts(document.querySelector("#salesByCategoryChart"), salesByCategoryOptions);
        salesByCategoryChart.render();

        // Order Types Distribution Chart (Pie Chart)
        var orderTypesOptions = {
            chart: {
                type: 'pie',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: orderTypeBreakdown.data,
            labels: orderTypeBreakdown.labels,
            colors: ['#10b981', '#3b82f6', '#ef4444', '#f59e0b'],
            legend: {
                position: 'bottom',
                fontSize: '12px',
                markers: {
                    width: 12,
                    height: 12
                }
            },
            tooltip: {
                y: {
                    formatter: function (value, { seriesIndex }) {
                        const revenue = orderTypeBreakdown.revenue[seriesIndex];
                        return `${value} orders (RM ${revenue.toLocaleString()})`;
                    }
                },
                theme: 'light',
                style: {
                    fontSize: '12px'
                }
            },
            dataLabels: {
                enabled: false
            }
        };

        var orderTypesChart = new ApexCharts(document.querySelector("#orderTypesChart"), orderTypesOptions);
        orderTypesChart.render();

        // QR vs Web Orders Chart (Column Chart)
        var qrVsWebOptions = {
            chart: {
                type: 'bar',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Orders',
                data: qrVsWeb.data
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 6,
                    columnWidth: '50%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: qrVsWeb.labels,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value, { dataPointIndex }) {
                        const percentage = dataPointIndex === 0 ? qrVsWeb.percentage.web : qrVsWeb.percentage.qr;
                        return `${value} orders (${percentage}%)`;
                    }
                },
                theme: 'light',
                style: {
                    fontSize: '12px'
                }
            },
            colors: ['#8b5cf6', '#3b82f6'],
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            }
        };

        var qrVsWebChart = new ApexCharts(document.querySelector("#qrVsWebChart"), qrVsWebOptions);
        qrVsWebChart.render();

        // Promotion Effectiveness Chart (Bar Chart)
        var promotionOptions = {
            chart: {
                type: 'bar',
                height: '100%',
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Metrics',
                data: [
                    {{ $promotionsUsed ?? 0 }},
                    {{ $promotionDiscounts ?? 0 }},
                    {{ $promotionStats['revenue_impact_percentage'] ?? 0 }},
                    {{ $promotionStats['avg_discount_per_use'] ?? 0 }}
                ]
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 6,
                    columnWidth: '50%',
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, { seriesIndex }) {
                    if (seriesIndex === 2) {
                        return val + '%';
                    } else if (seriesIndex === 1 || seriesIndex === 3) {
                        return 'RM ' + val.toLocaleString();
                    } else {
                        return val.toLocaleString();
                    }
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Promotions Used', 'Total Discounts', 'Revenue Impact', 'Avg Discount/Use'],
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value, { seriesIndex }) {
                        if (seriesIndex === 2) {
                            return value + '%';
                        } else if (seriesIndex === 1 || seriesIndex === 3) {
                            return 'RM ' + value.toLocaleString();
                        } else {
                            return value.toLocaleString();
                        }
                    },
                    style: {
                        colors: '#64748b',
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value, { seriesIndex }) {
                        if (seriesIndex === 2) {
                            return value + '%';
                        } else if (seriesIndex === 1 || seriesIndex === 3) {
                            return 'RM ' + value.toLocaleString();
                        } else {
                            return value.toLocaleString();
                        }
                    }
                },
                theme: 'light',
                style: {
                    fontSize: '12px'
                }
            },
            colors: ['#f59e0b', '#ef4444', '#3b82f6', '#10b981'],
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            }
        };

        var promotionChart = new ApexCharts(document.querySelector("#promotionEffectivenessChart"), promotionOptions);
        promotionChart.render();
    });
</script>
@endsection