@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Overview')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection

@section('content')
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Orders</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalOrders ?? 0 }}</div>
        <div class="admin-card-desc">{{ $todayOrders ?? 0 }} orders today</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Revenue</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM {{ number_format($todayRevenue ?? 0, 2) }}</div>
        <div class="admin-card-desc">{{ $revenueGrowth ?? 0 }}% from last week</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Customer Feedback</div>
          <div class="admin-card-icon icon-orange"><i class="fas fa-comments"></i></div>
        </div>
        <div class="admin-card-value">{{ $customerFeedbackCount ?? 0 }}</div>
        <div class="admin-card-desc">{{ $feedbackGrowth ?? 0 }}% new this week</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Avg. Rating</div>
          <div class="admin-card-icon icon-red"><i class="fas fa-star"></i></div>
        </div>
        <div class="admin-card-value">{{ number_format($averageRating ?? 0, 1) }}/5</div>
        <div class="admin-card-desc">From {{ $totalReviews ?? 0 }} reviews</div>
    </div>
</div>

<!-- Unpaid Orders Alert Section -->
@if($unpaidOrders->isNotEmpty())
<div class="admin-section" style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
    <div class="section-header" style="margin-bottom: 16px;">
        <h2 class="section-title" style="color: #dc2626; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-exclamation-triangle"></i>
            Unpaid Orders Alert ({{ $unpaidOrders->count() }})
        </h2>
        <a href="{{ route('admin.order.index', ['payment_status' => 'unpaid', 'is_flagged_unpaid' => true]) }}" class="admin-btn btn-danger">
            <i class="fas fa-eye"></i> View All
        </a>
    </div>
    <p style="color: #991b1b; margin-bottom: 16px; font-weight: 600;">
        <i class="fas fa-info-circle"></i> The following orders have been completed/served for more than 4 hours but remain unpaid:
    </p>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
        @foreach($unpaidOrders->take(6) as $order)
        <div style="background: white; padding: 16px; border-radius: 8px; border: 2px solid #fecaca;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                <div>
                    <div style="font-weight: 700; font-size: 16px; color: #1f2937;">
                        #{{ $order->confirmation_code }}
                    </div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 2px;">
                        <i class="fas fa-user"></i> {{ $order->customer_name }}
                    </div>
                </div>
                <span style="background: #dc2626; color: white; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                    UNPAID
                </span>
            </div>

            <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;">
                <i class="fas fa-dollar-sign"></i> Amount: <strong style="color: #1f2937;">RM {{ number_format($order->total_amount, 2) }}</strong>
            </div>

            <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;">
                <i class="fas fa-{{ $order->payment_method == 'online' ? 'credit-card' : 'money-bill-wave' }}"></i>
                Payment: <strong>{{ ucfirst($order->payment_method) }}</strong>
            </div>

            @if($order->table_number)
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;">
                <i class="fas fa-chair"></i> Table {{ $order->table_number }}
            </div>
            @endif

            <div style="font-size: 13px; color: #dc2626; margin-bottom: 12px; font-weight: 600;">
                @php
                    $completionTime = $order->actual_completion_time ?? $order->updated_at;
                    $hoursUnpaid = $completionTime->diffInHours(now());
                @endphp
                <i class="fas fa-clock"></i> {{ $hoursUnpaid }}h unpaid
                <span style="font-size: 11px; font-weight: 400; color: #6b7280;">
                    (since {{ $completionTime->format('M d, h:i A') }})
                </span>
            </div>

            <a href="{{ route('admin.order.show', $order->id) }}" class="admin-btn btn-sm btn-danger" style="width: 100%; justify-content: center;">
                <i class="fas fa-file-alt"></i> View Order
            </a>
        </div>
        @endforeach
    </div>

    @if($unpaidOrders->count() > 6)
    <div style="margin-top: 16px; text-align: center;">
        <a href="{{ route('admin.order.index', ['payment_status' => 'unpaid', 'is_flagged_unpaid' => true]) }}" style="color: #dc2626; font-weight: 600; text-decoration: underline;">
            View {{ $unpaidOrders->count() - 6 }} more unpaid order(s) →
        </a>
    </div>
    @endif
</div>
@endif

<!-- Sales Chart Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Sales Over Last 7 Days</h2>
    </div>
    <div class="chart-container" style="position: relative;">
        <canvas id="salesChart"></canvas>
    </div>
</div>


<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Recent Activity</h2>
        <button class="admin-btn btn-secondary">
            <div class="admin-nav-icon"><i class="fas fa-download"></i></div>
            Export
        </button>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Activity</th>
                <th>Time</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentActivity ?? [] as $user)
            <tr>
                <td data-label="User">{{ $user->name }}</td>
                <td data-label="Activity">Profile Update</td>
                <td data-label="Time">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}</td>
                <td data-label="Details">{{ $user->email }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No recent activity</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Popular Items Section -->
    <div class="admin-section">
      <div class="section-header">
        <h2 class="section-title">Popular Menu Items</h2>
        <button class="admin-btn btn-secondary">
          <div class="admin-nav-icon"><i class="fas fa-filter"></i></div>
          Filter
        </button>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Orders</th>
            <th>Revenue</th>
            <th>Trend</th>
          </tr>
        </thead>
        <tbody>
          @forelse($popularMenuItems ?? [] as $item)
          <tr>
            <td data-label="Item">{{ $item->name }}</td>
            <td data-label="Category">{{ $item->category->name ?? 'N/A' }}</td>
            <td data-label="Orders">{{ $item->order_items_count }}</td>
            <td data-label="Revenue">RM {{ number_format($item->price, 2) }}</td>
            <td data-label="Trend">-</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">No popular items found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Sales (RM)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection
