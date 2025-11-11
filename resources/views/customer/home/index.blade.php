@extends('layouts.customer')

@section('title', 'Home - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/home.css') }}">
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero" id="hero">
    <div class="hero-overlay">
        <h1 class="hero-title" id="heroTitle">
            @php
                $title = $hero['title'] ?? 'Welcome to {The Stag}';
                // Replace {text} with <span>text</span> for styling
                echo preg_replace('/\{([^}]+)\}/', '<span>$1</span>', $title);
            @endphp
        </h1>
        <p class="hero-sub" id="heroSubtitle">{{ $hero['subtitle'] ?? 'Experience premium dining with our signature steaks, authentic Malaysian flavors, and exceptional service that creates unforgettable culinary memories' }}</p>
        <div class="hero-cta">
            <a href="{{ route('customer.menu.index') }}" class="btn-primary" id="heroBtn1">{{ $hero['btn1_text'] ?? 'Explore Menu' }}</a>
            <button class="btn-muted" data-scroll="#about" id="heroBtn2">{{ $hero['btn2_text'] ?? 'Learn More' }}</button>
        </div>
    </div>
    <button class="scroll-down {{ Auth::check() ? 'logged-in' : '' }}" data-scroll="#about" aria-label="Scroll to about section">
        ↓ Discover More
    </button>
</section>

<!-- About Section -->
<section class="section about" id="about">
    <div class="about-wrap">
        <div class="about-text">
            <h2 id="aboutTitle">{{ $about['title'] ?? 'About The Stag' }}</h2>
            <p class="muted" id="aboutSubtitle">{{ $about['subtitle'] ?? 'Discover the perfect blend of Western cuisine and Malaysian favorites in the heart of the city.' }}</p>
            <p id="aboutDescription">{{ $about['description'] ?? 'At The Stag, we pride ourselves on delivering exceptional dining experiences through carefully crafted dishes, premium ingredients, and warm hospitality. From our signature steaks to authentic local delicacies, every meal tells a story.' }}</p>
            <ul class="about-list" id="aboutList">
                <li>🥩 {{ $about['feature1'] ?? 'Premium beef steaks aged to perfection' }}</li>
                <li>🍜 {{ $about['feature2'] ?? 'Authentic Malaysian dishes with a modern twist' }}</li>
                <li>🍝 {{ $about['feature3'] ?? 'Fresh pasta made daily with imported Italian ingredients' }}</li>
                <li>🌟 {{ $about['feature4'] ?? 'Award-winning culinary team with years of expertise' }}</li>
            </ul>
            <div class="about-cta">
                <a href="{{ route('customer.menu.index') }}" class="btn-primary" id="aboutBtn1">{{ $about['btn1_text'] ?? 'View Full Menu' }}</a>
                <button class="btn-muted" data-scroll="#contact" id="aboutBtn2">{{ $about['btn2_text'] ?? 'Contact Us' }}</button>
            </div>
        </div>
        <div class="about-media"></div>
    </div>
</section>

