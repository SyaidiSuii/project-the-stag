@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Orders</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div class="admin-card-value">142</div>
        <div class="admin-card-desc">+12% from last week</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Revenue</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">RM 8,542</div>
        <div class="admin-card-desc">+8% from last week</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Customer Feedback</div>
          <div class="admin-card-icon icon-orange"><i class="fas fa-comments"></i></div>
        </div>
        <div class="admin-card-value">24</div>
        <div class="admin-card-desc">+4 new this week</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Avg. Rating</div>
          <div class="admin-card-icon icon-red"><i class="fas fa-star"></i></div>
        </div>
        <div class="admin-card-value">4.8/5</div>
        <div class="admin-card-desc">From 128 reviews</div>
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
            <tr>
                <td>Sarah Johnson</td>
                <td>Made a reservation</td>
                <td>2 hours ago</td>
                <td>Table for 4, Nov 15, 7:00 PM</td>
            </tr>
            <tr>
                <td>Robert Chen</td>
                <td>Placed an order</td>
                <td>3 hours ago</td>
                <td>Order #1245, RM 85.00</td>
            </tr>
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
          <tr>
            <td>Beef Steak (235g++)</td>
            <td>Steaks</td>
            <td>42</td>
            <td>RM 1,890</td>
            <td>↑ 15%</td>
          </tr>
          <tr>
            <td>Tomyam Seafood</td>
            <td>Tom Yam</td>
            <td>38</td>
            <td>RM 608</td>
            <td>↑ 8%</td>
          </tr>
          <tr>
            <td>Chicken Chop Special</td>
            <td>Western</td>
            <td>35</td>
            <td>RM 525</td>
            <td>→ Steady</td>
          </tr>
          <tr>
            <td>Nasi Lemak with Chicken Rendang</td>
            <td>Malaysian</td>
            <td>31</td>
            <td>RM 387.50</td>
            <td>↑ 22%</td>
          </tr>
        </tbody>
      </table>
    </div>
@endsection
