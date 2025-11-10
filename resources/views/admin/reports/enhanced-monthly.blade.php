@extends('layouts.admin')

@section('title', 'Enhanced Analytics Dashboard')

@section('content')
<div class="analytics-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="dashboard-header-left">
            <h1>
                <i class="fas fa-chart-line"></i> Enhanced Analytics Dashboard
                <span id="new-alerts-badge" class="badge badge-danger" style="display: none;">
                    <i class="fas fa-bell"></i> <span id="new-alerts-count">0</span>
                </span>
            </h1>
            <small>
                <i class="fas fa-clock"></i> Last updated: <span id="last-updated">Loading...</span>
                <span style="margin-left: 16px;" id="auto-refresh-status">
                    <i class="fas fa-circle pulse" style="color: var(--success);"></i> Auto-refresh ON
                </span>
            </small>
        </div>
        <div class="dashboard-header-right">
            <button class="admin-btn btn-secondary" onclick="toggleAutoRefresh()" id="auto-refresh-btn">
                <i class="fas fa-pause"></i> <span id="auto-refresh-text">Pause</span>
            </button>
            <button class="admin-btn btn-primary" onclick="manualRefresh()" id="refresh-btn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="admin-btn btn-primary" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Export
            </button>
        </div>
    </div>

    <!-- Real-time Alert Banner -->
    <div id="alert-banner" class="alert-banner alert-info" style="display: none;">
        <div class="alert-banner-content">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alert-banner-text">
                <strong id="alert-title"></strong>
                <p id="alert-message"></p>
            </div>
        </div>
        <button class="alert-close" onclick="dismissAlert()">
            <span>&times;</span>
        </button>
    </div>

    <!-- Business Health Score Card -->
    <div class="analytics-section">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div style="flex: 0 0 auto;">
                <div style="font-size: 48px; font-weight: 800; line-height: 1;">
                    <span id="health-score">--</span>
                    <span style="font-size: 24px; color: var(--text-3);">/100</span>
                </div>
                <div style="font-size: 14px; font-weight: 700; color: var(--text-2); text-transform: uppercase; margin-top: 4px;">
                    Business Health Score
                </div>
            </div>
            <div style="flex: 0 0 auto;">
                <div style="font-size: 32px; font-weight: 700;" id="health-grade">
                    <span class="badge badge-success" style="font-size: 24px; padding: 8px 16px;">A+</span>
                </div>
            </div>
            <div style="flex: 1;">
                <div style="background: var(--muted); border-radius: 25px; height: 25px; overflow: hidden;">
                    <div id="health-progress" style="background: linear-gradient(135deg, var(--success), #059669); height: 100%; width: 0%; transition: width 0.5s ease; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; color: white; font-weight: 700; font-size: 12px;"></div>
                </div>
            </div>
            <div style="flex: 0 0 auto;">
                <div id="health-status" class="badge badge-success" style="font-size: 14px; padding: 6px 12px;">Healthy</div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="analytics-cards">
        <div class="analytics-card">
            <div class="analytics-card-header">
                <div class="analytics-card-title">Revenue</div>
                <div class="analytics-card-icon icon-blue"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <div class="analytics-card-value" id="current-revenue">RM 0.00</div>
            <div class="analytics-card-desc" id="revenue-change">--</div>
        </div>

        <div class="analytics-card">
            <div class="analytics-card-header">
                <div class="analytics-card-title">Total Orders</div>
                <div class="analytics-card-icon icon-green"><i class="fas fa-shopping-cart"></i></div>
            </div>
            <div class="analytics-card-value" id="total-orders">0</div>
            <div class="analytics-card-desc" id="orders-change">--</div>
        </div>

        <div class="analytics-card">
            <div class="analytics-card-header">
                <div class="analytics-card-title">Avg Order Value</div>
                <div class="analytics-card-icon icon-orange"><i class="fas fa-chart-bar"></i></div>
            </div>
            <div class="analytics-card-value" id="avg-order-value">RM 0.00</div>
            <div class="analytics-card-desc" id="aov-change">--</div>
        </div>

        {{-- <div class="analytics-card">
            <div class="analytics-card-header">
                <div class="analytics-card-title">Menu Health</div>
                <div class="analytics-card-icon icon-red"><i class="fas fa-utensils"></i></div>
            </div>
            <div class="analytics-card-value" style="font-size: 20px;" id="menu-attention">-- Need Attention</div>
            <div class="analytics-card-desc">Items requiring optimization</div>
        </div> --}}
    </div>

    <!-- Business Intelligence Section -->
    <div class="analytics-section">
        <div class="analytics-section-header">
            <div class="analytics-section-title">
                <i class="fas fa-chart-area"></i> Revenue Trends & Forecasting
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-container" id="revenue-trend-chart"></div>
            <div class="chart-container" id="order-distribution-chart"></div>
        </div>
    </div>

    <!-- Menu Performance Section -->
    <div class="analytics-section">
        <div class="analytics-section-header">
            <div class="analytics-section-title">
                <i class="fas fa-utensils"></i> Menu Performance Analysis
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-container" id="menu-performance-chart"></div>
            <div class="chart-container" id="top-performers-chart"></div>
        </div>

        <!-- Pricing & Bundle Opportunities -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 24px;">
            <div>
                <div class="analytics-section-title" style="margin-bottom: 16px;">
                    <i class="fas fa-tag"></i> Pricing Opportunities
                </div>
                <div id="pricing-opportunities"></div>
            </div>
            <div>
                <div class="analytics-section-title" style="margin-bottom: 16px;">
                    <i class="fas fa-gift"></i> Bundle Opportunities
                </div>
                <div id="bundle-opportunities"></div>
            </div>
        </div>
    </div>

    <!-- Peak Hours Analysis -->
    <div class="analytics-section">
        <div class="analytics-section-header">
            <div class="analytics-section-title">
                <i class="fas fa-clock"></i> Peak Hours Analysis
            </div>
        </div>
        <div class="chart-container" id="peak-hours-heatmap"></div>
        <div style="margin-top: 20px;" id="peak-hours-recommendations"></div>
    </div>

    <!-- Insights & Recommendations Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 20px;">
        <!-- Priority Insights -->
        <!-- Future Feature: Priority Insights (in coming) -->
        {{-- <div class="analytics-section">
            <div class="analytics-section-header">
                <div class="analytics-section-title">
                    <i class="fas fa-lightbulb"></i> Priority Insights
                </div>
            </div>
            <div class="insights-grid" id="insights-container"></div>
        </div> --}}
        <!-- End Future Feature: Priority Insights -->

        <!-- Top 10 Recommendations -->
        <!-- Future Feature: Top 10 Recommendations (in coming) -->
        {{-- <div class="analytics-section">
            <div class="analytics-section-header">
                <div class="analytics-section-title">
                    <i class="fas fa-tasks"></i> Top 10 Recommendations
                </div>
            </div>
            <div id="recommendations-container"></div>
        </div> --}}
        <!-- End Future Feature: Top 10 Recommendations -->
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
    color: var(--text-2);
}

