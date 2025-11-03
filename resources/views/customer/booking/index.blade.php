@extends('layouts.customer')

@section('title', 'Bookings - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/booking.css') }}">
@endsection

@section('content')
@auth
{{-- Main Content --}}
<div class="main-content">
      <!-- Header Section -->
      <div class="header-section">
        <!-- Search Bar -->
        <div class="search-bar-container" role="search">
          <div class="search-bar">
            <span class="search-icon" aria-hidden="true"><i class="fas fa-search"></i></span>
            <input type="text" class="search-input" placeholder="Search tables (e.g., T-05)…" id="searchInput" aria-label="Search tables">
            <button class="clear-btn" id="clearSearch" aria-label="Clear search"><i class="fas fa-times"></i></button>
          </div>
        </div>
        <!-- Category Tabs -->
        <div class="category-tabs" role="tablist" aria-label="Table Status">
          <button class="tab" data-filter="all" aria-current="page" role="tab">All Tables</button>
          <button class="tab" data-filter="available" role="tab">Available</button>
          <button class="tab" data-filter="occupied" role="tab">Occupied</button>
          <button class="tab" data-filter="reserved" role="tab">Reserved</button>
          <button class="tab" data-filter="pending" role="tab">Pending</button>
          <button class="tab" data-filter="maintenance" role="tab">Maintenance</button>
          <button class="tab" data-filter="selected" role="tab">My Selection</button>
        </div>
        <!-- Dynamic Category Title -->
        <h1 class="category-title">Table Booking</h1>
      </div>
      
      <!-- VVIP Special Section -->
      <div class="vvip-section">
        <div class="vvip-badge"><i class="fas fa-crown"></i> PREMIUM EXPERIENCE</div>
        <p class="vvip-description">Our exclusive VVIP Room offers privacy, premium service, and an unforgettable dining experience for up to 12 guests.</p>
        <p class="vvip-description" style="margin-top: 0.5rem; color: #ffd700; font-weight: 600;"><i class="fas fa-tag"></i> Booking Fee: RM 10/hour (1 hour minimum = RM 10)</p>
      </div>
      
      <!-- Floor Layout -->
      <div class="floor-container">
        <div class="floor-legend">
          <div class="legend-item">
            <div class="legend-dot available"></div>
            <span>Available</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot occupied"></div>
            <span>Occupied</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot reserved"></div>
            <span>Reserved</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot pending"></div>
            <span>Pending</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot maintenance"></div>
            <span>Maintenance</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot selected"></div>
            <span>Selected</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot outdoor"></div>
            <span>Outdoor</span>
          </div>
          <div class="legend-item">
            <div class="legend-dot vvip"></div>
            <span>VVIP Room</span>
          </div>
        </div>
        
        <div class="floor-grid" id="floorGrid">
          <div class="floor-grid-inner" style="width: {{ $layoutSetting->container_width }}px; height: {{ $layoutSetting->container_height }}px;">
            @foreach($tables as $table)
              @php
                $coordinates = $table->coordinates ?? [];
                $x = $coordinates['x'] ?? (30 + (($loop->index % 5) * 110));
                $y = $coordinates['y'] ?? (30 + (floor($loop->index / 5) * 100));

                // Determine size class based on capacity
                if ($table->table_type === 'vip') {
                  $sizeClass = 'vvip'; // VVIP always stays same size
                  $colorClass = '';
                } else {
                  // Size: capacity < 5 = small (rectangle), capacity >= 5 = large (square)
                  $sizeClass = $table->capacity < 5 ? 'rectangle' : 'square';

                  // Color: based on table type (outdoor or vip only)
                  $colorClass = $table->table_type === 'outdoor' ? 'outdoor-color' : '';
                }

                $tableClass = trim($sizeClass . ' ' . $colorClass);

                // Disable occupied and maintenance tables
                $isDisabled = in_array($table->status, ['occupied', 'maintenance']);
              @endphp
              <button class="table-tile {{ $tableClass }} {{ $table->status }}"
                      data-id="{{ $table->table_number }}"
                      data-capacity="{{ $table->capacity }}"
                      data-status="{{ $table->status }}"
                      style="position: absolute; left: {{ $x }}px; top: {{ $y }}px;"
                      {{ $isDisabled ? 'disabled' : '' }}
                      aria-label="Table {{ $table->table_number }}, Capacity {{ $table->capacity }}, Status {{ $table->status }}">
                <div class="table-capacity-badge">{{ $table->capacity }}p</div>
                @if($sizeClass === 'vvip')
                  <div class="vvip-badge-label">VVIP</div>
                  <div class="table-id vvip-label">VVIP Room</div>
                @else
                  <div class="table-id">{{ $table->table_number }}</div>
                @endif
                <div class="table-status-badge">{{ strtoupper($table->status) }}</div>
              </button>
            @endforeach
          </div>
        </div>
      </div>
    </div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Floating Booking Button -->
