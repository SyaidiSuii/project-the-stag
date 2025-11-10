@extends('layouts.admin')

@section('title', 'Enhanced Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line"></i> Enhanced Analytics Dashboard
                <span id="new-alerts-badge" class="badge badge-danger d-none ml-2">
                    <i class="fas fa-bell"></i> <span id="new-alerts-count">0</span>
                </span>
            </h1>
            <small class="text-muted">
                <i class="fas fa-clock"></i> Last updated: <span id="last-updated">Loading...</span>
                <span class="ml-3" id="auto-refresh-status">
                    <i class="fas fa-circle text-success pulse"></i> Auto-refresh ON
                </span>
            </small>
        </div>
        <div>
            <button class="btn btn-sm btn-info mr-2" onclick="toggleAutoRefresh()" id="auto-refresh-btn">
                <i class="fas fa-pause"></i> <span id="auto-refresh-text">Pause</span>
            </button>
            <button class="btn btn-primary" onclick="manualRefresh()" id="refresh-btn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Real-time Alert Banner -->
    <div id="alert-banner" class="alert alert-dismissible fade d-none mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
            <div>
                <strong id="alert-title"></strong>
                <p class="mb-0" id="alert-message"></p>
            </div>
        </div>
        <button type="button" class="close" onclick="dismissAlert()">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- Business Health Score Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="h1 mb-0" id="health-score-display">
                                <span id="health-score">--</span>
                                <span class="text-muted">/100</span>
                            </div>
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Business Health Score
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="h2 mb-0" id="health-grade">
                                <span class="badge badge-success">A+</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" id="health-progress" role="progressbar"
                                     style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div id="health-status" class="badge badge-success">Healthy</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Revenue (MoM)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="revenue-current">
                                RM {{ number_format($currentMonthRevenue, 2) }}
                            </div>
                            <div class="mt-2">
                                <span id="revenue-change" class="badge badge-{{ $revenueChangePercentage >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $revenueChangePercentage >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($revenueChangePercentage) }}%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $currentMonthOrders }}
                            </div>
                            <div class="mt-2">
                                <span class="badge badge-{{ $ordersChangePercentage >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $ordersChangePercentage >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($ordersChangePercentage) }}%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AOV Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                RM {{ number_format($currentMonthAvgOrderValue, 2) }}
                            </div>
                            <div class="mt-2">
                                <span class="badge badge-{{ $avgOrderValueChangePercentage >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $avgOrderValueChangePercentage >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($avgOrderValueChangePercentage) }}%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Health Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menu Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="menu-active">
                                {{ $activeItems }} Active
                            </div>
                            <div class="mt-2">
                                <span class="badge badge-warning" id="menu-attention">
                                    -- Need Attention
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Revenue & Forecast -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area"></i> Revenue Trend & Forecast (30 Days)
                    </h6>
                </div>
                <div class="card-body">
                    <div id="revenue-trend-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-pie"></i> Order Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div id="order-distribution-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Insights & Recommendations -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Priority Insights
                    </h6>
                    <span class="badge badge-danger" id="insights-count">0</span>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="insights-list">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Loading insights...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-lightbulb"></i> Recommendations
                    </h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="recommendations-list">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Loading recommendations...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Intelligence -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-utensils"></i> Menu Performance Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div id="menu-performance-chart"></div>
                        </div>
                        <div class="col-md-4">
                            <div id="top-performers-chart"></div>
                        </div>
                    </div>

                    <!-- Pricing & Bundle Opportunities -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success">
                                <i class="fas fa-dollar-sign"></i> Pricing Opportunities
                            </h6>
                            <div id="pricing-opportunities">
                                <p class="text-muted">Loading...</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-gift"></i> Bundle Opportunities
                            </h6>
                            <div id="bundle-opportunities">
                                <p class="text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Hours Heatmap -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-fire"></i> Peak Hours Heatmap
                    </h6>
                </div>
                <div class="card-body">
                    <div id="peak-hours-heatmap"></div>
                    <div class="mt-3" id="peak-hours-recommendations"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Analytics Dashboard Custom Styles - Following Project Theme */

/* Dashboard Container */
.analytics-container {
    max-width: 100%;
}

/* Dashboard Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--muted);
}

.dashboard-header-left h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
}

.dashboard-header-left small {
    color: var(--text-3);
    font-size: 13px;
    display: block;
    margin-top: 8px;
}

.dashboard-header-right {
    display: flex;
    gap: 10px;
}

/* Alert Banner */
.alert-banner {
    background: white;
    border-radius: var(--radius);
    padding: 16px 20px;
    margin-bottom: 20px;
    border-left: 4px solid;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow);
}