#new-alerts-badge {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Utility Classes */
.text-success { color: var(--success); }
.text-warning { color: var(--warning); }
.text-danger { color: var(--danger); }
.text-info { color: var(--brand); }
.text-muted { color: var(--text-3); }
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
const alertSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjWO1fPTgjMGHW7A7+OZURE=');

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
    document.getElementById('health-score').textContent = healthScore.score.toFixed(1);
    document.getElementById('health-progress').style.width = healthScore.score + '%';
    document.getElementById('health-progress').textContent = healthScore.score.toFixed(1) + '%';

    const gradeColors = {
        'A+': 'success', 'A': 'success', 'B+': 'info', 'B': 'info',
        'C+': 'warning', 'C': 'warning', 'D': 'danger', 'F': 'danger'
    };

    const statusColors = {
        'healthy': 'success', 'needs_attention': 'warning', 'critical': 'danger'
    };

    const gradeBadge = document.getElementById('health-grade');
    gradeBadge.innerHTML = `<span class="badge badge-${gradeColors[healthScore.grade]}" style="font-size: 24px; padding: 8px 16px;">${healthScore.grade}</span>`;

    const statusBadge = document.getElementById('health-status');
    statusBadge.className = `badge badge-${statusColors[healthScore.status]}`;
    statusBadge.style.fontSize = '14px';
    statusBadge.style.padding = '6px 12px';
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
                renderMenuPerformanceChart(data.data.performance_analysis);
                renderTopPerformersChart(data.data.performance_analysis);
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
    // The 'trends' object from the backend contains daily_revenue
    // which is an array of {date, total} objects.
    const dailyData = trends.daily_revenue || [];

    // The 'forecast' object contains a 'forecast' array of values.
    const forecastData = forecast?.forecast || [];

    // Prepare dates and revenue for the chart
    const historicalDates = dailyData.map(item => 
        new Date(item.date).toLocaleDateString('en-MY', { month: 'short', day: 'numeric' })
    );
    const historicalRevenue = dailyData.map(item => item.total);

    // Prepare forecast dates
    const forecastDates = [];
    const lastHistoricalDate = dailyData.length > 0 ? new Date(dailyData[dailyData.length - 1].date) : new Date();
    for (let i = 1; i <= forecastData.length; i++) {
        const date = new Date(lastHistoricalDate);
        date.setDate(date.getDate() + i);
        forecastDates.push(date.toLocaleDateString('en-MY', { month: 'short', day: 'numeric' }));
    }

    const allDates = [...historicalDates, ...forecastDates];
    
    // The series data needs to align with allDates.
    // Historical revenue is first, then nulls for the forecast period.
    const allRevenue = [...historicalRevenue, ...Array(forecastData.length).fill(null)];
    // Nulls for the historical period, then the forecast data.
    const allForecast = [...Array(historicalRevenue.length).fill(null), ...forecastData];

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
        colors: ['#6366f1', '#10b981'],
        title: {
            text: `Revenue Trend: ${trends.revenue_trend.direction.toUpperCase()} (${trends.revenue_trend.percentage.toFixed(2)}%)`,
            align: 'left'
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
                    return value !== null ? 'RM ' + value.toFixed(2) : '';
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (value) {
                    if (value !== null) {
                        return 'RM ' + value.toFixed(2);
                    }
                    return value;
                }
            }
        }
    };

    if (revenueTrendChart) {
        revenueTrendChart.destroy();
    }
    revenueTrendChart = new ApexCharts(document.querySelector("#revenue-trend-chart"), options);
    revenueTrendChart.render();
}