<button class="floating-booking-btn" id="floatingBookingBtn" aria-label="Open booking sidebar" style="position: fixed !important; top: 90px !important; right: 30px !important; z-index: 99999 !important; display: flex !important; align-items: center !important; gap: 8px !important; padding: 12px 20px !important; min-width: 180px !important; justify-content: center !important; font-size: 14px !important; font-weight: 600 !important; border-radius: 12px !important; height: auto !important; width: auto !important;">
  <span id="floatingButtonText">Select Table</span>
</button>

{{-- Booking Sidebar --}}
<div class="booking-sidebar" id="bookingSidebar">
      <div class="booking-header-section">
        <div class="booking-header">
          <div class="booking-title-section">
            <span class="booking-title"><i class="fas fa-calendar-alt"></i> Your Booking</span>
          </div>
        </div>
      </div>
      <div class="booking-content">
        <!-- Booking Card -->
        <div class="booking-card">
          <div class="booking-card-header">
            <div class="booking-card-title">Your Reservation</div>
          </div>
          <div class="booking-card-content">
            <div class="selection-info" id="selectionInfo">
              <div class="selection-table" id="selectedTable">No table selected</div>
              <div class="selection-capacity" id="selectedCapacity">Select a table to begin</div>
            </div>
            
            <div class="datetime-inputs">
              <div class="input-group">
                <label for="bookingDate">Date</label>
                <span class="input-icon" aria-hidden="true"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" id="bookingDate" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" aria-required="true" aria-describedby="bookingDateHelp">
                <span id="bookingDateHelp" class="sr-only">Select your reservation date. Must be today or a future date.</span>
              </div>
              <div class="input-group">
                <label for="bookingTime">Time</label>
                <span class="input-icon" aria-hidden="true"><i class="fas fa-clock"></i></span>
                <select id="bookingTime" aria-required="true" aria-describedby="bookingTimeHelp">
                  <option value="">Select time</option>
                  <option value="11:00">11:00 AM</option>
                  <option value="11:30">11:30 AM</option>
                  <option value="12:00">12:00 PM</option>
                  <option value="12:30">12:30 PM</option>
                  <option value="13:00">1:00 PM</option>
                  <option value="13:30">1:30 PM</option>
                  <option value="14:00">2:00 PM</option>
                  <option value="18:00">6:00 PM</option>
                  <option value="18:30">6:30 PM</option>
                  <option value="19:00">7:00 PM</option>
                  <option value="19:30">7:30 PM</option>
                  <option value="20:00">8:00 PM</option>
                  <option value="20:30">8:30 PM</option>
                  <option value="21:00">9:00 PM</option>
                </select>
                <span id="bookingTimeHelp" class="sr-only">Select your reservation time. Reservations must be made at least 1 hour in advance.</span>
              </div>
              <div class="input-group">
                <label for="guestCount">Party Size</label>
                <span class="input-icon" aria-hidden="true"><i class="fas fa-users"></i></span>
                <select id="guestCount" aria-required="true" aria-describedby="guestCountHelp">
                  <option value="1">1 Guest</option>
                  <option value="2" selected>2 Guests</option>
                  <option value="3">3 Guests</option>
                  <option value="4">4 Guests</option>
                  <option value="5">5 Guests</option>
                  <option value="6">6 Guests</option>
                  <option value="7">7 Guests</option>
                  <option value="8">8 Guests</option>
                  <option value="9">9 Guests</option>
                  <option value="10">10 Guests</option>
                  <option value="11">11 Guests</option>
                  <option value="12">12 Guests</option>
                </select>
                <span id="guestCountHelp" class="sr-only">Select the number of guests for your reservation. Must not exceed table capacity.</span>
              </div>
              <div class="input-group">
                <label for="guestName">Full Name</label>
                <span class="input-icon" aria-hidden="true"><i class="fas fa-user"></i></span>
                <input type="text" id="guestName" placeholder="Enter your full name" value="{{ auth()->user()->name ?? '' }}" required aria-required="true" aria-describedby="guestNameHelp">
                <span id="guestNameHelp" class="sr-only">Enter your full name for the reservation.</span>
              </div>
              <div class="input-group">
                <label for="guestEmail">Email Address</label>
                <span class="input-icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                <input type="email" id="guestEmail" placeholder="Enter your email" value="{{ auth()->user()->email ?? '' }}" required aria-required="true" aria-describedby="guestEmailHelp">
                <span id="guestEmailHelp" class="sr-only">Enter your email address for booking confirmation.</span>
              </div>
              <div class="input-group">
                <label for="guestPhone">Phone Number</label>
                <span class="input-icon" aria-hidden="true"><i class="fas fa-phone"></i></span>
                <input type="tel" id="guestPhone" placeholder="Enter your phone number" value="{{ auth()->user()->phone_number ?? '' }}" required aria-required="true" aria-describedby="guestPhoneHelp">
                <span id="guestPhoneHelp" class="sr-only">Enter your phone number so we can contact you about your reservation.</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Cart Section -->
        <div class="cart-section">
          <div class="cart-header" id="cartHeader">
            <div class="cart-title-section">
              <div class="cart-title"><i class="fas fa-shopping-cart"></i> Your Menu</div>
              <div class="cart-count" id="cartCount">{{ $cartCount }}</div>
            </div>
            <div class="cart-toggle-icon" id="cartToggleIcon"><i class="fas fa-chevron-down"></i></div>
          </div>
          <div class="cart-content open" id="cartContent">
            <p style="padding: 0 20px 10px; font-size: 0.85rem; color: var(--text-2);">Items added from Food &amp; Drinks pages:</p>
            <div id="cartItemsContainer">
              @if($cartItems->count() > 0)
                @foreach($cartItems as $item)
                  <div class="cart-item">
                    <div class="cart-item-details">
                      <div class="cart-item-name">{{ $item->menuItem->name }}</div>
                      <div class="cart-item-price">RM {{ number_format($item->unit_price, 2) }} each</div>
                      @if($item->special_notes)
                        <div class="cart-item-notes">Note: {{ $item->special_notes }}</div>
                      @endif
                    </div>
                    <div class="cart-item-quantity">{{ $item->quantity }}x</div>
                  </div>
                @endforeach
              @else
                <div style="text-align: center; padding: 20px; color: var(--text-2);">
                  <div style="font-size: 2rem; margin-bottom: 10px;"><i class="fas fa-shopping-cart"></i></div>
                  <div>Your pre-order cart is empty.</div>
                  <div>Add items from the <a href="{{ route('customer.menu.index') }}" style="color: var(--brand); text-decoration: none; font-weight: 600;">menu</a>.</div>
                </div>
              @endif
            </div>
            <div class="cart-summary">
              <div class="label">Total Items</div>
              <div class="value" id="cartItems">{{ $cartCount }}</div>
            </div>
            <div class="cart-summary">
              <div class="label">Food &amp; Beverage Total</div>
              <div class="value" id="cartTotal">RM {{ number_format($cartTotal, 2) }}</div>
            </div>
          </div>
        </div>
      <!-- Action Buttons -->
      <div class="action-buttons">
        <button class="btn btn-primary" id="bookWithMenu" disabled="">
          <i class="fas fa-utensils"></i> Book with Menu
        </button>
        <button class="btn btn-secondary" id="bookTableOnly" disabled="">
          <i class="fas fa-calendar-alt"></i> Book Table Only  
        </button>
        <a class="btn btn-accent" href="{{ route('customer.menu.index') }}">
          <i class="fas fa-shopping-cart"></i> View Menu
        </a>
      </div>
    </div>
  <!-- Booking Confirmation Modal -->
  <div id="bookingModal" class="booking-modal" role="dialog" aria-modal="true" aria-labelledby="bookingModalTitle" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header" id="bookingModalTitle">Confirm Your Booking</div>
      
      <div class="booking-summary" id="bookingSummary">
        <div class="summary-row">
          <span>Table:</span>
          <strong id="summaryTable">--</strong>
        </div>
        <div class="summary-row">
          <span>Date:</span>
          <strong id="summaryDate">--</strong>
        </div>
        <div class="summary-row">
          <span>Time:</span>
          <strong id="summaryTime">--</strong>
        </div>
        <div class="summary-row">
          <span>Guests:</span>
          <strong id="summaryGuests">--</strong>
        </div>
        <div class="summary-row">
          <span>Booking Type:</span>
          <strong id="summaryBookingType">--</strong>
        </div>
        <div class="summary-row" id="summaryFoodTotal" style="display: none;">
          <span>Food & Beverages:</span>
          <strong>RM 0.00</strong>
        </div>
        <div class="summary-row" id="summaryBookingFee" style="display: none;">
          <span>VVIP Booking Fee (1hr):</span>
          <strong>RM 0.00</strong>
        </div>
        <div class="summary-row">
          <span>Total:</span>
          <strong id="summaryTotal">RM 0.00</strong>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn btn-secondary" id="cancelBooking">Cancel</button>
        <button class="btn btn-primary" id="confirmBooking">Confirm Booking</button>
      </div>
    </div>
  </div>
</div>
</div>
@endauth

@guest
<!-- Guest User Content -->
<div class="main-content">
  <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 2rem; border-radius: 20px; margin: 2rem; text-align: center;">
    <h2 style="margin-bottom: 1rem;">📅 Table Reservations</h2>
    <p style="margin-bottom: 1.5rem; font-size: 1.1rem;">Please login to reserve your table, view availability in real-time, and enjoy a seamless dining experience at The Stag SmartDine.</p>
    <a href="{{ route('login') }}" style="background: white; color: #1e40af; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
      <i class="fas fa-sign-in-alt"></i> Login to Book Table
    </a>
  </div>
</div>
@endguest

@endsection

@section('scripts')
<script src="{{ asset('js/customer/booking.js') }}?v={{ time() }}"></script>
@endsection