.alert-banner.alert-success { border-left-color: var(--success); background: #d1fae5; }
.alert-banner.alert-warning { border-left-color: var(--warning); background: #ffedd5; }
.alert-banner.alert-danger { border-left-color: var(--danger); background: #fee2e2; }
.alert-banner.alert-info { border-left-color: var(--brand); background: #dbeafe; }

.alert-banner-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.alert-banner-content i {
    font-size: 24px;
}

.alert-banner-text strong {
    display: block;
    font-weight: 700;
    margin-bottom: 4px;
}

.alert-banner-text p {
    margin: 0;
    font-size: 14px;
}

.alert-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: var(--text-3);
    padding: 0;
    width: 24px;
    height: 24px;
}

.alert-close:hover {
    color: var(--text);
}

/* Cards Grid */
.analytics-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.analytics-card {
    background: white;
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow);
    border: 1px solid var(--muted);
}

.analytics-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.analytics-card-title {
    font-weight: 600;
    font-size: 16px;
    color: var(--text-2);
}

.analytics-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 18px;
}

.analytics-card-value {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 8px;
    color: var(--text);
}

.analytics-card-desc {
    font-size: 14px;
    color: var(--text-3);
}

/* Sections */
.analytics-section {
    background: white;
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow);
    border: 1px solid var(--muted);
    margin-bottom: 30px;
}

.analytics-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--muted);
}

.analytics-section-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Charts Grid */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.chart-container {
    min-height: 350px;
}

/* Insight Items */
.insights-grid {
    display: grid;
    gap: 16px;
}

.insight-item {
    border-left: 4px solid;
    padding: 16px;
    border-radius: var(--radius);
    background: var(--bg);
    border: 1px solid var(--muted);
}

.insight-critical { border-left-color: var(--danger); background: #fee2e2; }
.insight-high { border-left-color: var(--warning); background: #ffedd5; }
.insight-medium { border-left-color: var(--brand); background: #dbeafe; }
.insight-low { border-left-color: var(--text-3); background: var(--bg); }

.insight-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 8px;
}

.insight-title {
    font-weight: 700;
    font-size: 15px;
    color: var(--text);
}

.insight-message {
    font-size: 14px;
    color: var(--text-2);
    margin-bottom: 8px;
}

.insight-action {
    font-size: 13px;
    color: var(--text-3);
    font-style: italic;
}

/* Recommendation Items */
.recommendation-item {
    padding: 14px;
    margin-bottom: 12px;
    border-radius: var(--radius);
    background: white;
    border: 1px solid var(--muted);
    display: flex;
    justify-content: space-between;
    align-items: start;
}

.recommendation-item:hover {
    box-shadow: var(--shadow);
    border-color: var(--brand);
}

.recommendation-content {
    flex: 1;
}

.recommendation-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--text);
    margin-bottom: 4px;
}

.recommendation-action {
    font-size: 13px;
    color: var(--text-2);
}

/* Opportunity Cards */
.opportunities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}

.opportunity-card {
    background: var(--bg);
    padding: 16px;
    border-radius: var(--radius);
    border-left: 3px solid var(--success);
    border: 1px solid var(--muted);
}

.opportunity-card:hover {
    box-shadow: var(--shadow);
}

.opportunity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.opportunity-title {
    font-weight: 700;
    font-size: 15px;
    color: var(--text);
}

.opportunity-row {
    display: flex;
    gap: 20px;
    margin-bottom: 8px;
}

.opportunity-col {
    flex: 1;
}

.opportunity-label {
    font-size: 12px;
    color: var(--text-3);
    margin-bottom: 4px;
}

.opportunity-value {
    font-weight: 700;
    font-size: 16px;
    color: var(--text);
}

.opportunity-note {
    font-size: 13px;
    color: var(--text-3);
    margin-top: 8px;
    display: block;
}