// Render Order Distribution Chart
function renderOrderDistributionChart(data) {
    const distribution = data.trends?.order_status_distribution || {};
    const labels = ['Completed', 'Pending', 'Cancelled', 'Processing'];
    const series = [
        distribution.completed || 0,
        distribution.pending || 0,
        distribution.cancelled || 0,
        distribution.processing || 0,
    ];

    const options = {
        series: series,
        chart: {
            type: 'donut',
            height: 350
        },
        labels: labels,
        colors: ['#10b981', '#6366f1', '#ef4444', '#f59e0b'],
        title: {
            text: 'Order Distribution',
            align: 'left'
        },
        legend: {
            position: 'bottom'
        }
    };

    if (orderDistributionChart) {
        orderDistributionChart.destroy();
    }
    orderDistributionChart = new ApexCharts(document.querySelector("#order-distribution-chart"), options);
    orderDistributionChart.render();
}

// Render Insights
function renderInsights(insights) {
    const container = document.getElementById('insights-container');

    if (!insights || !insights.insights) {
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle"></i> No insights available</p>';
        return;
    }

    // Collect all insights
    const allInsights = [];
    Object.values(insights.insights).forEach(categoryInsights => {
        if (Array.isArray(categoryInsights)) {
            allInsights.push(...categoryInsights);
        }
    });

    // Sort by priority
    const priorityOrder = { 'critical': 0, 'high': 1, 'medium': 2, 'low': 3 };
    allInsights.sort((a, b) => (priorityOrder[a.priority] || 99) - (priorityOrder[b.priority] || 99));

    container.innerHTML = allInsights.slice(0, 10).map(insight => `
        <div class="insight-item insight-${insight.priority}">
            <div class="insight-header">
                <div class="insight-title">${insight.title}</div>
                <span class="badge badge-${getPriorityBadgeClass(insight.priority)}">${insight.priority}</span>
            </div>
            <div class="insight-message">${insight.message}</div>
            <div class="insight-action"><i class="fas fa-arrow-right"></i> ${insight.action}</div>
        </div>
    `).join('');
}

