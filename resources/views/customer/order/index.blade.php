@extends('layouts.customer')

@section('title', 'Orders - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
@endsection

@section('content')
<div class="main-content">
    @guest
    <!-- Guest Message -->
    <div style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 2rem; border-radius: 20px; margin: 2rem; text-align: center;">
        <h2 style="margin-bottom: 1rem;">📦 My Orders</h2>
        <p style="margin-bottom: 1.5rem; font-size: 1.1rem;">Please login to view your order history, track ongoing orders, and manage your bookings.</p>
        <a href="{{ route('login') }}" style="background: white; color: #6366f1; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
            <i class="fas fa-sign-in-alt"></i> Login to View Orders
        </a>
    </div>
    @else
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
      <div class="orders-grid" id="ordersGrid">
        @forelse($orders as $order)
        <div class="order-card" data-id="{{ $order->confirmation_code ?? 'ORD-' . $order->id }}" data-status="{{ $order->order_status }}">
          <div class="order-header">
            <div class="order-info">
              <h3>Order #{{ $order->confirmation_code ?? 'ORD-' . $order->id }}</h3>
              <div class="order-date">{{ $order->created_at->format('M j, g:i A') }}</div>
            </div>
            <div class="order-statuses">
              <div class="payment-status status-payment-{{ $order->payment_status }}">
                <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : 'clock' }}"></i>
                {{ ucfirst($order->payment_status) }}
              </div>
              <div class="order-status status-{{ $order->order_status }}">{{ ucfirst($order->order_status) }}</div>
            </div>
          </div>
          <div class="order-items">
            <h4>Items</h4>
            @php
              $itemList = $order->items->map(function($item) {
                $name = $item->menuItem->name ?? 'Unknown Item';
                return "{$name} (x{$item->quantity})";
              })->implode(', ');
              $truncatedList = Str::limit($itemList, 50, '...');
            @endphp
            <p class="item-list-text" title="{{ $itemList }}">{{ $truncatedList }}</p>
          </div>
          <div class="order-total">
            <span class="total-label">Total</span>
            <span class="total-amount">RM {{ number_format($order->total_amount, 2) }}</span>
          </div>
          
          <div class="order-actions">
            @if($order->order_status === 'pending')
              <button class="btn btn-danger cancel-booking-btn" data-order-id="{{ $order->id }}">Cancel Order</button>
            @elseif($order->order_status === 'preparing')
              <button class="btn btn-primary track-order-btn" data-order-id="{{ $order->id }}">Track Order</button>
            @elseif($order->order_status === 'ready')
              @if($order->order_type === 'dine_in')
                <button class="btn btn-info waiting-serve-btn" data-order-id="{{ $order->id }}" disabled>Waiting to be Served</button>
              @else
                <button class="btn btn-success collect-order-btn" data-order-id="{{ $order->id }}">Collect Order</button>
              @endif
            @elseif($order->order_status === 'served')
              <button class="btn btn-success complete-order-btn" data-order-id="{{ $order->id }}">Mark as Completed</button>
            @elseif($order->order_status === 'completed' || $order->order_status === 'served')
              <button class="btn btn-primary reorder-btn" data-order-id="{{ $order->id }}">Reorder</button>
              @auth
                @php
                  $hasReviews = $order->reviews()->exists();
                @endphp
                @if(false && !$hasReviews)
                  <a href="{{ route('customer.orders.show', $order->id) }}#review-section" class="btn btn-success" style="text-decoration: none; display: none;">
                    <i class="fas fa-star"></i> Rate Order
                  </a>
                @elseif(false && $hasReviews)
                  <span class="btn btn-secondary" style="cursor: default; opacity: 0.7; display: none;" title="You've already reviewed this order">
                    <i class="fas fa-check-circle"></i> Reviewed
                  </span>
                @endif
              @endauth
            @elseif($order->order_status === 'cancelled')
              <button class="btn btn-primary reorder-btn" data-order-id="{{ $order->id }}">Reorder</button>
            @endif
            @if($order->payment_status === 'unpaid' && $order->order_status !== 'cancelled' && $order->payment_method === 'online')
              <button class="btn btn-primary pay-now-btn" data-order-id="{{ $order->id }}">Pay Now</button>
            @endif
            @if($order->order_status !== 'cancelled')
              <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-secondary view-details-btn" style="text-decoration: none;">View Details</a>
            @endif
          </div>
        </div>
        @empty
        <div class="no-orders-message">
          <div class="empty-state-icon">🍽️</div>
          <h3 class="empty-state-title">No Orders Yet</h3>
          <p class="empty-state-description">Start your culinary journey with us today!</p>
          <a href="{{ route('customer.menu.index') }}" class="btn btn-primary empty-state-cta">
            <span class="btn-icon">📋</span>
            Browse Menu
          </a>
        </div>
        @endforelse

        @forelse($reservations as $reservation)
        <div class="booking-card" data-id="{{ $reservation->confirmation_code }}" data-status="{{ $reservation->status }}">
          <div class="booking-header">
            <div class="booking-info">
              <h3>Table Reservation #{{ $reservation->confirmation_code }}</h3>
              <div class="order-date">{{ $reservation->booking_date->format('M j') }}, {{ $reservation->booking_time->format('g:i A') }}</div>
            </div>
            <div class="order-status status-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</div>
          </div>
          <div class="booking-details">
            <div class="booking-detail">
              <span class="booking-detail-icon">👥</span>
              <span class="booking-detail-text">{{ $reservation->party_size }} People</span>
            </div>
            
            @if($reservation->table)
            <div class="booking-detail">
              <span class="booking-detail-icon">🪑</span>
              <span class="booking-detail-text">Table {{ $reservation->table->table_number }} 
                @if($reservation->table->location)
                  ({{ $reservation->table->location }})
                @endif
              </span>
            </div>
            @endif
            
            @if($reservation->special_requests)
            <div class="booking-detail">
              <span class="booking-detail-icon">📝</span>
              <span class="booking-detail-text">{{ Str::limit($reservation->special_requests, 50) }}</span>
            </div>
            @endif
          </div>
          <div class="order-actions">
            @if($reservation->status === 'pending' || $reservation->status === 'confirmed')
              @if($reservation->status === 'confirmed')
                <button class="btn btn-primary modify-booking-btn" data-reservation-id="{{ $reservation->id }}">Modify Booking</button>
              @endif
              <button class="btn btn-danger cancel-booking-btn" data-reservation-id="{{ $reservation->id }}">Cancel Booking</button>
            @endif
          </div>
        </div>
        @empty
        @if($orders->isEmpty())
        <div class="no-bookings-message">
          <div class="empty-state-icon">🪑</div>
          <h3 class="empty-state-title">No Reservations Yet</h3>
          <p class="empty-state-description">Reserve your perfect table for a memorable dining experience</p>
          <a href="{{ route('customer.booking.index') }}" class="btn btn-success empty-state-cta">
            <span class="btn-icon">📅</span>
            Book a Table
          </a>
        </div>
        @endif
        @endforelse
      </div>
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
        <!-- Content will be dynamically populated -->
      </div>
    </div>
  </div>

  <!-- Track Order Modal -->
  <div class="popup" id="trackOrderModal">
    <div class="cart-sidebar" id="trackOrderSidebar">
      <div class="cart-header-section">
        <div class="cart-header">
          <div class="cart-title-section">
            <span class="cart-title-icon">🚚</span>
            <span class="cart-title" id="trackOrderTitle">Track Order</span>
          </div>
          <button class="close-cart-btn" id="closeTrackOrderBtn">✕</button>
        </div>
      </div>
      <div class="cart-content" id="trackOrderContent">
        <!-- Content will be dynamically populated -->
      </div>
    </div>
  </div>

  <!-- Reorder Modal -->
  <div class="popup" id="reorderModal">
    <div class="cart-sidebar" id="reorderSidebar">
      <div class="cart-header-section">
        <div class="cart-header">
          <div class="cart-title-section">
            <span class="cart-title-icon">🔄</span>
            <span class="cart-title" id="reorderTitle">Reorder</span>
          </div>
          <button class="close-cart-btn" id="closeReorderBtn">✕</button>
        </div>
      </div>
      <div class="cart-content" id="reorderContent">
        <!-- Content will be dynamically populated -->
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/order.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const orderDetailsModal = document.getElementById('orderDetailsModal');
    const trackOrderModal = document.getElementById('trackOrderModal');
    const orderDetailsContent = document.getElementById('orderDetailsContent');
    const trackOrderContent = document.getElementById('trackOrderContent');
    const orderDetailsTitle = document.getElementById('orderDetailsTitle');
    const trackOrderTitle = document.getElementById('trackOrderTitle');
    
    // Close buttons
    const closeOrderDetailsBtn = document.getElementById('closeOrderDetailsBtn');
    const closeTrackOrderBtn = document.getElementById('closeTrackOrderBtn');
    
    // View Details Button - uses link directly, no JS handler needed
    
    // Track Order Button Click Handler
    document.addEventListener('click', function(e) {
        const trackOrderBtn = e.target.closest('.track-order-btn');
        if (trackOrderBtn) {
            e.preventDefault();
            console.log('Track order clicked'); // Debug log
            const orderId = trackOrderBtn.getAttribute('data-order-id');
            console.log('Track Order ID:', orderId); // Debug log
            showTrackOrder(orderId);
        }
    });
    
    // Cancel Order Button Click Handler
    document.addEventListener('click', function(e) {
        const cancelOrderBtn = e.target.closest('.cancel-booking-btn[data-order-id]');
        if (cancelOrderBtn) {
            e.preventDefault();
            console.log('Cancel order clicked'); // Debug log
            const orderId = cancelOrderBtn.getAttribute('data-order-id');
            console.log('Cancel Order ID:', orderId); // Debug log
            cancelOrder(orderId);
        }
    });
    
    // Reorder Button Click Handler
    document.addEventListener('click', function(e) {
        const reorderBtn = e.target.closest('.reorder-btn');
        if (reorderBtn) {
            e.preventDefault();
            console.log('Reorder clicked'); // Debug log
            const orderId = reorderBtn.getAttribute('data-order-id');
            console.log('Reorder Order ID:', orderId); // Debug log
            showReorderModal(orderId);
        }
    });
    
    // Pay Now Button Click Handler
    document.addEventListener('click', function(e) {
        const payNowBtn = e.target.closest('.pay-now-btn');
        if (payNowBtn) {
            e.preventDefault();
            console.log('Pay now clicked'); // Debug log
            const orderId = payNowBtn.getAttribute('data-order-id');
            console.log('Pay Now Order ID:', orderId); // Debug log
            // Redirect to booking payment page
            window.location.href = `/customer/booking/${orderId}/payment`;
        }
    });
    
    // Close Modal Handlers
    closeOrderDetailsBtn.addEventListener('click', function() {
        orderDetailsModal.classList.remove('open');
    });
    
    closeTrackOrderBtn.addEventListener('click', function() {
        trackOrderModal.classList.remove('open');
    });
    
    const closeReorderBtn = document.getElementById('closeReorderBtn');
    closeReorderBtn.addEventListener('click', function() {
        document.getElementById('reorderModal').classList.remove('open');
    });
    
    // Close modal when clicking outside
    orderDetailsModal.addEventListener('click', function(e) {
        if (e.target === orderDetailsModal) {
            orderDetailsModal.classList.remove('open');
        }
    });
    
    trackOrderModal.addEventListener('click', function(e) {
        if (e.target === trackOrderModal) {
            trackOrderModal.classList.remove('open');
        }
    });
    
    // Function to show order details
    function showOrderDetails(orderId) {
        console.log('showOrderDetails called with ID:', orderId);
        
        // Show loading state
        orderDetailsTitle.textContent = 'Loading Order Details...';
        orderDetailsContent.innerHTML = `
            <div class="detail-card">
                <div class="detail-body" style="text-align: center; padding: 2rem;">
                    <div class="loading-container" style="margin: 1rem 0;">
                        <div class="loading-spinner"></div>
                        <p style="margin-top: 1rem;">Loading...</p>
                    </div>
                    <p>Fetching order details from database...</p>
                </div>
            </div>
        `;
        orderDetailsModal.classList.add('open');
        
        // Fetch order details from server
        fetch(`/customer/orders/${orderId}/details`)
            .then(response => response.json())
            .then(data => {
                console.log('Order details response:', data);
                
                if (data.success) {
                    const order = data.order;
                    
                    // Update modal title
                    orderDetailsTitle.textContent = `Order Details: ${order.order_number}`;
                    
                    // Generate items HTML
                    let itemsHTML = '';
                    if (order.items && order.items.length > 0) {
                        itemsHTML = order.items.map(item => `
                            <li class="detail-item">
                                <span class="detail-item-name">${item.name}</span>
                                <span class="detail-item-qty">x${item.quantity}</span>
                                <span class="detail-item-price">${item.formatted_total_price}</span>
                            </li>
                        `).join('');
                    } else {
                        itemsHTML = '<li>No items found</li>';
                    }
                    
                    // Generate order details content
                    const detailsHTML = `
                        <div class="detail-card">
                            <div class="detail-header">
                                <span class="detail-icon">📋</span>
                                <h3>${order.order_number}</h3>
                                <span class="order-status status-${order.order_status}">${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}</span>
                            </div>
                            <div class="detail-body">
                                <div class="detail-section">
                                    <h4>Items Ordered</h4>
                                    <ul class="detail-item-list">
                                        ${itemsHTML}
                                    </ul>
                                </div>
                                <div class="detail-section">
                                    <h4>Summary</h4>
                                    <p><strong>Date:</strong> ${order.order_time}</p>
                                    <p><strong>Status:</strong> ${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}</p>
                                    <p><strong>Payment:</strong> <span class="status-badge payment-${order.payment_status_color}">${order.payment_status_text}</span></p>
                                    <p><strong>Customer:</strong> ${order.customer_name}</p>
                                    ${order.table_number ? `<p><strong>Table:</strong> ${order.table_number}</p>` : ''}
                                    <p><strong>Total Amount:</strong> ${order.formatted_total}</p>
                                </div>
                            </div>
                            <div class="detail-actions">
                                <button class="btn btn-secondary" onclick="closeOrderDetailsModal()">Close</button>
                            </div>
                        </div>
                    `;
                    
                    orderDetailsContent.innerHTML = detailsHTML;
                } else {
                    // Handle error
                    orderDetailsTitle.textContent = 'Error Loading Order';
                    orderDetailsContent.innerHTML = `
                        <div class="detail-card">
                            <div class="detail-body" style="text-align: center; padding: 2rem;">
                                <p style="color: #ef4444;">Failed to load order details. Please try again.</p>
                                <button class="btn btn-secondary" onclick="closeOrderDetailsModal()">Close</button>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching order details:', error);
                orderDetailsTitle.textContent = 'Error Loading Order';
                orderDetailsContent.innerHTML = `
                    <div class="detail-card">
                        <div class="detail-body" style="text-align: center; padding: 2rem;">
                            <p style="color: #ef4444;">Network error. Please check your connection and try again.</p>
                            <button class="btn btn-secondary" onclick="closeOrderDetailsModal()">Close</button>
                        </div>
                    </div>
                `;
            });
    }
    
    // Function to show track order
    function showTrackOrder(orderId) {
        console.log('showTrackOrder called with ID:', orderId);
        
        // Show loading state
        trackOrderTitle.textContent = 'Loading Order Tracking...';
        trackOrderContent.innerHTML = `
            <div class="detail-card">
                <div class="detail-body" style="text-align: center; padding: 2rem;">
                    <div class="loading-container" style="margin: 1rem 0;">
                        <div class="loading-spinner"></div>
                        <p style="margin-top: 1rem;">Loading...</p>
                    </div>
                    <p>Fetching tracking information from database...</p>
                </div>
            </div>
        `;
        trackOrderModal.classList.add('open');
        
        // Fetch order tracking from server
        fetch(`/customer/orders/${orderId}/tracking`)
            .then(response => response.json())
            .then(data => {
                console.log('Order tracking response:', data);
                
                if (data.success) {
                    const order = data.order;
                    const progress = order.progress;
                    
                    // Update modal title
                    trackOrderTitle.textContent = `Track Order: ${order.order_number}`;
                    
                    // Generate items HTML
                    let itemsHTML = '';
                    if (order.items && order.items.length > 0) {
                        itemsHTML = order.items.map(item => `
                            <p>${item.name} (x${item.quantity}) - ${item.formatted_unit_price}</p>
                        `).join('');
                    } else {
                        itemsHTML = '<p>No items found</p>';
                    }
                    
                    // Generate ETA information
                    let etaHTML = '';
                    if (order.eta) {
                        etaHTML = `
                            <p><strong>Kitchen Estimate:</strong> ${order.eta.current_estimate} minutes</p>
                            ${order.eta.is_delayed ? `<p style="color: #f59e0b;"><strong>Delayed by:</strong> ${order.eta.delay_duration} minutes</p>` : ''}
                        `;
                    }
                    
                    // Generate track order content
                    const trackHTML = `
                        <div class="detail-card">
                            <div class="detail-header">
                                <span class="detail-icon">🚚</span>
                                <h3>Order Tracking: ${order.order_number}</h3>
                                <span class="order-status status-${order.order_status}">${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}</span>
                            </div>
                            <div class="detail-body">
                                <div class="detail-section">
                                    <h4>Current Status</h4>
                                    <div class="tracking-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: ${progress.percentage}%"></div>
                                        </div>
                                        <p class="progress-text">${progress.percentage}% Complete</p>
                                    </div>
                                    <p><strong>Status:</strong> ${progress.message}</p>
                                    <p><strong>Estimated:</strong> ${progress.estimated_time}</p>
                                    ${etaHTML}
                                </div>
                                <div class="detail-section">
                                    <h4>Items Ordered</h4>
                                    <div class="detail-items-text">
                                        ${itemsHTML}
                                    </div>
                                </div>
                                <div class="detail-section">
                                    <h4>Order Summary</h4>
                                    <p><strong>Date:</strong> ${order.order_time}</p>
                                    <p><strong>Order Type:</strong> ${order.order_type.replace('_', ' ')}</p>
                                    <p><strong>Total:</strong> ${order.formatted_total}</p>
                                </div>
                            </div>
                            <div class="detail-actions">
                                <button class="btn btn-secondary" onclick="closeTrackOrderModal()">Close</button>
                            </div>
                        </div>
                    `;
                    
                    trackOrderContent.innerHTML = trackHTML;
                } else {
                    // Handle error
                    trackOrderTitle.textContent = 'Error Loading Tracking';
                    trackOrderContent.innerHTML = `
                        <div class="detail-card">
                            <div class="detail-body" style="text-align: center; padding: 2rem;">
                                <p style="color: #ef4444;">Failed to load tracking information. Please try again.</p>
                                <button class="btn btn-secondary" onclick="closeTrackOrderModal()">Close</button>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching order tracking:', error);
                trackOrderTitle.textContent = 'Error Loading Tracking';
                trackOrderContent.innerHTML = `
                    <div class="detail-card">
                        <div class="detail-body" style="text-align: center; padding: 2rem;">
                            <p style="color: #ef4444;">Network error. Please check your connection and try again.</p>
                            <button class="btn btn-secondary" onclick="closeTrackOrderModal()">Close</button>
                        </div>
                    </div>
                `;
            });
    }
    
    // Global functions for closing modals (accessible from onclick)
    window.closeOrderDetailsModal = function() {
        orderDetailsModal.classList.remove('open');
    };
    
    window.closeTrackOrderModal = function() {
        trackOrderModal.classList.remove('open');
    };
    
    // Function to cancel order (expose as window global)
    window.cancelOrder = async function(orderId) {
        console.log('Cancel order clicked for ID:', orderId);
        
        // Find the order card to get confirmation code
        const orderCard = document.querySelector(`.order-card .cancel-booking-btn[data-order-id="${orderId}"]`)?.closest('.order-card');
        const confirmationCode = orderCard ? orderCard.dataset.id : `ORD-${orderId}`;
        
        // Show confirmation modal using the existing system
        const confirmed = await showConfirm(
            'Cancel Order?',
            `Are you sure you want to cancel this order? Order #${confirmationCode} This action cannot be undone.`,
            'danger',
            'Cancel Order',
            'Keep Order'
        );
        
        if (!confirmed) {
            return;
        }
        
        console.log('Cancelling order ID:', orderId);
        
        // Send cancel request to server
        try {
            const response = await fetch(`/customer/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            console.log('Cancel response:', data);
            
            if (data.success) {
                // Show success message with Toast
                Toast.success('Success', 'Order has been cancelled successfully.');
                
                // Reload page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                // Show error message
                Toast.error('Error', data.error || data.message || 'Unable to cancel order. Please try again.');
            }
        } catch (error) {
            console.error('Error cancelling order:', error);
            Toast.error('Error', 'Network error. Please check your connection and try again.');
        }
    };
    
    // Function to show reorder modal
    function showReorderModal(orderId) {
        console.log('showReorderModal called with ID:', orderId);
        
        const reorderModal = document.getElementById('reorderModal');
        const reorderTitle = document.getElementById('reorderTitle');
        const reorderContent = document.getElementById('reorderContent');
        
        console.log('Modal elements found:', {
            modal: !!reorderModal,
            title: !!reorderTitle,
            content: !!reorderContent
        });
        
        if (!reorderModal || !reorderTitle || !reorderContent) {
            console.error('Reorder modal elements not found!');
            return;
        }
        
        // Show loading state
        reorderTitle.textContent = 'Loading Reorder Details...';
        reorderContent.innerHTML = `
            <div class="detail-card">
                <div class="detail-body" style="text-align: center; padding: 2rem;">
                    <div class="loading-container" style="margin: 1rem 0;">
                        <div class="loading-spinner"></div>
                        <p style="margin-top: 1rem;">Loading...</p>
                    </div>
                    <p>Fetching reorder information from database...</p>
                </div>
            </div>
        `;
        reorderModal.classList.add('open');
        
        // Fetch reorder details from server
        fetch(`/customer/orders/${orderId}/reorder`)
            .then(response => response.json())
            .then(data => {
                console.log('Reorder details response:', data);
                
                if (data.success) {
                    const order = data.order;
                    
                    // Update modal title
                    reorderTitle.textContent = `Reorder: ${order.order_number}`;
                    
                    // Generate unavailable items warning
                    let warningHTML = '';
                    if (order.has_unavailable_items) {
                        warningHTML = `
                            <div class="warning-section" style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 1rem; margin-bottom: 1rem;">
                                <h4 style="color: #92400e; margin: 0 0 0.5rem 0;">⚠️ Some items are unavailable</h4>
                                ${order.unavailable_items.map(item => `
                                    <p style="color: #92400e; margin: 0; font-size: 0.875rem;">• ${item.name} (x${item.quantity}) - ${item.reason}</p>
                                `).join('')}
                            </div>
                        `;
                    }
                    
                    // Generate available items HTML
                    let itemsHTML = '';
                    if (order.available_items && order.available_items.length > 0) {
                        itemsHTML = order.available_items.map(item => `
                            <li class="detail-item">
                                <span class="detail-item-name">
                                    ${item.name} 
                                    ${item.price_changed ? '<span style="color: #f59e0b; font-size: 0.75rem;">(Price Updated)</span>' : ''}
                                </span>
                                <span class="detail-item-qty">x${item.quantity}</span>
                                <span class="detail-item-price">${item.formatted_item_total}</span>
                            </li>
                        `).join('');
                    } else {
                        itemsHTML = '<li style="color: #6b7280;">No available items to reorder</li>';
                    }
                    
                    // Generate price comparison
                    let priceComparisonHTML = '';
                    if (order.price_changed) {
                        priceComparisonHTML = `
                            <div style="margin: 1rem 0; padding: 0.75rem; background: #f3f4f6; border-radius: 6px;">
                                <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">
                                    Original Total: <span style="text-decoration: line-through;">${order.formatted_original_total}</span><br>
                                    Current Total: <strong style="color: #059669;">${order.formatted_current_total}</strong>
                                </p>
                            </div>
                        `;
                    }
                    
                    // Generate reorder content
                    const reorderHTML = `
                        <div class="detail-card">
                            <div class="detail-header">
                                <span class="detail-icon">🔄</span>
                                <h3>Reorder: ${order.order_number}</h3>
                            </div>
                            <div class="detail-body">
                                ${warningHTML}
                                
                                ${order.available_items.length > 0 ? `
                                    <div class="detail-section">
                                        <h4>Items to Reorder</h4>
                                        <ul class="detail-item-list">
                                            ${itemsHTML}
                                        </ul>
                                        ${priceComparisonHTML}
                                    </div>
                                ` : ''}
                                
                                <div class="detail-section">
                                    <h4>Total: ${order.formatted_current_total}</h4>
                                    ${order.price_changed ? '<p style="color: #f59e0b; font-size: 0.875rem; margin: 0.5rem 0;">⚠️ Prices may have changed since your last order</p>' : ''}
                                </div>
                            </div>
                            <div class="detail-actions">
                                ${order.available_items.length > 0 ? `
                                    <button class="btn btn-primary" onclick="addToCart(${orderId})">Add to Cart</button>
                                    <button class="btn btn-success" onclick="orderNow(${orderId})">Order Now</button>
                                ` : ''}
                                <button class="btn btn-secondary" onclick="closeReorderModal()">Cancel</button>
                            </div>
                        </div>
                    `;
                    
                    reorderContent.innerHTML = reorderHTML;
                } else {
                    // Handle error
                    reorderTitle.textContent = 'Error Loading Reorder';
                    reorderContent.innerHTML = `
                        <div class="detail-card">
                            <div class="detail-body" style="text-align: center; padding: 2rem;">
                                <p style="color: #ef4444;">Failed to load reorder details. Please try again.</p>
                                <button class="btn btn-secondary" onclick="closeReorderModal()">Close</button>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching reorder details:', error);
                reorderTitle.textContent = 'Error Loading Reorder';
                reorderContent.innerHTML = `
                    <div class="detail-card">
                        <div class="detail-body" style="text-align: center; padding: 2rem;">
                            <p style="color: #ef4444;">Network error. Please check your connection and try again.</p>
                            <button class="btn btn-secondary" onclick="closeReorderModal()">Close</button>
                        </div>
                    </div>
                `;
            });
    }
    
    // Global function to close reorder modal
    window.closeReorderModal = function() {
        document.getElementById('reorderModal').classList.remove('open');
    };
    
    // Function to add items to cart (simple reorder)
    window.addToCart = async function(orderId) {
        console.log('Adding order to cart:', orderId);
        
        try {
            const response = await fetch(`/customer/orders/${orderId}/add-to-cart`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (typeof Toast !== 'undefined') {
                    Toast.success('Added to Cart', result.message);
                } else {
                    alert(result.message);
                }
                closeReorderModal();

                // Redirect to food page to show cart
                setTimeout(() => {
                    window.location.href = '/customer/food?showCart=true';
                }, 500);
            } else {
                if (typeof Toast !== 'undefined') {
                    Toast.error('Failed', result.message || 'Failed to add items to cart');
                } else {
                    alert(result.message || 'Failed to add items to cart');
                }
            }

        } catch (error) {
            console.error('Error adding to cart:', error);
            if (typeof Toast !== 'undefined') {
                Toast.error('Error', 'Error adding items to cart. Please try again.');
            } else {
                alert('Error adding items to cart. Please try again.');
            }
        }
    };
    
    // Function to order now (skip cart review)
    window.orderNow = async function(orderId) {
        console.log('Order now:', orderId);

        // Use modern confirmation modal
        const confirmed = await new Promise((resolve) => {
            if (typeof showConfirm === 'function') {
                showConfirm(
                    'Order Now?',
                    'Order these items immediately? You will skip cart review.',
                    'info',
                    'Order Now',
                    'Cancel'
                ).then(resolve);
            } else {
                // Fallback to native confirm
                resolve(confirm('Order these items immediately? You will skip cart review.'));
            }
        });

        if (!confirmed) {
            return;
        }

        try {
            // Get the reorder data and proceed to checkout
            const response = await fetch(`/customer/orders/${orderId}/reorder`);
            const data = await response.json();
            
            if (data.success && data.order.available_items.length > 0) {
                // Store items for checkout
                const checkoutItems = data.order.available_items.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.formatted_current_price, // Use already formatted price
                    quantity: item.quantity,
                    notes: item.notes || ''
                }));
                
                // Store in sessionStorage for checkout
                sessionStorage.setItem('checkoutCart', JSON.stringify(checkoutItems));
                
                closeReorderModal();
                
                // Redirect to payment page
                window.location.href = '/customer/payment';
            } else {
                if (typeof Toast !== 'undefined') {
                    Toast.warning('No Items', 'No available items to order.');
                } else {
                    alert('No available items to order.');
                }
            }
        } catch (error) {
            console.error('Error processing order:', error);
            if (typeof Toast !== 'undefined') {
                Toast.error('Error', 'Error processing order. Please try again.');
            } else {
                alert('Error processing order. Please try again.');
            }
        }
    };
    
    // Test function to ensure modal works
    window.testModal = function() {
        console.log('Testing modal...');
        orderDetailsContent.innerHTML = `
            <div class="detail-card">
                <div class="detail-header">
                    <span class="detail-icon">📋</span>
                    <h3>Test Order</h3>
                    <span class="order-status status-ready">Test Status</span>
                </div>
                <div class="detail-body">
                    <div class="detail-section">
                        <h4>Test Section</h4>
                        <p>This is a test modal to ensure functionality works.</p>
                    </div>
                </div>
                <div class="detail-actions">
                    <button class="btn btn-secondary" onclick="closeOrderDetailsModal()">Close</button>
                </div>
            </div>
        `;
        orderDetailsModal.classList.add('open');
    };

    // Category Filtering Logic
    const categoryTabs = document.querySelectorAll('.category-tabs .tab');
    const orderCards = document.querySelectorAll('.order-card');
    const bookingCards = document.querySelectorAll('.booking-card');
    const categoryTitle = document.getElementById('categoryTitle');

    // Add event listeners to category tabs
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const selectedCategory = this.dataset.category;
            
            // Update active tab
            categoryTabs.forEach(t => t.removeAttribute('aria-current'));
            this.setAttribute('aria-current', 'page');
            
            // Update category title
            categoryTitle.textContent = selectedCategory === 'All' ? 'All Orders' : 
                                       selectedCategory === 'History' ? 'Order History' : 
                                       selectedCategory;
            
            // Filter orders and bookings
            filterByCategory(selectedCategory);
        });
    });

    function filterByCategory(category) {
        // Define status groups
        const ongoingOrderStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'served'];
        const historyOrderStatuses = ['completed', 'cancelled'];
        const ongoingBookingStatuses = ['pending', 'confirmed', 'seated'];
        const historyBookingStatuses = ['completed', 'cancelled'];

        let visibleCount = 0;

        // Filter order cards
        orderCards.forEach(card => {
            const orderStatus = card.dataset.status;
            let shouldShow = false;

            switch(category) {
                case 'All':
                    shouldShow = true;
                    break;
                case 'Ongoing':
                    shouldShow = ongoingOrderStatuses.includes(orderStatus);
                    break;
                case 'History':
                    shouldShow = historyOrderStatuses.includes(orderStatus);
                    break;
                case 'Booking':
                    shouldShow = false; // Only bookings, no orders
                    break;
            }

            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Filter booking cards
        bookingCards.forEach(card => {
            const bookingStatus = card.dataset.status;
            let shouldShow = false;

            switch(category) {
                case 'All':
                    shouldShow = true;
                    break;
                case 'Ongoing':
                    shouldShow = ongoingBookingStatuses.includes(bookingStatus);
                    break;
                case 'History':
                    shouldShow = historyBookingStatuses.includes(bookingStatus);
                    break;
                case 'Booking':
                    shouldShow = true; // All bookings
                    break;
            }

            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        const noResultsMsg = document.getElementById('noResults');
        if (visibleCount === 0) {
            noResultsMsg.style.display = 'block';
            noResultsMsg.textContent = `No ${category.toLowerCase()} found.`;
        } else {
            noResultsMsg.style.display = 'none';
        }
    }

    // Initialize with 'All' category on page load
    filterByCategory('All');
});
</script>
@endguest
@endsection