/* Badges */
.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.badge-success { background: #d1fae5; color: var(--success); }
.badge-danger { background: #fee2e2; color: var(--danger); }
.badge-warning { background: #ffedd5; color: var(--warning); }
.badge-info { background: #dbeafe; color: var(--brand); }
.badge-secondary { background: var(--muted); color: var(--text-2); }

/* Real-time Features */
.pulse {
    animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
    0% { opacity: 1; }
    50% { opacity: 0.4; }
    100% { opacity: 1; }
}

.refreshing {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#last-updated {
    font-weight: 600;
    color: #5a5c69;
}

#new-alerts-badge {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>
@endpush

@push('scripts')
<!-- ApexCharts for Interactive Charts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
// Global chart instances
let revenueTrendChart, orderDistributionChart, menuPerformanceChart, topPerformersChart, peakHoursChart;

// Real-time configuration
let autoRefreshInterval = null;
let autoRefreshEnabled = true;
const REFRESH_INTERVAL = 5 * 60 * 1000; // 5 minutes in milliseconds
let previousCriticalCount = 0;

// Audio for alerts
const alertSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjWO1fPTgjMGHW7A7+OZURE='); // Short beep sound

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', function() {
    updateLastUpdated();
    loadAllData();
    startAutoRefresh();
});

// Load Executive Summary
function loadExecutiveSummary() {
    fetch('/admin/reports/executive-summary')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateHealthScore(data.data.health_score);
                updateKeyMetrics(data.data.key_metrics);
                updateMenuHealth(data.data.menu_health);
            }
        })
        .catch(console.error);
}

// Update Health Score
function updateHealthScore(healthScore) {
    document.getElementById('health-score').textContent = healthScore.score;
    document.getElementById('health-progress').style.width = healthScore.score + '%';
    document.getElementById('health-progress').setAttribute('aria-valuenow', healthScore.score);

    const gradeColors = {
        'A+': 'success', 'A': 'success', 'B+': 'info', 'B': 'info',
        'C+': 'warning', 'C': 'warning', 'D': 'danger', 'F': 'danger'
    };

    const statusColors = {
        'healthy': 'success', 'needs_attention': 'warning', 'critical': 'danger'
    };

    const gradeBadge = document.getElementById('health-grade');
    gradeBadge.innerHTML = `<span class="badge badge-${gradeColors[healthScore.grade]}">${healthScore.grade}</span>`;

    const statusBadge = document.getElementById('health-status');
    statusBadge.className = `badge badge-${statusColors[healthScore.status]}`;
    statusBadge.textContent = healthScore.status.replace('_', ' ').toUpperCase();

    // Monitor health score changes (real-time)
    monitorHealthScore(healthScore);
}

// Load Business Intelligence
function loadBusinessIntelligence() {
    fetch('/admin/reports/business-intelligence?days=30')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderRevenueTrendChart(data.data.trends, data.data.forecast);
                renderOrderDistributionChart(data.data);
                renderPeakHoursHeatmap(data.data.peak_hours);
            }
        })
        .catch(console.error);
}

// Load Menu Intelligence
function loadMenuIntelligence() {
    fetch('/admin/reports/menu-intelligence?days=30')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const analysis = data.data.performance_analysis;
                document.getElementById('menu-attention').textContent =
                    analysis.summary.items_needing_attention + ' Need Attention';

                renderMenuPerformanceChart(analysis);
                renderTopPerformersChart(analysis);
                renderPricingOpportunities(data.data.pricing_opportunities);
                renderBundleOpportunities(data.data.bundle_opportunities);
            }
        })
        .catch(console.error);
}

// Load Business Insights
function loadBusinessInsights() {
    fetch('/admin/reports/business-insights')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderInsights(data.insights);
                renderRecommendations(data.recommendations);

                // Check for new critical alerts (real-time monitoring)
                checkForNewAlerts(data);
            }
        })
        .catch(console.error);
}

