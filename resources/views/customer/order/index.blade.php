@extends('layouts.customer')

@section('title', 'Orders - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
@endsection

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="header-section">
      <!-- Search Bar -->
      <div class="search-bar-container" role="search">
        <div class="search-bar">
          <span class="search-icon" aria-hidden="true">🔍</span>
          <input type="text" class="search-input" placeholder="Search orders by ID or item..." id="searchInput" aria-label="Search orders" />
          <button class="clear-btn" id="clearSearch" aria-label="Clear search">✕</button>
        </div>
      </div>

      <!-- Category Tabs -->
      <div class="category-tabs" role="tablist" aria-label="Order Categories">
        <button class="tab" data-category="All" aria-current="page" role="tab">All</button>
        <button class="tab" data-category="Ongoing" role="tab">Ongoing</button>
        <button class="tab" data-category="History" role="tab">Order History</button>
        <button class="tab" data-category="Booking" role="tab">Booking</button>
      </div>

      <!-- Dynamic Category Title -->
      <h1 class="category-title" id="categoryTitle">All Orders</h1>
      <p class="no-results" id="noResults">No results found. Try another keyword.</p>
    </div>

    <!-- ======= Orders Section (Dynamic Content) ======= -->
    <div class="category-section" data-category="All">
      <div class="orders-grid" id="ordersGrid"><div class="order-card" data-id="ORD-2024-001" data-status="preparing">
          <div class="order-header">
            <div class="order-info">
              <h3>Order #ORD-2024-001</h3>
              <div class="order-date">Today, 2:30 PM</div>
            </div>
            <div class="order-status status-preparing">Preparing</div>
          </div>
          <div class="order-items">
            <h4>Items</h4>
            <p class="item-list-text" title="Beef Steak (180g++) (x1), Americano (x2), French Fries (x1)">Beef Steak (180g++) (x1), Americano (x2), French F...</p>
          </div>
          <div class="order-total">
            <span class="total-label">Total</span>
            <span class="total-amount">RM 57.00</span>
          </div>
          
          <div class="order-actions">
            
            <button class="btn btn-primary track-order-btn">Track Order</button>
            <button class="btn btn-secondary view-details-btn">View Details</button>
          
          </div>
        </div><div class="order-card" data-id="ORD-2024-002" data-status="ready">
          <div class="order-header">
            <div class="order-info">
              <h3>Order #ORD-2024-002</h3>
              <div class="order-date">Today, 1:45 PM</div>
            </div>
            <div class="order-status status-ready">Ready</div>
          </div>
          <div class="order-items">
            <h4>Items</h4>
            <p class="item-list-text" title="Chicken Chop Special (x1), Iced Tea (x1)">Chicken Chop Special (x1), Iced Tea (x1)</p>
          </div>
          <div class="order-total">
            <span class="total-label">Total</span>
            <span class="total-amount">RM 20.00</span>
          </div>
          
          <div class="order-actions">
            
            <button class="btn btn-success collect-order-btn">Collect Order</button>
            <button class="btn btn-secondary view-details-btn">View Details</button>
          
          </div>
        </div><div class="order-card" data-id="ORD-2024-003" data-status="completed">
          <div class="order-header">
            <div class="order-info">
              <h3>Order #ORD-2024-003</h3>
              <div class="order-date">Yesterday, 7:20 PM</div>
            </div>
            <div class="order-status status-completed">Completed</div>
          </div>
          <div class="order-items">
            <h4>Items</h4>
            <p class="item-list-text" title="CKT Special (x1), Mango Smoothie (x1)">CKT Special (x1), Mango Smoothie (x1)</p>
          </div>
          <div class="order-total">
            <span class="total-label">Total</span>
            <span class="total-amount">RM 28.00</span>
          </div>
          
          <div class="order-actions">
            
            <button class="btn btn-primary reorder-btn">Reorder</button>
            <button class="btn btn-secondary view-details-btn">View Details</button>
          
          </div>
        </div><div class="order-card" data-id="ORD-2024-006" data-status="completed">
          <div class="order-header">
            <div class="order-info">
              <h3>Order #ORD-2024-006</h3>
              <div class="order-date">Today, 3:40 PM</div>
            </div>
            <div class="order-status status-completed">Completed</div>
          </div>
          <div class="order-items">
            <h4>Items</h4>
            <p class="item-list-text" title="Spaghetti Carbonara (x1)">Spaghetti Carbonara (x1)</p>
          </div>
          <div class="order-total">
            <span class="total-label">Total</span>
            <span class="total-amount">RM 22.00</span>
          </div>
          
          <div class="order-actions">
            
            <button class="btn btn-primary reorder-btn">Reorder</button>
            <button class="btn btn-secondary view-details-btn">View Details</button>
          
          </div>
        </div><div class="booking-card" data-id="BK-2024-001" data-status="confirmed">
          <div class="booking-header">
            <div class="booking-info">
              <h3>Table Reservation #BK-2024-001</h3>
              <div class="order-date">Tomorrow, 7:00 PM</div>
            </div>
            <div class="order-status status-confirmed">Confirmed</div>
          </div>
          <div class="booking-details">
            
          <div class="booking-detail">
            <span class="booking-detail-icon">👥</span>
            <span class="booking-detail-text">4 People</span>
          </div>
        
          <div class="booking-detail">
            <span class="booking-detail-icon">🪑</span>
            <span class="booking-detail-text">Table 12 (Window)</span>
          </div>
        
          <div class="booking-detail">
            <span class="booking-detail-icon">📝</span>
            <span class="booking-detail-text">Special occasion: Anniversary dinner</span>
          </div>
        
          </div>
          <div class="order-actions">
            
            <button class="btn btn-primary modify-booking-btn">Modify Booking</button>
            <button class="btn btn-danger cancel-booking-btn">Cancel Booking</button>
          
          </div>
        </div><div class="booking-card" data-id="BK-2024-002" data-status="pending">
          <div class="booking-header">
            <div class="booking-info">
              <h3>Table Reservation #BK-2024-002</h3>
              <div class="order-date">Next Tuesday, 8:00 PM</div>
            </div>
            <div class="order-status status-pending">Pending</div>
          </div>
          <div class="booking-details">
            
          <div class="booking-detail">
            <span class="booking-detail-icon">👥</span>
            <span class="booking-detail-text">2 People</span>
          </div>
        
          <div class="booking-detail">
            <span class="booking-detail-icon">🪑</span>
            <span class="booking-detail-text">Table 05</span>
          </div>
        
          <div class="booking-detail">
            <span class="booking-detail-icon">📝</span>
            <span class="booking-detail-text">Window seat requested</span>
          </div>
        
          </div>
          <div class="order-actions">
            
            <button class="btn btn-danger cancel-booking-btn">Cancel Booking</button>
          
          </div>
        </div><div class="order-card" data-id="ORD-2024-005" data-status="completed">
          <div class="order-header">
            <div class="order-info">
              <h3>Order #ORD-2024-005</h3>
              <div class="order-date">2 days ago, 6:30 PM</div>
            </div>
            <div class="order-status status-completed">Completed</div>
          </div>
          <div class="order-items">
            <h4>Items</h4>
            <p class="item-list-text" title="Lamb Chop Special (x1), Hot Chocolate (x1)">Lamb Chop Special (x1), Hot Chocolate (x1)</p>
          </div>
          <div class="order-total">
            <span class="total-label">Total</span>
            <span class="total-amount">RM 56.00</span>
          </div>
          
          <div class="order-actions">
            
            <button class="btn btn-primary reorder-btn">Reorder</button>
            <button class="btn btn-secondary view-details-btn">View Details</button>
          
          </div>
        </div></div>
    </div>


  <!-- Order Details Modal -->
  <div class="popup" id="orderDetailsModal">
    <div class="cart-sidebar" id="orderDetailsSidebar">
      <div class="cart-header-section">
        <div class="cart-header">
          <div class="cart-title-section">
            <span class="cart-title-icon">📦</span>
            <span class="cart-title" id="orderDetailsTitle">Order Details</span>
          </div>
          <button class="close-cart-btn" id="closeOrderDetailsBtn">✕</button>
        </div>
      </div>
      <div class="cart-content" id="orderDetailsContent">
        <!-- Details will be dynamically loaded here -->
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/order.js') }}"></script>
@endsection