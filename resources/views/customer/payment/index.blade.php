<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - The Stag - SmartDine</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/payment.css') }}">
</head>
<body>
  <!-- Header -->
  <header class="checkout-header">
    <div class="logo-container">
      <div class="logo">🦌</div>
      <h1 class="header-title">The Stag - SmartDine</h1>
    </div>
    <a href="{{ route('customer.menu.index') }}" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Menu
    </a>
  </header>

  <!-- Progress Bar -->
  <div class="progress-container">
    <div class="progress-bar">
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Menu</div>
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
          <h4 style="color: white; margin-bottom: 0.5rem; margin-left: 0.5rem;">Food Order</h4>
          <p style="color: rgba(255,255,255,0.9); margin: 0.25rem 0 0.25rem 0.5rem; font-size: 0.9rem;" id="order-type-display"><strong>Order Type:</strong> <span id="order-type-text">-</span></p>
        </div>
      </div>

      <!-- Order Items -->
      <h2 class="summary-title">
        <i class="fas fa-receipt"></i> Order Summary
      </h2>
      <div class="order-items" id="order-items">
        <!-- Items will be populated by JavaScript -->
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
        <div class="total-line grand-total">
          <span>Total</span>
          <span id="grand-total">RM 0.00</span>
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
              <i class="fas fa-university"></i>
            </div>
            <div class="method-name">Online Banking</div>
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
            <i class="fas fa-lock"></i> Online Banking Payment
          </h3>
          <div class="banking-info-card">
            <div class="banking-icon">
              <i class="fas fa-university"></i>
            </div>
            <div class="banking-details">
              <h4>Secure Online Banking Payment</h4>
              <p>You will be redirected to ToyyibPay's secure payment page where you can:</p>
              <ul class="banking-features">
                <li><i class="fas fa-check"></i> Choose from all major Malaysian banks</li>
                <li><i class="fas fa-check"></i> Login securely through your bank's official page</li>
                <li><i class="fas fa-check"></i> Complete payment with your existing banking credentials</li>
                <li><i class="fas fa-check"></i> Get instant payment confirmation</li>
              </ul>
            </div>
          </div>
          <div class="supported-banks">
            <h5>Supported Banks Include:</h5>
            <div class="bank-logos">
              <span class="bank-name">Maybank2u</span>
              <span class="bank-name">CIMB Clicks</span>
              <span class="bank-name">Public Bank</span>
              <span class="bank-name">RHB Now</span>
              <span class="bank-name">Hong Leong</span>
              <span class="bank-name">AmBank</span>
              <span class="bank-name">Bank Islam</span>
              <span class="bank-name">BSN</span>
              <span class="bank-name">And more...</span>
            </div>
          </div>
        </div>

        <!-- Pay at Restaurant Section -->
        <div class="form-section" id="cash-payment-section" style="display: none;">
          <h3 class="section-title">
            <i class="fas fa-money-bill-wave"></i> Pay at Restaurant
          </h3>
          <div class="banking-info-card">
            <div class="banking-icon">
              <i class="fas fa-store"></i>
            </div>
            <div class="banking-details">
              <h4>Complete Payment at Restaurant</h4>
              <p>You can pay for your order when you arrive at the restaurant. Here's what you need to know:</p>
              <ul class="banking-features">
                <li><i class="fas fa-check"></i> Pay with cash or card at the counter</li>
                <li><i class="fas fa-check"></i> Show your order confirmation to our staff</li>
                <li><i class="fas fa-check"></i> Payment must be completed before receiving your order</li>
                <li><i class="fas fa-check"></i> You'll receive a receipt after payment</li>
              </ul>
            </div>
          </div>
          <div class="supported-banks">
            <h5>Accepted Payment Methods at Restaurant:</h5>
            <div class="bank-logos">
              <span class="bank-name">Cash (MYR)</span>
              <span class="bank-name">Credit Card</span>
              <span class="bank-name">Debit Card</span>
              <span class="bank-name">E-Wallet</span>
              <span class="bank-name">Touch 'n Go</span>
            </div>
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
            <span id="submit-text">Pay Now</span>
            <span id="submit-amount" style="display: none;"></span>
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
      <p>Your order has been placed successfully.</p>
      <div id="success-details"></div>
      <button class="modal-btn modal-btn-primary" id="success-ok-btn">
        Continue to Orders
      </button>
    </div>
  </div>

  <script src="{{ asset('js/toast.js') }}"></script>
  <script src="{{ asset('js/customer/payment.js') }}?v={{ time() }}"></script>
</body>
</html>
