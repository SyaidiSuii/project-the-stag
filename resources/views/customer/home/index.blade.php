@extends('layouts.customer')

@section('title', 'Home - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/home.css') }}">
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="hero-overlay">
        <h1 class="hero-title" id="heroTitle">Welcome to <span>The Stag</span></h1>
        <p class="hero-sub" id="heroSubtitle">Experience premium dining with our signature steaks, authentic Malaysian flavors, and exceptional service that creates unforgettable culinary memories</p>
        <div class="hero-cta">
            <a href="{{ route('customer.food.index') }}" class="btn-primary" id="heroBtn1">Explore Menu</a>
            <button class="btn-muted" data-scroll="#about" id="heroBtn2">Learn More</button>
        </div>
    </div>
    <button class="scroll-down" data-scroll="#about" aria-label="Scroll to about section">
        ↓ Discover More
    </button>
</section>

<!-- About Section -->
<section class="section about" id="about">
    <div class="about-wrap">
        <div class="about-text">
            <h2 id="aboutTitle">About The Stag</h2>
            <p class="muted" id="aboutSubtitle">Discover the perfect blend of Western cuisine and Malaysian favorites in the heart of the city.</p>
            <p id="aboutDescription">At The Stag, we pride ourselves on delivering exceptional dining experiences through carefully crafted dishes, premium ingredients, and warm hospitality. From our signature steaks to authentic local delicacies, every meal tells a story.</p>
            <ul class="about-list" id="aboutList">
                <li>🥩 Premium beef steaks aged to perfection</li>
                <li>🍜 Authentic Malaysian dishes with a modern twist</li>
                <li>🍝 Fresh pasta made daily with imported Italian ingredients</li>
                <li>🌟 Award-winning culinary team with years of expertise</li>
            </ul>
            <div class="about-cta">
                <a href="{{ route('customer.food.index') }}" class="btn-primary" id="aboutBtn1">View Full Menu</a>
                <button class="btn-muted" data-scroll="#contact" id="aboutBtn2">Contact Us</button>
            </div>
        </div>
        <div class="about-media"></div>
    </div>
</section>

<!-- Promotion Section -->
<section class="section promotion" id="promotion">
    <h2 id="promotionTitle">Featured Promotions</h2>
    <p class="muted" id="promotionSubtitle">Don't miss out on our limited-time offers and special deals!</p>
    <div class="cards" id="promotionGrid" role="list" aria-label="Featured promotions">
        <!-- Default promotions - will be replaced by dynamic content if available -->
        {{-- @if(isset($promotions) && count($promotions) > 0)
            @foreach($promotions as $index => $promo)
                <article class="card" role="listitem" style="animation-delay: {{ $index * 0.1 }}s">
                    <div class="card-img" style="display:grid;place-items:center;font-size:4rem">{{ $promo['img'] ?? '🍽️' }}</div>
                    <div class="card-body">
                        <div class="card-title">{{ $promo['name'] ?? 'Special Dish' }}</div>
                        <p class="card-description">{{ $promo['description'] ?? 'Delicious meal prepared with care.' }}</p>
                        <div class="price">RM {{ number_format($promo['price'] ?? 25.00, 2) }}</div>
                        <div class="card-actions">
                            <a class="btn-muted" href="{{ $promo['link'] ?? route('customer.food.index') }}">View Details</a>
                            <a class="btn-primary" href="{{ $promo['link'] ?? route('customer.food.index') }}">Order Now</a>
                        </div>
                    </div>
                </article>
            @endforeach
        @else
            <!-- Default promotions when no data is available -->
            <article class="card" role="listitem">
                <div class="card-img" style="display:grid;place-items:center;font-size:4rem">🥩</div>
                <div class="card-body">
                    <div class="card-title">Signature Steak</div>
                    <p class="card-description">Premium beef steak grilled to perfection with our special seasoning.</p>
                    <div class="price">RM 45.00</div>
                    <div class="card-actions">
                        <a class="btn-muted" href="{{ route('customer.food.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.food.index') }}">Order Now</a>
                    </div>
                </div>
            </article>
            <article class="card" role="listitem">
                <div class="card-img" style="display:grid;place-items:center;font-size:4rem">🍜</div>
                <div class="card-body">
                    <div class="card-title">Laksa Special</div>
                    <p class="card-description">Authentic Malaysian laksa with fresh ingredients and rich coconut broth.</p>
                    <div class="price">RM 18.00</div>
                    <div class="card-actions">
                        <a class="btn-muted" href="{{ route('customer.food.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.food.index') }}">Order Now</a>
                    </div>
                </div>
            </article>
            <article class="card" role="listitem">
                <div class="card-img" style="display:grid;place-items:center;font-size:4rem">🍝</div>
                <div class="card-body">
                    <div class="card-title">Pasta Carbonara</div>
                    <p class="card-description">Creamy carbonara pasta with crispy bacon and fresh herbs.</p>
                    <div class="price">RM 22.00</div>
                    <div class="card-actions">
                        <a class="btn-muted" href="{{ route('customer.food.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.food.index') }}">Order Now</a>
                    </div>
                </div>
            </article>
            <article class="card" role="listitem">
                <div class="card-img" style="display:grid;place-items:center;font-size:4rem">🍔</div>
                <div class="card-body">
                    <div class="card-title">Gourmet Burger</div>
                    <p class="card-description">Juicy beef patty with fresh lettuce, tomato, and our special sauce.</p>
                    <div class="price">RM 28.00</div>
                    <div class="card-actions">
                        <a class="btn-muted" href="{{ route('customer.food.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.food.index') }}">Order Now</a>
                    </div>
                </div>
            </article>
            <article class="card" role="listitem">
                <div class="card-img" style="display:grid;place-items:center;font-size:4rem">🥗</div>
                <div class="card-body">
                    <div class="card-title">Garden Salad</div>
                    <p class="card-description">Fresh mixed greens with seasonal vegetables and house dressing.</p>
                    <div class="price">RM 15.00</div>
                    <div class="card-actions">
                        <a class="btn-muted" href="{{ route('customer.food.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.food.index') }}">Order Now</a>
                    </div>
                </div>
            </article>
        @endif --}}
    </div>
    <div class="promotion-cta">
        <a href="{{ route('customer.food.index') }}" class="btn-primary" id="promotionBtn">See Full Menu</a>
    </div>