<!-- AI Recommendations Section -->
<section class="section promotion" id="promotion">
    <h2 id="promotionTitle">{{ $promotionHeader['title'] ?? 'Recommended For You' }}</h2>
    <p class="muted" id="promotionSubtitle">{{ $promotionHeader['subtitle'] ?? 'Dishes specially selected for you' }}</p>
    <div class="cards" id="promotionGrid" role="list" aria-label="Recommended dishes">
        @if(isset($recommendedItems) && count($recommendedItems) > 0)
            @foreach($recommendedItems->take(6) as $index => $item)
                <article class="card" role="listitem" style="animation-delay: {{ $index * 0.1 }}s">
                    @if($item->image)
                        <div class="card-img" style="background-image: url('{{ asset('storage/' . $item->image) }}'); background-size: cover; background-position: center; height: 200px; border-radius: 12px 12px 0 0;"></div>
                    @else
                        <div class="card-img" style="display:grid;place-items:center;font-size:4rem;height:200px">
                            {{ $item->category && strpos(strtolower($item->category->type), 'drink') !== false ? '🍹' : '🍽️' }}
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="card-title">{{ $item->name }}</div>
                        <p class="card-description">
                            @if($item->category)
                                {{ $item->category->name }} • 
                            @endif
                            {{ Str::limit($item->description ?? 'Delicious dish prepared with care', 60) }}
                        </p>
                        <div class="price">RM {{ number_format($item->price, 2) }}</div>
                        <div class="card-actions">
                            <a class="btn-muted" href="{{ route('customer.menu.index') }}">View Menu</a>
                            <button class="btn-primary" onclick="quickAddToCart({{ $item->id }}, {{ json_encode($item->name) }}, {{ $item->price }}, {{ json_encode($item->image ?? '') }})">Add to Cart</button>
                        </div>
                    </div>
                </article>
            @endforeach
        @else
            <!-- Default popular items when no recommendations -->
            <article class="card" role="listitem">
                <div class="card-img" style="display:grid;place-items:center;font-size:4rem">🥩</div>
                <div class="card-body">
                    <div class="card-title">Signature Steak</div>
                    <p class="card-description">Premium beef steak grilled to perfection with our special seasoning.</p>
                    <div class="price">RM 45.00</div>
                    <div class="card-actions">
                        <a class="btn-muted" href="{{ route('customer.menu.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.menu.index') }}">Order Now</a>
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
                        <a class="btn-muted" href="{{ route('customer.menu.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.menu.index') }}">Order Now</a>
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
                        <a class="btn-muted" href="{{ route('customer.menu.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.menu.index') }}">Order Now</a>
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
                        <a class="btn-muted" href="{{ route('customer.menu.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.menu.index') }}">Order Now</a>
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
                        <a class="btn-muted" href="{{ route('customer.menu.index') }}">View Details</a>
                        <a class="btn-primary" href="{{ route('customer.menu.index') }}">Order Now</a>
                    </div>
                </div>
            </article>
        @endif
    </div>
    <div class="promotion-cta">
        <a href="{{ route('customer.menu.index') }}" class="btn-primary" id="promotionBtn">Explore Full Menu</a>
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
            <h2 id="contactTitle">{{ $contact['title'] ?? 'Visit Us Today' }}</h2>
            <p class="muted" id="contactSubtitle">{{ $contact['subtitle'] ?? "We're located in the heart of the city, ready to serve you exceptional dining experiences." }}</p>
            <ul class="info-list" id="infoList">
                <li><strong>📍 Address:</strong> {{ $contact['address'] ?? '123 Food Street, City Center, 50200 Kuala Lumpur' }}</li>
                <li><strong>📞 Phone:</strong> {{ $contact['phone'] ?? '+60 12-345-6789' }}</li>
                <li><strong>🕐 Hours:</strong> {{ $contact['hours'] ?? 'Daily 11AM - 11PM' }}</li>
                <li><strong>✉️ Email:</strong> {{ $contact['email'] ?? 'hello@thestag.com' }}</li>
            </ul>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3968.2701343721774!2d102.2575562!3d5.957472999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31b6bdb86a1f1f1b%3A0x7a5e581c88791a33!2sThe%20Stag%20Coffee%20House!5e0!3m2!1sen!2smy!4v1761011598652!5m2!1sen!2smy"
                    width="100%"
                    height="100%"
                    style="border:0; border-radius: 20px;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        <div class="feedback">
            <h3 id="feedbackTitle">{{ $contact['feedback_title'] ?? 'Share Your Feedback' }}</h3>
            <p class="muted" id="feedbackSubtitle">{{ $contact['feedback_subtitle'] ?? 'Help us improve by sharing your dining experience with us.' }}</p>

            @if($feedbackInfo && !$feedbackInfo['can_submit'])
                <div style="padding: 16px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #92400e; font-weight: 600;">
                        <i class="fas fa-info-circle"></i> You have submitted {{ $feedbackInfo['submitted'] }} feedback(s) for your {{ $feedbackInfo['total_orders'] }} completed order(s).
                    </p>
                    <p style="margin: 8px 0 0 0; color: #92400e; font-size: 14px;">
                        Complete more orders to submit additional feedback!
                    </p>
                </div>
            @elseif($feedbackInfo)
                <div style="padding: 12px 16px; background: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 8px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #1e40af; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> You can submit <strong>{{ $feedbackInfo['remaining'] }}</strong> more feedback(s) based on your completed orders.
                    </p>
                </div>
            @endif

            <form id="feedbackForm" action="{{ route('customer.feedback.store') }}" method="POST" @if($feedbackInfo && !$feedbackInfo['can_submit']) style="pointer-events: none; opacity: 0.6;" @endif>
                @csrf
                <div class="grid-two-narrow">
                    <!-- Star Rating Section -->
                    <div class="row span2">
                        <label for="fbRating">Rate Your Experience <span style="color: #dc2626;">*</span></label>
                        <div class="star-rating" id="starRating">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="fbRating" name="rating" value="" required />
                        <small class="rating-text" id="ratingText" style="display: none; margin-top: 8px; color: #6b7280;"></small>
                    </div>

                    <div class="row">
                        <label for="fbName">Name</label>
                        <input type="text" id="fbName" name="name" placeholder="Your name" value="{{ Auth::check() ? Auth::user()->name : '' }}" required />
                    </div>
                    <div class="row">
                        <label for="fbContact">Email</label>
                        <input type="email" id="fbContact" name="email" placeholder="your@email.com" value="{{ Auth::check() ? Auth::user()->email : '' }}" required />
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
                    <a href="{{ route('customer.menu.index') }}">Menu</a>
                    <a href="#contact">Contact</a>
                    <a href="#">Privacy</a>
                </nav>
            </div>
        </footer>

        
    <!-- Back to Top Button -->
    <button class="to-top {{ Auth::check() ? '' : 'logged-out' }}" id="toTop" aria-label="Back to top">↑</button>
@endsection