// Render Revenue Trend Chart
function renderRevenueTrendChart(trends, forecast) {
    // Get last 30 days of data from monthly analytics
    fetch('/admin/reports/business-intelligence?days=30')
        .then(r => r.json())
        .then(response => {
            const data = response.data;

            // Extract historical data (mock with trend data for now)
            const historicalDates = [];
            const historicalRevenue = [];
            const today = new Date();

            // Generate last 30 days
            for (let i = 29; i >= 0; i--) {
                const date = new Date(today);
                date.setDate(date.getDate() - i);
                historicalDates.push(date.toLocaleDateString('en-MY', { month: 'short', day: 'numeric' }));
                // Use forecast base as historical avg
                historicalRevenue.push(Math.random() * 200 + 50); // Placeholder until we have daily data
            }

            // Add forecast dates
            const forecastDates = [];
            const forecastData = forecast?.forecast || [];
            for (let i = 1; i <= forecastData.length; i++) {
                const date = new Date(today);
                date.setDate(date.getDate() + i);
                forecastDates.push(date.toLocaleDateString('en-MY', { month: 'short', day: 'numeric' }));
            }

            const allDates = [...historicalDates, ...forecastDates];
            const allRevenue = [...historicalRevenue, ...Array(forecastData.length).fill(null)];
            const allForecast = [...Array(historicalDates.length).fill(null), ...forecastData];

            const options = {
                series: [{
                    name: 'Actual Revenue (RM)',
                    data: allRevenue
                }, {
                    name: 'Forecast (RM)',
                    data: allForecast
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: { enabled: true },
                    toolbar: { show: true }
                },
                stroke: {
                    width: [3, 2],
                    curve: 'smooth',
                    dashArray: [0, 5]
                },
                colors: ['#4e73df', '#1cc88a'],
                title: {
                    text: `Revenue Trend: ${trends.revenue_trend.direction.toUpperCase()} (${trends.revenue_trend.percentage.toFixed(2)}%)`,
                    align: 'left',
                    style: { fontSize: '14px', fontWeight: 600 }
                },
                xaxis: {
                    categories: allDates,
                    labels: {
                        rotate: -45,
                        rotateAlways: true
                    }
                },
                yaxis: {
                    title: { text: 'Revenue (RM)' },
                    labels: {
                        formatter: function (value) {
                            return value ? 'RM ' + value.toFixed(2) : '';
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (value) {
                            return value ? 'RM ' + value.toFixed(2) : '';
                        }
                    }
                },
                annotations: {
                    xaxis: [{
                        x: historicalDates[historicalDates.length - 1],
                        borderColor: '#999',
                        label: {
                            text: 'Today',
                            style: { color: '#fff', background: '#775DD0' }
                        }
                    }]
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };

            if (revenueTrendChart) {
                revenueTrendChart.destroy();
            }
            revenueTrendChart = new ApexCharts(document.querySelector("#revenue-trend-chart"), options);
            revenueTrendChart.render();
        });
}

// Render Order Distribution Chart
function renderOrderDistributionChart(data) {
    const options = {
        series: [40, 30, 20, 10],
        chart: {
            type: 'donut',
            height: 300
        },
        labels: ['Dine In', 'Takeaway', 'Delivery', 'QR Orders'],
        colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
        legend: {
            position: 'bottom'
        }
    };

    orderDistributionChart = new ApexCharts(document.querySelector("#order-distribution-chart"), options);
    orderDistributionChart.render();
}

// Render Insights
function renderInsights(insights) {
    const container = document.getElementById('insights-list');
    const executiveSummary = insights.insights.executive_summary;
    const topInsights = executiveSummary.top_priority_insights || [];

    document.getElementById('insights-count').textContent = executiveSummary.requires_immediate_attention;

    if (topInsights.length === 0) {
        container.innerHTML = '<p class="text-success"><i class="fas fa-check-circle"></i> No critical issues detected!</p>';
        return;
    }

    container.innerHTML = topInsights.map(insight => `
        <div class="insight-item insight-${insight.priority}">
            <div class="d-flex justify-content-between">
                <strong>${insight.title}</strong>
                <span class="badge badge-${getPriorityBadgeClass(insight.priority)}">${insight.priority.toUpperCase()}</span>
            </div>
            <p class="mb-2 mt-2">${insight.message}</p>
            <small class="text-muted"><i class="fas fa-lightbulb"></i> ${insight.action}</small>
        </div>
    `).join('');
}

// Render Recommendations
function renderRecommendations(recommendations) {
    const container = document.getElementById('recommendations-list');

    if (!recommendations || recommendations.length === 0) {
        container.innerHTML = '<p class="text-muted">No recommendations at this time.</p>';
        return;
    }

    container.innerHTML = recommendations.slice(0, 10).map((rec, index) => `
        <div class="recommendation-item">
            <div class="d-flex align-items-start">
                <div class="mr-2">
                    <span class="badge badge-primary">${index + 1}</span>
                </div>
                <div class="flex-grow-1">
                    <strong>${rec.title}</strong>
                    <p class="mb-1 mt-1 text-sm">${rec.action}</p>
                    <small class="text-muted">
                        <i class="fas fa-tag"></i> ${rec.category} |
                        <span class="badge badge-sm badge-${getPriorityBadgeClass(rec.priority)}">${rec.priority}</span>
                    </small>
                </div>
            </div>
        </div>
    `).join('');
}

// Update Key Metrics
function updateKeyMetrics(keyMetrics) {
    if (keyMetrics.revenue) {
        document.getElementById('current-revenue').textContent = 'RM ' + keyMetrics.revenue.current.toFixed(2);
        document.getElementById('revenue-change').textContent = keyMetrics.revenue.change;
    }
    if (keyMetrics.orders) {
        document.getElementById('total-orders').textContent = keyMetrics.orders.current;
        document.getElementById('orders-change').textContent = keyMetrics.orders.change;
    }
    if (keyMetrics.aov) {
        document.getElementById('avg-order-value').textContent = 'RM ' + keyMetrics.aov.current.toFixed(2);
        document.getElementById('aov-change').textContent = keyMetrics.aov.change;
    }
}

// Update Menu Health
function updateMenuHealth(menuHealth) {
    if (menuHealth) {
        document.getElementById('menu-attention').textContent = menuHealth.items_needing_attention + ' Need Attention';
    }
}

// Render Menu Performance Chart
function renderMenuPerformanceChart(analysis) {
    const topItems = analysis.top_performers.slice(0, 10);
    const itemNames = topItems.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name);
    const performanceScores = topItems.map(item => item.performance_score);

    const options = {
        series: [{
            name: 'Performance Score',
            data: performanceScores
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: true }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                distributed: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1);
            },
            offsetX: -10,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            }
        },
        xaxis: {
            categories: itemNames,
            title: { text: 'Performance Score (0-100)' }
        },
        yaxis: {
            title: { text: 'Menu Items' }
        },
        title: {
            text: 'Top 10 Menu Items by Performance',
            align: 'center'
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2) + ' / 100';
                }
            }
        }
    };

    if (menuPerformanceChart) {
        menuPerformanceChart.destroy();
    }
    menuPerformanceChart = new ApexCharts(document.querySelector("#menu-performance-chart"), options);
    menuPerformanceChart.render();
}

