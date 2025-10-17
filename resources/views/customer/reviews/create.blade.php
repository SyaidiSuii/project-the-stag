@extends('layouts.customer')

@section('title', 'Rate Your Order - The Stag SmartDine')

@section('styles')
<style>
.review-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
}

.review-header {
    text-align: center;
    margin-bottom: 2rem;
}

.review-header h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.review-header p {
    color: #6b7280;
    font-size: 1rem;
}

.order-summary {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.order-summary h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #374151;
}

.review-items-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.review-item-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    transition: border-color 0.3s;
}

.review-item-card:hover {
    border-color: #10b981;
}

.item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.item-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
}

.item-quantity {
    background: #e5e7eb;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.875rem;
    color: #4b5563;
}

.rating-section {
    margin-bottom: 1rem;
}

.rating-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.star-rating {
    display: flex;
    gap: 0.5rem;
    font-size: 2rem;
}

.star {
    cursor: pointer;
    color: #d1d5db;
    transition: color 0.2s, transform 0.1s;
}

.star:hover {
    transform: scale(1.1);
}

.star.active {
    color: #fbbf24;
}

.review-text-section {
    margin-bottom: 1rem;
}

.review-text-section label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.review-textarea {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    resize: vertical;
    font-family: inherit;
}

.review-textarea:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.anonymous-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.anonymous-checkbox input[type="checkbox"] {
    width: 1.125rem;
    height: 1.125rem;
    cursor: pointer;
}

.anonymous-checkbox label {
    font-size: 0.875rem;
    color: #6b7280;
    cursor: pointer;
    margin: 0;
}

.submit-section {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #10b981;
    color: white;
}

.btn-primary:hover {
    background: #059669;
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.success-message {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    text-align: center;
}

.error-message {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #991b1b;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    text-align: center;
}

.no-items-message {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.no-items-message h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

@media (max-width: 640px) {
    .review-container {
        padding: 1rem;
    }

    .submit-section {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>
@endsection

@section('content')
<div class="review-container">
    <div class="review-header">
        <h1>Rate Your Order</h1>
        <p>Help us serve you better by rating your meal</p>
    </div>

    <div class="order-summary">
        <h3>Order #{{ $order->confirmation_code ?? 'ORD-' . $order->id }}</h3>
        <p style="color: #6b7280; font-size: 0.875rem;">{{ $order->created_at->format('M j, Y - g:i A') }}</p>
    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    @if(!empty($reviewableItems))
        <form id="reviewForm" action="{{ route('customer.reviews.store-batch') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div class="review-items-list">
                @foreach($reviewableItems as $index => $item)
                    <div class="review-item-card">
                        <div class="item-header">
                            <span class="item-name">{{ $item['menu_item']->name }}</span>
                            <span class="item-quantity">x{{ $item['quantity'] }}</span>
                        </div>

                        <input type="hidden" name="reviews[{{ $index }}][menu_item_id]" value="{{ $item['menu_item_id'] }}">

                        <div class="rating-section">
                            <label class="rating-label">Rating *</label>
                            <div class="star-rating" data-item-index="{{ $index }}">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star" data-rating="{{ $i }}">★</span>
                                @endfor
                            </div>
                            <input type="hidden" name="reviews[{{ $index }}][rating]" class="rating-input" required>
                        </div>

                        <div class="review-text-section">
                            <label for="review_text_{{ $index }}">Your Review (Optional)</label>
                            <textarea
                                name="reviews[{{ $index }}][review_text]"
                                id="review_text_{{ $index }}"
                                class="review-textarea"
                                placeholder="Tell us about your experience with this dish..."></textarea>
                        </div>

                        <div class="anonymous-checkbox">
                            <input
                                type="checkbox"
                                name="reviews[{{ $index }}][is_anonymous]"
                                id="anonymous_{{ $index }}"
                                value="1">
                            <label for="anonymous_{{ $index }}">Post anonymously</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="submit-section">
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit Reviews</button>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    @else
        <div class="no-items-message">
            <h3>No Items to Review</h3>
            <p>All items from this order have already been reviewed or are no longer available.</p>
            <a href="{{ route('customer.orders.index') }}" class="btn btn-primary" style="margin-top: 1rem;">Back to Orders</a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starRatings = document.querySelectorAll('.star-rating');
    const submitBtn = document.getElementById('submitBtn');
    const reviewForm = document.getElementById('reviewForm');

    // Handle star rating clicks
    starRatings.forEach(ratingContainer => {
        const stars = ratingContainer.querySelectorAll('.star');
        const itemIndex = ratingContainer.dataset.itemIndex;
        const ratingInput = document.querySelector(`input[name="reviews[${itemIndex}][rating]"]`);

        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;

                // Update star display
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });

            // Hover effect
            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                stars.forEach((s, i) => {
                    if (i < rating) {
                        s.style.color = '#fbbf24';
                    } else {
                        s.style.color = '#d1d5db';
                    }
                });
            });
        });

        // Reset hover effect
        ratingContainer.addEventListener('mouseleave', function() {
            const currentRating = ratingInput.value;
            stars.forEach((s, i) => {
                if (i < currentRating) {
                    s.style.color = '#fbbf24';
                } else {
                    s.style.color = '#d1d5db';
                }
            });
        });
    });

    // Form submission
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate that all items have ratings
            const ratingInputs = document.querySelectorAll('.rating-input');
            let allRated = true;

            ratingInputs.forEach(input => {
                if (!input.value) {
                    allRated = false;
                }
            });

            if (!allRated) {
                alert('Please rate all items before submitting.');
                return;
            }

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            // Submit via AJAX
            const formData = new FormData(reviewForm);

            // Convert to JSON structure
            const reviews = [];
            ratingInputs.forEach((input, index) => {
                const reviewTextArea = document.querySelector(`textarea[name="reviews[${index}][review_text]"]`);
                const isAnonymousCheckbox = document.querySelector(`input[name="reviews[${index}][is_anonymous]"]`);
                const menuItemIdInput = document.querySelector(`input[name="reviews[${index}][menu_item_id]"]`);

                reviews.push({
                    menu_item_id: menuItemIdInput.value,
                    rating: input.value,
                    review_text: reviewTextArea.value,
                    is_anonymous: isAnonymousCheckbox.checked ? 1 : 0
                });
            });

            const data = {
                order_id: document.querySelector('input[name="order_id"]').value,
                reviews: reviews
            };

            fetch('{{ route("customer.reviews.store-batch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = '{{ route("customer.orders.index") }}';
                } else {
                    alert(data.message || 'Failed to submit reviews. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Reviews';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Reviews';
            });
        });
    }
});
</script>
@endsection
