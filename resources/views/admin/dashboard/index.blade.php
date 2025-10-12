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
                <td>{{ $user->name }}</td>
                <td>Profile Update</td>
                <td>{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}</td>
                <td>{{ $user->email }}</td>
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
            <td>{{ $item->name }}</td>
            <td>{{ $item->category->name ?? 'N/A' }}</td>
            <td>{{ $item->order_items_count }}</td>
            <td>RM {{ number_format($item->price, 2) }}</td>
            <td>-</td>
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