// Render Recommendations
function renderRecommendations(recommendations) {
    const container = document.getElementById('recommendations-container');

    if (!recommendations || recommendations.length === 0) {
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle"></i> No recommendations available</p>';
        return;
    }

    container.innerHTML = recommendations.slice(0, 10).map(rec => `
        <div class="recommendation-item">
            <div class="recommendation-content">
                <div class="recommendation-title">${rec.title}</div>
                <div class="recommendation-action">${rec.action}</div>
            </div>
            <div>
                <span class="badge badge-${getPriorityBadgeClass(rec.priority)}">${rec.priority}</span>
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
                distributed: true
            }
        },
        colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
        xaxis: {
            categories: itemNames,
            title: { text: 'Performance Score (0-100)' }
        },
        title: {
            text: 'Top 10 Menu Items by Performance',
            align: 'center'
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
                horizontal: false
            }
        },
        xaxis: {
            categories: itemNames,
            labels: { rotate: -45 }
        },
        yaxis: {
            title: { text: 'Revenue (RM)' }
        },
        colors: ['#10b981'],
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
        if (peakHoursChart) {
            peakHoursChart.destroy();
        }
        document.querySelector("#peak-hours-heatmap").innerHTML = '<p class="text-muted" style="text-align: center; padding-top: 50px;"><i class="fas fa-info-circle"></i> No peak hours data available</p>';
        return;
    }

    const hours = Object.keys(peakHours.peak_hours);
    const orderCounts = Object.values(peakHours.peak_hours);
    
    // The current backend data is a 1D array of hours. The frontend is trying to show a 7-day heatmap.
    // This will result in the same pattern each day, but we will remove the random noise for now.
    const days = ['Sun', 'Sat', 'Fri', 'Thu', 'Wed', 'Tue', 'Mon']; // Reverse to show Mon at bottom
    const heatmapData = [];

    days.forEach(day => {
        const dayData = { name: day, data: [] };
        hours.forEach(hour => {
            const index = hours.indexOf(hour);
            const value = orderCounts[index] || 0;
            dayData.data.push({
                x: hour + ':00',
                y: value 
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
        plotOptions: {
            heatmap: {
                colorScale: {
                    ranges: [{
                        from: 0,
                        to: 5,
                        color: '#dbeafe',
                        name: 'low',
                    }, {
                        from: 6,
                        to: 20,
                        color: '#60a5fa',
                        name: 'medium',
                    }, {
                        from: 21,
                        to: 45,
                        color: '#2563eb',
                        name: 'high',
                    }]
                }
            }
        },
        colors: ["#6366f1"],
        title: {
            text: 'Peak Hours Analysis (Orders by Hour)',
            align: 'center'
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
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle"></i> No pricing opportunities detected</p>';
        return;
    }

    container.innerHTML = pricingData.opportunities.slice(0, 5).map(opp => `
        <div class="opportunity-card">
            <div class="opportunity-header">
                <div class="opportunity-title">${opp.item}</div>
                <span class="badge badge-warning">Opportunity</span>
            </div>
            <div class="opportunity-row">
                <div class="opportunity-col">
                    <div class="opportunity-label">Current Price:</div>
                    <div class="opportunity-value">RM ${parseFloat(opp.current_price).toFixed(2)}</div>
                </div>
                <div class="opportunity-col">
                    <div class="opportunity-label">Suggested Price:</div>
                    <div class="opportunity-value text-success">RM ${parseFloat(opp.suggested_price).toFixed(2)}</div>
                </div>
            </div>
            <small class="opportunity-note"><i class="fas fa-lightbulb"></i> ${opp.reason}</small>
        </div>
    `).join('');
}

// Render Bundle Opportunities
function renderBundleOpportunities(bundleData) {
    const container = document.getElementById('bundle-opportunities');

    if (!bundleData || bundleData.count === 0) {
        container.innerHTML = '<p class="text-muted"><i class="fas fa-info-circle"></i> No bundle opportunities detected</p>';
        return;
    }

    container.innerHTML = bundleData.bundle_opportunities.slice(0, 5).map(bundle => `
        <div class="opportunity-card">
            <div class="opportunity-header">
                <div class="opportunity-title">Bundle Opportunity</div>
                <span class="badge badge-success">Create</span>
            </div>
            <div style="margin-bottom: 8px;">
                <i class="fas fa-utensils"></i> <strong>Items:</strong> ${bundle.items.join(' + ')}
            </div>
            <div class="opportunity-row">
                <div class="opportunity-col">
                    <div class="opportunity-label">Frequency:</div>
                    <div class="opportunity-value">${bundle.frequency} times</div>
                </div>
                <div class="opportunity-col">
                    <div class="opportunity-label">Suggested Price:</div>
                    <div class="opportunity-value text-success">RM ${parseFloat(bundle.suggested_bundle_price).toFixed(2)}</div>
                </div>
            </div>
            <div style="margin-top: 8px;">
                <span class="badge badge-info">${bundle.discount_percentage}% off</span>
                <small class="text-success" style="margin-left: 8px;">Potential: RM ${parseFloat(bundle.potential_revenue).toFixed(2)}</small>
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

    icon.classList.add('refreshing');
    btn.disabled = true;

    loadAllData();
    updateLastUpdated();

    setTimeout(() => {
        icon.classList.remove('refreshing');
        btn.disabled = false;
        showNotification('Dashboard Updated', 'All data refreshed successfully', 'success');
    }, 1500);
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
    const status = document.getElementById('auto-refresh-status');

    if (autoRefreshEnabled) {
        btn.innerHTML = '<i class="fas fa-pause"></i> <span id="auto-refresh-text">Pause</span>';
        status.innerHTML = '<i class="fas fa-circle pulse" style="color: var(--success);"></i> Auto-refresh ON';
        showNotification('Auto-Refresh Enabled', 'Dashboard will refresh every 5 minutes', 'info');
    } else {
        btn.innerHTML = '<i class="fas fa-play"></i> <span id="auto-refresh-text">Resume</span>';
        status.innerHTML = '<i class="fas fa-circle" style="color: var(--text-3);"></i> Auto-refresh OFF';
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

    titleEl.textContent = title;
    messageEl.textContent = message;

    banner.className = 'alert-banner alert-' + type;
    banner.style.display = 'flex';

    setTimeout(() => {
        dismissAlert();
    }, 5000);
}

// Dismiss alert
function dismissAlert() {
    const banner = document.getElementById('alert-banner');
    banner.style.display = 'none';
}

// Check for new critical alerts
function checkForNewAlerts(insights) {
    if (!insights || !insights.insights || !insights.insights.executive_summary) {
        return;
    }

    const currentCriticalCount = insights.insights.executive_summary.critical_items || 0;
    const badge = document.getElementById('new-alerts-badge');
    const count = document.getElementById('new-alerts-count');

    if (currentCriticalCount > previousCriticalCount && previousCriticalCount > 0) {
        const newAlerts = currentCriticalCount - previousCriticalCount;

        count.textContent = newAlerts;
        badge.style.display = 'inline-block';

        try {
            alertSound.play().catch(e => console.log('Sound play failed:', e));
        } catch(e) {
            console.log('Sound not supported');
        }

        showNotification(
            'New Critical Alert!',
            `${newAlerts} new critical ${newAlerts === 1 ? 'issue' : 'issues'} detected. Please review immediately.`,
            'danger'
        );

        badge.style.animation = 'shake 0.5s';
    }

    if (currentCriticalCount > 0) {
        count.textContent = currentCriticalCount;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
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
            showNotification(
                'Health Score Alert',
                `Business health score dropped by ${Math.abs(scoreDiff).toFixed(1)} points!`,
                'danger'
            );
        } else if (scoreDiff > 10) {
            showNotification(
                'Health Score Improved',
                `Business health score increased by ${scoreDiff.toFixed(1)} points!`,
                'success'
            );
        }
    }

    localStorage.setItem('previous_health_score', score);
}

function exportToExcel() {
    alert('Excel export functionality will be implemented next. This will download a comprehensive report with pivot tables.');
}
</script>
@endpush
@endsection