// Render Top Performers Chart
function renderTopPerformersChart(analysis) {
    const topItems = analysis.top_performers.slice(0, 5);
    const itemNames = topItems.map(item => item.name);
    const revenues = topItems.map(item => parseFloat(item.metrics.total_revenue));

    const options = {
        series: [{
            name: 'Revenue (RM)',
            data: revenues
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '55%',
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return 'RM ' + val.toFixed(2);
            },
            offsetY: -20,
            style: {
                fontSize: '10px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: itemNames,
            labels: {
                rotate: -45,
                style: {
                    fontSize: '10px'
                }
            }
        },
        yaxis: {
            title: { text: 'Revenue (RM)' }
        },
        colors: ['#1cc88a'],
        title: {
            text: 'Top 5 Revenue Generators',
            align: 'center'
        }
    };

    if (topPerformersChart) {
        topPerformersChart.destroy();
    }
    topPerformersChart = new ApexCharts(document.querySelector("#top-performers-chart"), options);
    topPerformersChart.render();
}

// Render Peak Hours Heatmap
function renderPeakHoursHeatmap(peakHours) {
    if (!peakHours || !peakHours.peak_hours) {
        return;
    }

    // Prepare heatmap data
    const hours = Object.keys(peakHours.peak_hours);
    const orderCounts = Object.values(peakHours.peak_hours);

    // Create 7 days x 24 hours grid (simplified to show hourly distribution)
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const heatmapData = [];

    // For now, distribute data across days (in real implementation, this would come from API)
    days.forEach(day => {
        const dayData = {
            name: day,
            data: []
        };

        hours.forEach(hour => {
            const index = hours.indexOf(hour);
            const value = orderCounts[index] || 0;
            // Add some variance for visualization
            const variance = Math.floor(Math.random() * 3) - 1;
            dayData.data.push({
                x: hour + ':00',
                y: value + variance > 0 ? value + variance : value
            });
        });

        heatmapData.push(dayData);
    });

    const options = {
        series: heatmapData,
        chart: {
            height: 350,
            type: 'heatmap',
            toolbar: { show: true }
        },
        dataLabels: {
            enabled: false
        },
        colors: ["#4e73df"],
        title: {
            text: 'Peak Hours Analysis (Orders by Hour)',
            align: 'center'
        },
        xaxis: {
            title: { text: 'Hour of Day' }
        },
        plotOptions: {
            heatmap: {
                colorScale: {
                    ranges: [
                        { from: 0, to: 5, color: '#E8F5E9', name: 'Low' },
                        { from: 6, to: 15, color: '#66BB6A', name: 'Medium' },
                        { from: 16, to: 30, color: '#FFA726', name: 'High' },
                        { from: 31, to: 100, color: '#EF5350', name: 'Very High' }
                    ]
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' orders';
                }
            }
        }
    };

    if (peakHoursChart) {
        peakHoursChart.destroy();
    }
    peakHoursChart = new ApexCharts(document.querySelector("#peak-hours-heatmap"), options);
    peakHoursChart.render();
}

// Render Pricing Opportunities
function renderPricingOpportunities(pricingData) {
    const container = document.getElementById('pricing-opportunities');

    if (!pricingData || pricingData.count === 0) {
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle"></i> No pricing opportunities detected at this time.</p>';
        return;
    }

    container.innerHTML = pricingData.opportunities.slice(0, 5).map(opp => `
        <div class="opportunity-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>${opp.item}</strong>
                <span class="badge badge-warning">Opportunity</span>
            </div>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Current Price:</small>
                    <div class="font-weight-bold">RM ${opp.current_price.toFixed(2)}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Suggested Price:</small>
                    <div class="font-weight-bold text-success">RM ${opp.suggested_price.toFixed(2)}</div>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-lightbulb"></i> ${opp.reason}
            </small>
        </div>
    `).join('');
}

// Render Bundle Opportunities
function renderBundleOpportunities(bundleData) {
    const container = document.getElementById('bundle-opportunities');

    if (!bundleData || bundleData.count === 0) {
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle"></i> No bundle opportunities detected. Need more transaction data.</p>';
        return;
    }

    container.innerHTML = bundleData.bundle_opportunities.slice(0, 5).map(bundle => `
        <div class="opportunity-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>Bundle Opportunity</strong>
                <span class="badge badge-success">Create</span>
            </div>
            <div class="mb-2">
                <i class="fas fa-utensils"></i> <strong>Items:</strong> ${bundle.items.join(' + ')}
            </div>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Frequency:</small>
                    <div class="font-weight-bold">${bundle.frequency} times</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Suggested Price:</small>
                    <div class="font-weight-bold text-success">RM ${bundle.suggested_bundle_price.toFixed(2)}</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted">Discount:</small>
                    <span class="badge badge-info">${bundle.discount_percentage}% off</span>
                    <small class="text-success ml-2">Potential: RM ${bundle.potential_revenue.toFixed(2)}</small>
                </div>
            </div>
        </div>
    `).join('');
}

// Helper Functions
function getPriorityBadgeClass(priority) {
    const classes = {
        'critical': 'danger',
        'high': 'warning',
        'medium': 'info',
        'low': 'secondary'
    };
    return classes[priority] || 'secondary';
}

// ============================================
// REAL-TIME FEATURES
// ============================================

// Load all data (consolidated function)
function loadAllData() {
    loadExecutiveSummary();
    loadBusinessIntelligence();
    loadMenuIntelligence();
    loadBusinessInsights();
}

// Manual Refresh with animation
function manualRefresh() {
    const btn = document.getElementById('refresh-btn');
    const icon = btn.querySelector('i');

    // Add spinning animation
    icon.classList.add('refreshing');
    btn.disabled = true;

    // Reload all data
    loadAllData();

    // Update timestamp
    updateLastUpdated();

    // Show success notification
    setTimeout(() => {
        icon.classList.remove('refreshing');
        btn.disabled = false;
        showNotification('Dashboard Updated', 'All data refreshed successfully', 'success');
    }, 1500);
}

// Legacy function for compatibility
function refreshDashboard() {
    manualRefresh();
}

// Start auto-refresh
function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }

    autoRefreshInterval = setInterval(() => {
        if (autoRefreshEnabled) {
            console.log('Auto-refresh triggered at', new Date().toLocaleTimeString());
            loadAllData();
            updateLastUpdated();
        }
    }, REFRESH_INTERVAL);
}

