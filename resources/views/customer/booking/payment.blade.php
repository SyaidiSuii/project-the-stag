<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Payment - The Stag - SmartDine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/customer/payment.css') }}">
</head>
<body>
  <!-- Header -->
  <header class="checkout-header">
    <div class="logo-container">
      <div class="logo">🦌</div>
      <h1 class="header-title">The Stag - SmartDine</h1>
    </div>
    <a href="{{ route('customer.orders.index') }}" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
  </header>
  
  <!-- Progress Bar -->
  <div class="progress-container">
    <div class="progress-bar">
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Booking</div>
      </div>
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Order</div>
      </div>
      <div class="progress-step active">
        <div class="step-circle">3</div>
        <div class="step-label">Payment</div>
      </div>
      <div class="progress-step">
        <div class="step-circle">4</div>
        <div class="step-label">Confirmation</div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <main class="checkout-container">
    <!-- Order Summary -->
    <section class="order-summary">
      <h2 class="summary-title">
        <i class="fas fa-calendar-check"></i> Booking Summary
      </h2>
      
      <!-- Reservation Details -->
      <div class="order-item" style="background: linear-gradient(135deg, var(--brand), var(--brand-dark)); color: white; margin-bottom: 1rem; border-radius: var(--radius);">
        <div class="item-details" style="margin-left: 1rem;">
          <h4 style="color: white; margin-bottom: 0.5rem; margin-left: 0.5rem;">Table Reservation</h4>
          @if($orderData['reservation_code'])
            <p style="color: rgba(255,255,255,0.9); margin: 0.25rem 0 0.25rem 0.5rem; font-size: 0.9rem;"><strong>Reservation:</strong> {{ $orderData['reservation_code'] }}</p>
          @endif
          <p style="color: rgba(255,255,255,0.9); margin: 0.25rem 0 0.25rem 0.5rem; font-size: 0.9rem;"><strong>Table:</strong> {{ $orderData['table_number'] }}</p>
          @if($orderData['is_vvip'])
            <p style="color: #ffd700; margin: 0.25rem 0 0.25rem 0.5rem; font-size: 0.9rem;"><i class="fas fa-crown"></i> <strong>VVIP Table - Premium Experience</strong></p>
          @endif
        </div>
      </div>

      <!-- Order Items -->
      <h2 class="summary-title">
        <i class="fas fa-receipt"></i> Order Summary
      </h2>
      <div class="order-items" id="order-items">
        @if(!empty($orderData['items']))
          @foreach($orderData['items'] as $item)
            <div class="order-item">
              @if($item['image'])
                <div class="item-image" style="width: 50px; height: 50px; flex-shrink: 0;">
                  <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                </div>
              @else
                <div class="item-image placeholder" style="width: 50px; height: 50px; flex-shrink: 0;">
                  <i class="fas fa-utensils"></i>
                </div>
              @endif
              <div class="item-details">
                <h4 class="item-name">{{ $item['name'] }}</h4>
                @if($item['notes'])
                  <p class="item-notes">{{ $item['notes'] }}</p>
                @endif
                <div class="item-price-qty">
                  <span class="item-price">RM {{ number_format($item['price'], 2) }}</span>
                  <span class="item-quantity">x{{ $item['quantity'] }}</span>
                </div>
              </div>
              <div class="item-total">
                RM {{ number_format($item['total'], 2) }}
              </div>
            </div>
          @endforeach
        @else
          <div style="text-align: center; padding: 2rem; color: var(--text-light);">
            <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="fas fa-calendar-check"></i></div>
            <p>Table reservation only - no food items ordered.</p>
          </div>
        @endif
      </div>

      <div class="voucher-section" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--muted);">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="voucher-select" class="form-label" style="font-size: 1.125rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
              <i class="fas fa-ticket-alt" style="color: var(--brand);"></i>
              <span>Voucher / Promo Code</span>
            </label>
            <select id="voucher-select" class="form-input">
                <option value="">-- Select an available voucher --</option>
                <!-- Vouchers will be populated here by JS -->
            </select>
        </div>
        <div id="voucher-status" style="margin-top: 0.75rem; font-size: 0.9rem;"></div>
      </div>

      <!-- Order Totals -->
      <div class="order-totals">
        @if($orderData['is_vvip'])
          <div class="total-line">
            <span class="total-label">Food & Beverages</span>
            <span class="total-amount">RM {{ number_format($orderData['total_amount'] - 50, 2) }}</span>
          </div>
          <div class="total-line" style="color: #ffd700;">
            <span class="total-label"><i class="fas fa-crown"></i> VVIP Booking Fee</span>
            <span class="total-amount">RM 50.00</span>
          </div>
        @endif
        <div class="total-line grand-total">
          <span>Total</span>
          <span>RM {{ number_format($orderData['total_amount'], 2) }}</span>
        </div>
      </div>
      
      <div class="security-badge">
        <i class="fas fa-shield-alt"></i>
        <span>Secure Payment</span>
      </div>
    </section>

    <!-- Payment Form -->
    <section class="payment-form-container">
      <h2 class="form-title">Payment Details</h2>
      
      <!-- Payment Method Selection -->
      <div class="form-section">
        <h3 class="section-title">
          <i class="fas fa-credit-card"></i> Payment Method
        </h3>
        <div class="payment-methods">
          <div class="payment-method selected" data-method="card">
            <div class="method-icon">
              <i class="fas fa-credit-card"></i>
            </div>
            <div class="method-name">Credit/Debit Card</div>
          </div>
          <div class="payment-method" data-method="wallet">
            <div class="method-icon">
              <i class="fas fa-wallet"></i>
            </div>
            <div class="method-name">E-Wallet</div>
          </div>
          <div class="payment-method" data-method="cash">
            <div class="method-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="method-name">Pay at Restaurant</div>
          </div>
        </div>
      </div>
      
      <!-- Card Payment Form -->
      <form id="payment-form">
        <div class="form-section" id="card-payment-section">
          <h3 class="section-title">
            <i class="fas fa-lock"></i> Card Information
          </h3>
          <div class="form-group">
            <label for="card-number" class="form-label">Card Number</label>
            <div class="card-input-group">
              <input type="text" id="card-number" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19">
              <div class="card-icons">
                <i class="fab fa-cc-visa card-icon visa"></i>
                <i class="fab fa-cc-mastercard card-icon mastercard"></i>
                <i class="fab fa-cc-amex card-icon amex"></i>
              </div>
            </div>
            <div class="error-message">Please enter a valid card number</div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="card-name" class="form-label">Name on Card</label>
              <input type="text" id="card-name" class="form-input" placeholder="John Doe">
              <div class="error-message">Please enter the name on your card</div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group half-width">
              <label for="expiry-date" class="form-label">Expiry Date</label>
              <input type="text" id="expiry-date" class="form-input" placeholder="MM/YY" maxlength="5">
              <div class="error-message">Please enter a valid expiry date</div>
            </div>
            <div class="form-group half-width">
              <label for="cvv" class="form-label">CVV</label>
              <input type="text" id="cvv" class="form-input" placeholder="123" maxlength="4">
              <div class="error-message">Please enter a valid CVV</div>
            </div>
          </div>
        </div>

        <!-- E-Wallet Payment Section -->
        <div class="form-section" id="wallet-payment-section" style="display: none;">
          <h3 class="section-title">
            <i class="fas fa-mobile-alt"></i> E-Wallet Payment
          </h3>
          <div class="form-group">
            <label for="wallet-type" class="form-label">Select E-Wallet</label>
            <select id="wallet-type" class="form-input">
              <option value="">Select your e-wallet</option>
              <option value="touchngo">Touch 'n Go eWallet</option>
              <option value="grabpay">GrabPay</option>
              <option value="boost">Boost</option>
              <option value="maybank">Maybank QRPay</option>
            </select>
            <div class="error-message">Please select an e-wallet</div>
          </div>
          <div class="form-group">
            <label for="phone-number" class="form-label">Phone Number</label>
            <input type="tel" id="phone-number" class="form-input" placeholder="012-345 6789">
            <div class="error-message">Please enter a valid phone number</div>
          </div>
        </div>

        <!-- Email Receipt -->
        <div class="form-section" id="receipt-section">
          <h3 class="section-title">
            <i class="fas fa-envelope"></i> Receipt Options
          </h3>
          <div class="form-group">
            <label for="receipt-email" class="form-label">Email Receipt (Optional)</label>
            <input type="email" id="receipt-email" class="form-input" placeholder="your@email.com" value="{{ auth()->user()->email ?? '' }}">
          </div>
        </div>

        <!-- Submit Button -->
        <div class="form-section">
          <button type="submit" class="submit-btn" id="submit-payment">
            <i class="fas fa-lock"></i>
            <span>Pay RM {{ number_format($orderData['total_amount'], 2) }}</span>
          </button>
          <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Your payment information is secure and encrypted</span>
          </div>
        </div>
      </form>
    </section>
  </main>

  <!-- Success Modal -->
  <div id="success-modal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h2>Payment Successful!</h2>
      <p>Your booking payment has been processed successfully.</p>
      <div id="success-details"></div>
      <button class="btn btn-primary" id="success-ok-btn">
        Continue to Orders
      </button>
    </div>
  </div>

  <script src="{{ asset('js/customer/booking-payment.js') }}"></script>
  <script>
    // Initialize payment form with order data
    window.bookingOrderData = @json($orderData);
    console.log('Booking Order Data:', window.bookingOrderData);
  </script>
</body>
</html>