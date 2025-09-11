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
    <a href="{{ route('customer.food.index') }}" class="back-link">
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
            <div class="method-name">Cash on Delivery</div>
          </div>
        </div>
      </div>
      
      <!-- Card Payment Form (default visible) -->
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
        
        <!-- E-Wallet Section (hidden by default) -->
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
    <!-- Cart Manager -->
    <script src="{{ asset('js/customer/cart-manager.js') }}"></script>
    <script src="{{ asset('js/customer/payment.js') }}"></script>
</html>