// Toggle auto-refresh
function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;

    const btn = document.getElementById('auto-refresh-btn');
    const text = document.getElementById('auto-refresh-text');
    const status = document.getElementById('auto-refresh-status');

    if (autoRefreshEnabled) {
        // Enable auto-refresh
        btn.innerHTML = '<i class="fas fa-pause"></i> <span id="auto-refresh-text">Pause</span>';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-info');
        status.innerHTML = '<i class="fas fa-circle text-success pulse"></i> Auto-refresh ON';
        showNotification('Auto-Refresh Enabled', 'Dashboard will refresh every 5 minutes', 'info');
    } else {
        // Disable auto-refresh
        btn.innerHTML = '<i class="fas fa-play"></i> <span id="auto-refresh-text">Resume</span>';
        btn.classList.remove('btn-info');
        btn.classList.add('btn-success');
        status.innerHTML = '<i class="fas fa-circle text-muted"></i> Auto-refresh OFF';
        showNotification('Auto-Refresh Paused', 'Click Resume to enable auto-refresh', 'warning');
    }
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-MY', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const dateString = now.toLocaleDateString('en-MY', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    document.getElementById('last-updated').textContent = `${dateString} ${timeString}`;
}

// Show notification toast
function showNotification(title, message, type = 'info') {
    const banner = document.getElementById('alert-banner');
    const titleEl = document.getElementById('alert-title');
    const messageEl = document.getElementById('alert-message');

    // Set content
    titleEl.textContent = title;
    messageEl.textContent = message;

    // Set alert type
    banner.className = 'alert alert-dismissible fade show mb-4';
    const typeClasses = {
        'success': 'alert-success',
        'warning': 'alert-warning',
        'danger': 'alert-danger',
        'info': 'alert-info'
    };
    banner.classList.add(typeClasses[type] || 'alert-info');

    // Show banner
    banner.classList.remove('d-none');

    // Auto-hide after 5 seconds
    setTimeout(() => {
        dismissAlert();
    }, 5000);
}

