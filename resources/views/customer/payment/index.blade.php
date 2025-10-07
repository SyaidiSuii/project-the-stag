<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - The Stag - SmartDine</title>
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
    <a href="{{ route('customer.menu.index') }}" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Menu
    </a>
  </header>
  
  <!-- Progress Bar -->
  <div class="progress-container">
    <div class="progress-bar">
      <div class="progress-step completed">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <div class="step-label">Cart</div>
      </div>
      <div class="progress-step active">
        <div class="step-circle">2</div>
        <div class="step-label">Payment</div>
      </div>
      <div class="progress-step">
        <div class="step-circle">3</div>
        <div class="step-label">Confirmation</div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <main class="checkout-container">
    <!-- Order Summary -->
    <section class="order-summary">
      <h2 class="summary-title">
        <i class="fas fa-receipt"></i> Order Summary
      </h2>
      <div class="order-items" id="order-items">
        <!-- Dynamically populated from cart -->
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

      <div class="order-totals">
        <div class="total-line">
          <span class="total-label">Subtotal</span>
          <span class="total-amount" id="subtotal">RM 0.00</span>
        </div>
        <div class="total-line" id="discount-line" style="display: none;">
          <span class="total-label">Discount</span>
          <span class="total-amount" id="discount-amount" style="color: var(--success);">- RM 0.00</span>
        </div>
        
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
          <div class="payment-method selected" data-method="online">
            <div class="method-icon">
              <i class="fas fa-globe"></i>
            </div>
            <div class="method-name">Pay Online</div>
            <div class="method-desc" style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">Online Banking / E-Wallet</div>
          </div>
          <div class="payment-method" data-method="counter">
            <div class="method-icon">
              <i class="fas fa-store"></i>
            </div>
            <div class="method-name">Pay at Counter</div>
            <div class="method-desc" style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">Cash / Card</div>
          </div>
        </div>
      </div>

      <!-- Payment Form -->
      <form id="payment-form">
        <!-- Online Payment Section (default visible) -->
        <div class="form-section" id="online-payment-section">
          <h3 class="section-title">
            <i class="fas fa-lock"></i> Secure Online Payment
          </h3>
          <div class="banking-info-card">
            <div class="banking-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div class="banking-details">
              <h4>Pay Securely Online</h4>
              <p>You will be redirected to ToyyibPay's secure payment page where you can choose:</p>
              <ul class="banking-features">
                <li><i class="fas fa-check"></i> <strong>Online Banking</strong> - All major Malaysian banks</li>
                <li><i class="fas fa-check"></i> <strong>E-Wallet</strong> - Touch 'n Go, GrabPay, Boost, etc.</li>
                <li><i class="fas fa-check"></i> Instant payment confirmation</li>
                <li><i class="fas fa-check"></i> 100% secure and encrypted</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Counter Payment Section (hidden by default) -->
        <div class="form-section" id="counter-payment-section" style="display: none;">
          <h3 class="section-title">
            <i class="fas fa-store"></i> Pay at Counter
          </h3>
          <div class="banking-info-card" style="background: #f0fdf4; border: 2px solid #22c55e;">
            <div class="banking-icon" style="background: #22c55e;">
              <i class="fas fa-info-circle"></i>
            </div>
            <div class="banking-details">
              <h4>Order will be prepared upon payment</h4>
              <p><strong>How it works:</strong></p>
              <ul class="banking-features">
                <li><i class="fas fa-check"></i> Place your order now</li>
                <li><i class="fas fa-check"></i> Go to the counter and make payment (Cash/Card)</li>
                <li><i class="fas fa-check"></i> Your order will start preparing after payment</li>
                <li><i class="fas fa-check"></i> Collect your order when ready</li>
              </ul>
              <p style="margin-top: 1rem; padding: 0.75rem; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0.5rem;">
                <strong>Note:</strong> Your order status will be "Pending Payment" until you pay at the counter.
              </p>
            </div>
          </div>
        </div>

        <!-- Billing Address -->
        <div class="form-section">
          <h3 class="section-title">
            <i class="fas fa-map-marker-alt"></i> Billing Address
          </h3>
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" class="form-input" placeholder="your.email@example.com" value="{{ auth()->user()->email ?? '' }}" {{ auth()->check() ? 'readonly' : '' }}>
            <div class="error-message">Please enter a valid email address</div>
          </div>
          <div class="form-group">
            <label for="address" class="form-label">Street Address</label>
            <input type="text" id="address" class="form-input" placeholder="123 Main Street">
            <div class="error-message">Please enter your address</div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="city" class="form-label">City</label>
              <input type="text" id="city" class="form-input" placeholder="Kuala Lumpur">
              <div class="error-message">Please enter your city</div>
            </div>
            <div class="form-group third-width">
              <label for="postcode" class="form-label">Postcode</label>
              <input type="text" id="postcode" class="form-input" placeholder="50000">
              <div class="error-message">Please enter a valid postcode</div>
            </div>
            <div class="form-group">
              <label for="state" class="form-label">State</label>
              <select id="state" class="form-input">
                <option value="">Select State</option>
                <option value="johor">Johor</option>
                <option value="kedah">Kedah</option>
                <option value="kelantan">Kelantan</option>
                <option value="kl">Kuala Lumpur</option>
                <option value="melaka">Melaka</option>
                <option value="ns">Negeri Sembilan</option>
                <option value="pahang">Pahang</option>
                <option value="penang">Penang</option>
                <option value="perak">Perak</option>
                <option value="perlis">Perlis</option>
                <option value="sabah">Sabah</option>
                <option value="sarawak">Sarawak</option>
                <option value="selangor">Selangor</option>
                <option value="terengganu">Terengganu</option>
              </select>
              <div class="error-message">Please select your state</div>
            </div>
          </div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms-group">
          <input type="checkbox" id="terms" class="terms-checkbox">
          <label for="terms" class="terms-text">
            I agree to the <a href="#" class="terms-link">Terms and Conditions</a> and 
            <a href="#" class="terms-link">Privacy Policy</a>
          </label>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" id="submit-payment" class="submit-btn">
          <span id="submit-text">Pay Now</span>
          <span id="loading-spinner" class="loading-spinner" style="display: none;"></span>
        </button>
      </form>
    </section>
  </main>
  
  <!-- Success Modal -->
  <div class="modal-overlay" id="success-modal">
    <div class="modal-content">
      <div class="modal-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h2 class="modal-title">Payment Successful!</h2>
      <p class="modal-message">Thank you for your order. Your food is being prepared and will be ready soon.</p>
      <p class="modal-message">Order ID: <strong id="order-id">STG-20250827-1234</strong></p>
      <div class="modal-actions">
        <button class="modal-btn modal-btn-primary" id="view-order-btn">View Order Status</button>
        <button class="modal-btn modal-btn-secondary" id="new-order-btn">Place New Order</button>
      </div>
    </div>
  </div>
</body>
    <!-- Toast Notification -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <!-- Cart Manager -->
    <script src="{{ asset('js/customer/cart-manager.js') }}"></script>
    <script src="{{ asset('js/customer/payment.js') }}"></script>
</html>