</section>

<!-- Quick Stats -->
<section class="section stats">
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card">
            <div class="stat-icon">🍽️</div>
            <div class="stat-number">{{ $stats['menu_items'] ?? '100+' }}</div>
            <div class="stat-label">Menu Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-number">{{ $stats['rating'] ?? '4.8' }}</div>
            <div class="stat-label">Customer Rating</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👨‍🍳</div>
            <div class="stat-number">{{ $stats['experience'] ?? '15+' }}</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-number">{{ $stats['customers'] ?? '25k+' }}</div>
            <div class="stat-label">Happy Customers</div>
        </div>
    </div>
</section>

<!-- Contact & Feedback Section -->
<section class="section contact" id="contact">
    <div class="grid-two">
        <div>
            <h2 id="contactTitle">Visit Us Today</h2>
            <p class="muted" id="contactSubtitle">We're located in the heart of the city, ready to serve you exceptional dining experiences.</p>
            <ul class="info-list" id="infoList">
                <li><strong>📍 Address:</strong> {{ $contact['address'] ?? '123 Food Street, City Center, 50200 Kuala Lumpur' }}</li>
                <li><strong>📞 Phone:</strong> {{ $contact['phone'] ?? '+60 12-345-6789' }}</li>
                <li><strong>🕐 Hours:</strong> {{ $contact['hours'] ?? 'Daily 11AM - 11PM' }}</li>
                <li><strong>✉️ Email:</strong> {{ $contact['email'] ?? 'hello@thestag.com' }}</li>
            </ul>
            <div class="map">
                <div class="pin">📍</div>
            </div>
        </div>
        <div class="feedback">
            <h3 id="feedbackTitle">Share Your Feedback</h3>
            <p class="muted" id="feedbackSubtitle">Help us improve by sharing your dining experience with us.</p>
            <form id="feedbackForm" action="{{ route('customer.feedback.store') }}" method="POST">
                @csrf
                <div class="grid-two-narrow">
                    <div class="row">
                        <label for="fbName">Name</label>
                        <input type="text" id="fbName" name="name" placeholder="Your name" required />
                    </div>
                    <div class="row">
                        <label for="fbContact">Contact</label>
                        <input type="text" id="fbContact" name="contact" placeholder="Phone or email" />
                    </div>
                    <div class="row span2">
                        <label for="fbSubject">Subject</label>
                        <input type="text" id="fbSubject" name="subject" placeholder="Feedback subject" />
                    </div>
                    <div class="row span2">
                        <label for="fbMessage">Message</label>
                        <textarea id="fbMessage" name="message" rows="4" placeholder="Tell us about your experience..." required></textarea>
                    </div>
                </div>
                <div class="actions">
                    <button type="button" class="btn-muted" id="btnClear">Clear</button>
                    <button type="submit" class="btn-primary">Submit Feedback</button>
                </div>
            </form>
            <p class="form-note" id="fbNote">
                @if(session('feedback_success'))
                    <span style="color: #16a34a;">{{ session('feedback_success') }}</span>
                @endif
                @if(session('feedback_error'))
                    <span style="color: #dc2626;">{{ session('feedback_error') }}</span>
                @endif
            </p>
        </div>
    </div>
</section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-inner">
                <div>
                    <span>© <span id="year">{{ date('Y') }}</span> The Stag SmartDine. All rights reserved.</span>
                </div>
                <nav class="footer-nav">
                    <a href="#about">About</a>
                    <a href="{{ route('customer.food.index') }}">Menu</a>
                    <a href="#contact">Contact</a>
                    <a href="#">Privacy</a>
                </nav>
            </div>
        </footer>

        
    <!-- Back to Top Button -->
    <button class="to-top" id="toTop" aria-label="Back to top">↑</button>
@endsection

@section('scripts')
<script>
    // Page-specific initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Any homepage-specific JavaScript can go here
        // The main functionality is handled by customer-design.js
        
        // Example: Track page views, analytics, etc.
        console.log('🏠 Homepage loaded successfully');
    });

    // --- Page Functionality ---

function smoothTo(target) {
  const el = typeof target === 'string' ? document.querySelector(target) : target;
  if (!el) return;
  const targetPosition = el.getBoundingClientRect().top + window.scrollY - 80;
  window.scrollTo({ top: targetPosition, behavior: 'smooth' });
}

function initializeNavigation() {
  document.querySelectorAll('[data-scroll]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const target = btn.getAttribute('data-scroll');
      smoothTo(target);
    });
  });
}

function initializeScrollFeatures() {
  const toTopBtn = document.getElementById('toTop');
  if (toTopBtn) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 500) {
        toTopBtn.style.display = 'block';
        toTopBtn.style.opacity = '1';
      } else {
        toTopBtn.style.opacity = '0';
        setTimeout(() => { if (window.scrollY <= 500) toTopBtn.style.display = 'none'; }, 300);
      }
    });
    toTopBtn.addEventListener('click', () => { smoothTo('#hero'); });
  }

  const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.card, .stat-card, .about-text, .feedback').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    observer.observe(el);
  });
}
</script>

@endsection