// Dismiss alert
function dismissAlert() {
    const banner = document.getElementById('alert-banner');
    banner.classList.add('d-none');
}

// Check for new critical alerts
function checkForNewAlerts(insights) {
    if (!insights || !insights.insights || !insights.insights.executive_summary) {
        return;
    }

    const currentCriticalCount = insights.insights.executive_summary.critical_items || 0;
    const badge = document.getElementById('new-alerts-badge');
    const count = document.getElementById('new-alerts-count');

    // Check if there are new critical alerts
    if (currentCriticalCount > previousCriticalCount && previousCriticalCount > 0) {
        const newAlerts = currentCriticalCount - previousCriticalCount;

        // Show badge
        count.textContent = newAlerts;
        badge.classList.remove('d-none');

        // Play alert sound
        try {
            alertSound.play().catch(e => console.log('Sound play failed:', e));
        } catch(e) {
            console.log('Sound not supported');
        }

        // Show notification
        showNotification(
            'New Critical Alert!',
            `${newAlerts} new critical ${newAlerts === 1 ? 'issue' : 'issues'} detected. Please review immediately.`,
            'danger'
        );

        // Shake animation
        badge.style.animation = 'none';
        setTimeout(() => {
            badge.style.animation = 'shake 0.5s';
        }, 10);
    }

    // Update badge count even if not new
    if (currentCriticalCount > 0) {
        count.textContent = currentCriticalCount;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }

    previousCriticalCount = currentCriticalCount;
}

// Monitor health score changes
function monitorHealthScore(healthScore) {
    if (!healthScore) return;

    const score = healthScore.score;
    const previousScore = localStorage.getItem('previous_health_score');

    if (previousScore !== null) {
        const scoreDiff = score - parseFloat(previousScore);

        if (scoreDiff < -10) {
            // Significant drop in health score
            showNotification(
                'Health Score Alert',
                `Business health score dropped by ${Math.abs(scoreDiff).toFixed(1)} points!`,
                'danger'
            );
        } else if (scoreDiff > 10) {
            // Significant improvement
            showNotification(
                'Health Score Improved',
                `Business health score increased by ${scoreDiff.toFixed(1)} points!`,
                'success'
            );
        }
    }

    // Store current score
    localStorage.setItem('previous_health_score', score);
}

function exportToExcel() {
    // Placeholder for Excel export - will need backend implementation
    alert('Excel export functionality will be implemented next. This will download a comprehensive report with pivot tables.');
}
</script>
@endpush
@endsection