@section('scripts')
<script>
    // Page-specific initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Any homepage-specific JavaScript can go here
        // The main functionality is handled by customer-design.js

        // Example: Track page views, analytics, etc.
        console.log('🏠 Homepage loaded successfully');

        // Show session messages as Toast
        @if(session('feedback_success'))
            if (typeof Toast !== 'undefined') {
                Toast.success('Feedback Submitted!', '{{ session('feedback_success') }}');
            }
        @endif

        @if(session('feedback_error'))
            if (typeof Toast !== 'undefined') {
                Toast.error('Error', '{{ session('feedback_error') }}');
            }
        @endif
    });

    // Quick Add to Cart Function for AI Recommendations
    async function quickAddToCart(itemId, itemName, itemPrice, itemImage) {
        @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href);
        return;
        @endguest

        try {
            const response = await fetch('/customer/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    menu_item_id: itemId,
                    quantity: 1
                })
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                const toast = document.createElement('div');
                toast.style.cssText = 'position:fixed;top:24px;right:24px;background:#10b981;color:white;padding:16px 24px;border-radius:12px;box-shadow:0 4px 16px rgba(16,185,129,0.3);z-index:9999;font-weight:600;animation:slideIn 0.3s ease;';
                toast.textContent = `✅ ${itemName} added to cart!`;
                document.body.appendChild(toast);

                // Add animation
                const style = document.createElement('style');
                style.textContent = '@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
                document.head.appendChild(style);

                setTimeout(() => {
                    toast.style.animation = 'slideIn 0.3s ease reverse';
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to add item');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            if (typeof Toast !== 'undefined') {
                Toast.error('Error', 'Error: ' + error.message);
            } else {
                alert('Error: ' + error.message);
            }
        }
    }

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

// Star Rating Functionality
document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.getElementById('starRating');
    const ratingInput = document.getElementById('fbRating');
    const ratingText = document.getElementById('ratingText');
    const stars = starRating.querySelectorAll('i');
    const feedbackForm = document.getElementById('feedbackForm');

    let selectedRating = 0;

    const ratingLabels = {
        1: 'Poor - Not satisfied',
        2: 'Fair - Below expectations',
        3: 'Good - Met expectations',
        4: 'Very Good - Exceeded expectations',
        5: 'Excellent - Outstanding!'
    };

    // Star hover effect
    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
            ratingText.textContent = ratingLabels[index + 1];
            ratingText.style.display = 'block';
        });
    });

    starRating.addEventListener('mouseleave', function() {
        if (selectedRating > 0) {
            highlightStars(selectedRating);
            ratingText.textContent = ratingLabels[selectedRating];
        } else {
            clearStars();
            ratingText.style.display = 'none';
        }
    });

    // Star click to select
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            selectedRating = index + 1;
            ratingInput.value = selectedRating;
            highlightStars(selectedRating);
            ratingText.textContent = ratingLabels[selectedRating];
            ratingText.style.display = 'block';
            ratingText.style.color = '#8b5cf6';
            ratingText.style.fontWeight = '600';
        });
    });

    function highlightStars(count) {
        stars.forEach((star, index) => {
            if (index < count) {
                star.classList.remove('far');
                star.classList.add('fas');
                star.style.color = '#fbbf24';
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
                star.style.color = '#d1d5db';
            }
        });
    }

    function clearStars() {
        stars.forEach(star => {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = '#d1d5db';
        });
    }

    // Form submission validation
    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();

        @guest
        // User not logged in
        if (typeof Toast !== 'undefined') {
            Toast.warning('Login Required', 'Please login to submit feedback.');
        } else {
            alert('Please login to submit feedback.');
        }
        setTimeout(() => {
            window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href);
        }, 1500);
        return false;
        @endguest

        @if($feedbackInfo && !$feedbackInfo['can_submit'])
        // User has reached feedback limit
        if (typeof Toast !== 'undefined') {
            Toast.info('Feedback Limit Reached', 'You have submitted all available feedbacks. Complete more orders to submit additional feedback.');
        } else {
            alert('You have reached the maximum number of feedbacks. Complete more orders to submit additional feedback.');
        }
        return false;
        @endif

        // Check if rating is selected
        if (!ratingInput.value || ratingInput.value === '') {
            if (typeof Toast !== 'undefined') {
                Toast.warning('Rating Required', 'Please rate your experience before submitting.');
            } else {
                alert('Please rate your experience before submitting.');
            }
            starRating.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Submit the form
        this.submit();
    });

    // Clear button functionality
    document.getElementById('btnClear').addEventListener('click', function() {
        feedbackForm.reset();
        selectedRating = 0;
        clearStars();
        ratingText.style.display = 'none';
        ratingInput.value = '';
    });
});
</script>

<style>
.star-rating {
    display: flex;
    gap: 8px;
    font-size: 32px;
    cursor: pointer;
    margin-top: 8px;
}

.star-rating i {
    color: #d1d5db;
    transition: color 0.2s ease, transform 0.2s ease;
}

.star-rating i:hover {
    transform: scale(1.2);
}

.star-rating i.fas {
    color: #fbbf24;
}

.rating-text {
    font-size: 14px;
    font-weight: 500;
    margin-top: 8px;
}
</style>

@endsection