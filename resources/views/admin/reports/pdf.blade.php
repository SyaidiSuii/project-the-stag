<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Analytics Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
        }
        .report-container {
            width: 100%;
            margin: 0 auto;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header h1 {
            margin: 0;
            font-size: 22px;
            color: #222;
        }
        .report-header p {
            margin: 4px 0;
            font-size: 13px;
            color: #666;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f7f7f7;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1>The Stag SmartDine</h1>
            <p>Full Analytics Report</p>
            <p><strong>Report Date:</strong> {{ $reportDate }}</p>
            <p><strong>Period:</strong> {{ $reportPeriod }}</p>
        </div>

        <div class="section-title">Monthly Summary</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Revenue</td>
                    <td class="text-right">RM {{ number_format($currentMonthRevenue, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Orders</td>
                    <td class="text-right">{{ $currentMonthOrders }}</td>
                </tr>
                <tr>
                    <td>Average Order Value</td>
                    <td class="text-right">RM {{ number_format($currentMonthAvgOrderValue, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Order Distribution</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orderDistribution as $status => $count)
                <tr>
                    <td>{{ ucfirst($status) }}</td>
                    <td class="text-right">{{ $count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="no-data">No order distribution data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Top 10 Menu Items by Performance</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th class="text-right">Performance Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($top10Items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right">{{ number_format($item['performance_score'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="no-data">No menu performance data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Top 5 Revenue Generators</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th class="text-right">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($top5Revenue as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right">RM {{ number_format($item['metrics']['total_revenue'] ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="no-data">No revenue generator data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Most Booked Tables</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Table Number</th>
                    <th class="text-right">Booking Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mostBookedTables as $table)
                <tr>
                    <td>Table {{ $table->table_number }}</td>
                    <td class="text-right">{{ $table->booking_count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="no-data">No table booking data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Peak Hours Analysis</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Hour</th>
                    <th class="text-right">Order Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peakHours as $hour => $count)
                <tr>
                    <td>{{ $hour }}:00 - {{ $hour }}:59</td>
                    <td class="text-right">{{ $count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="no-data">No peak hours data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</body>
</html>
