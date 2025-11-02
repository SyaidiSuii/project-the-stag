<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Report - The Stag</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            max-width: 120px;
            margin: 0 auto 15px;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 10px;
        }
        
        .report-subtitle {
            font-size: 16px;
            color: #64748b;
            margin: 0 0 20px;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: #475569;
        }
        
        .info-value {
            font-weight: 500;
            color: #1e293b;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 5px;
        }
        
        .stat-change {
            font-size: 13px;
            color: #64748b;
        }
        
        .positive {
            color: #10b981;
        }
        
        .negative {
            color: #ef4444;
        }
        
        .chart-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 15px;
        }
        
        .chart-placeholder {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
            color: #94a3b8;
            font-size: 14px;
        }
        
        .promotion-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .promotion-stat {
            text-align: center;
            padding: 15px;
            background-color: #f1f5f9;
            border-radius: 8px;
        }
        
        .promotion-stat-label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .promotion-stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }
        
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 30px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                font-size: 12px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .promotion-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="report-title">Analytics Report</h1>
            <p class="report-subtitle">The Stag Restaurant Performance Dashboard</p>
            <div class="report-info">
                <div class="info-item">
                    <span class="info-label">Report Generated</span>
                    <span class="info-value">{{ $reportDate ?? now()->format('F j, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Reporting Period</span>
                    <span class="info-value">{{ $reportPeriod ?? 'Last 30 Days' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Report Type</span>
                    <span class="info-value">Performance Analytics</span>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">📊 Core Performance Metrics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-value">RM {{ number_format($currentMonthRevenue ?? 0, 2) }}</div>
                    <div class="stat-change {{ ($revenueChangePercentage ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        @if(($revenueChangePercentage ?? 0) >= 0)
                            ↑
                        @else
                            ↓
                        @endif
                        {{ abs($revenueChangePercentage ?? 0) }}% from last month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-value">{{ number_format($currentMonthOrders ?? 0) }}</div>
                    <div class="stat-change {{ ($ordersChangePercentage ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        @if(($ordersChangePercentage ?? 0) >= 0)
                            ↑
                        @else
                            ↓
                        @endif
                        {{ abs($ordersChangePercentage ?? 0) }}% from last month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Avg Order Value</div>
                    </div>
                    <div class="stat-value">RM {{ number_format($currentMonthAvgOrderValue ?? 0, 2) }}</div>
                    <div class="stat-change {{ ($avgOrderValueChangePercentage ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        @if(($avgOrderValueChangePercentage ?? 0) >= 0)
                            ↑
                        @else
                            ↓
                        @endif
                        {{ abs($avgOrderValueChangePercentage ?? 0) }}% from last month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Active Menu Items</div>
                    </div>
                    <div class="stat-value">{{ $activeItems ?? 0 }}</div>
                    <div class="stat-change">
                        +{{ $newItemsThisMonth ?? 0 }} new items this month
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">📱 Additional Metrics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">QR Orders</div>
                    </div>
                    <div class="stat-value">{{ number_format($qrOrders ?? 0) }}</div>
                    <div class="stat-change">
                        RM {{ number_format($qrRevenue ?? 0, 2) }} revenue
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Table Bookings</div>
                    </div>
                    <div class="stat-value">{{ number_format($tableBookings ?? 0) }}</div>
                    <div class="stat-change">
                        Reservations this month
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Promotions Used</div>
                    </div>
                    <div class="stat-value">{{ number_format($promotionsUsed ?? 0) }}</div>
                    <div class="stat-change">
                        RM {{ number_format($promotionDiscounts ?? 0, 2) }} discounts
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Rewards Redeemed</div>
                    </div>
                    <div class="stat-value">{{ number_format($rewardsRedeemed ?? 0) }}</div>
                    <div class="stat-change">
                        Loyalty program redemptions
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">👥 Customer Insights</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">New Customers</div>
                    </div>
                    <div class="stat-value">{{ number_format($customerRetention['new_customers'] ?? 0) }}</div>
                    <div class="stat-change">
                        First-time visitors
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Returning Customers</div>
                    </div>
                    <div class="stat-value">{{ number_format($customerRetention['returning_customers'] ?? 0) }}</div>
                    <div class="stat-change">
                        Repeat visitors
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Retention Rate</div>
                    </div>
                    <div class="stat-value">{{ number_format($customerRetention['retention_rate'] ?? 0, 1) }}%</div>
                    <div class="stat-change">
                        Customer loyalty
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-label">Total Customers</div>
                    </div>
                    <div class="stat-value">{{ number_format(($customerRetention['new_customers'] ?? 0) + ($customerRetention['returning_customers'] ?? 0)) }}</div>
                    <div class="stat-change">
                        Active this period
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">📈 Sales Performance Charts</h2>
            
            <div class="chart-container">
                <h3 class="chart-title">Sales Overview (Last 30 Days)</h3>
                <div class="chart-placeholder">
                    Sales trend chart visualization would appear here in the digital version
                </div>
                <p><strong>Data Summary:</strong> Total revenue of RM {{ number_format($currentMonthRevenue ?? 0, 2) }} across {{ number_format($currentMonthOrders ?? 0) }} orders with an average value of RM {{ number_format($currentMonthAvgOrderValue ?? 0, 2) }} per order.</p>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">Top 10 Selling Products</h3>
                <div class="chart-placeholder">
                    Top products bar chart visualization would appear here in the digital version
                </div>
                <p><strong>Top Performer:</strong> 
                    @if(!empty($topSellingProducts) && isset($topSellingProducts[0]) && is_array($topSellingProducts[0]))
                        {{ $topSellingProducts[0]['menu_item']['name'] ?? 'Unknown Item' }} ({{ number_format($topSellingProducts[0]['total_quantity'] ?? 0) }} units sold)
                    @else
                        No data available
                    @endif
                </p>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">🏷️ Sales Distribution</h2>
            
            <div class="chart-container">
                <h3 class="chart-title">Sales by Category</h3>
                <div class="chart-placeholder">
                    Category distribution pie chart visualization would appear here in the digital version
                </div>
                <p><strong>Category Breakdown:</strong> 
                    @if(isset($salesByCategory) && is_array($salesByCategory))
                        @foreach($salesByCategory['labels'] as $index => $label)
                            {{ $label }}: RM {{ number_format($salesByCategory['revenue'][$index] ?? 0) }}@if(!$loop->last), @endif
                        @endforeach
                    @else
                        Data not available
                    @endif
                </p>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">Order Types Distribution</h3>
                <div class="chart-placeholder">
                    Order types distribution chart visualization would appear here in the digital version
                </div>
                <p><strong>Order Type Summary:</strong> 
                    @if(isset($orderTypeBreakdown) && is_array($orderTypeBreakdown))
                        @foreach($orderTypeBreakdown['labels'] as $index => $label)
                            {{ $label }}: {{ number_format($orderTypeBreakdown['data'][$index] ?? 0) }} orders (RM {{ number_format($orderTypeBreakdown['revenue'][$index] ?? 0) }})@if(!$loop->last), @endif
                        @endforeach
                    @else
                        Data not available
                    @endif
                </p>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">📱 Channel Performance</h2>
            
            <div class="chart-container">
                <h3 class="chart-title">QR vs Web Orders (Last 30 Days)</h3>
                <div class="chart-placeholder">
                    QR vs Web orders comparison chart visualization would appear here in the digital version
                </div>
                <p><strong>Channel Performance:</strong> 
                    @if(isset($qrVsWeb) && is_array($qrVsWeb) && isset($qrVsWeb['labels']) && count($qrVsWeb['labels']) >= 2)
                        {{ $qrVsWeb['labels'][0] }}: {{ number_format($qrVsWeb['data'][0] ?? 0) }} orders 
                        @if(isset($qrVsWeb['percentage']))
                            ({{ $qrVsWeb['percentage']['web'] ?? 0 }}%),
                        @else
                            (0%),
                        @endif
                        {{ $qrVsWeb['labels'][1] }}: {{ number_format($qrVsWeb['data'][1] ?? 0) }} orders 
                        @if(isset($qrVsWeb['percentage']))
                            ({{ $qrVsWeb['percentage']['qr'] ?? 0 }}%)
                        @else
                            (0%)
                        @endif
                    @else
                        Data not available
                    @endif
                </p>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">🎁 Promotion Effectiveness</h2>
            
            <div class="chart-container">
                <h3 class="chart-title">Promotion Performance Metrics</h3>
                <div class="promotion-stats">
                    <div class="promotion-stat">
                        <div class="promotion-stat-label">Total Promotions Used</div>
                        <div class="promotion-stat-value">{{ number_format($promotionStats['total_usage'] ?? 0) }}</div>
                    </div>
                    <div class="promotion-stat">
                        <div class="promotion-stat-label">Total Discounts</div>
                        <div class="promotion-stat-value">RM {{ number_format($promotionStats['total_discounts'] ?? 0, 2) }}</div>
                    </div>
                    <div class="promotion-stat">
                        <div class="promotion-stat-label">Revenue Impact</div>
                        <div class="promotion-stat-value">{{ number_format($promotionStats['revenue_impact_percentage'] ?? 0, 2) }}%</div>
                    </div>
                    <div class="promotion-stat">
                        <div class="promotion-stat-label">Avg Discount/Use</div>
                        <div class="promotion-stat-value">RM {{ number_format($promotionStats['avg_discount_per_use'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <p style="margin-top: 20px;"><strong>Promotion Summary:</strong> {{ number_format($promotionsUsed ?? 0) }} promotions utilized with a total discount value of RM {{ number_format($promotionDiscounts ?? 0, 2) }}, representing a {{ number_format($promotionStats['revenue_impact_percentage'] ?? 0, 2) }}% impact on revenue.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Report generated by The Stag Restaurant Management System</p>
            <p>This is a computer-generated document. No signature required.</p>
        </div>
    </div>
</body>
</html>