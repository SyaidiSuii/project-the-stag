@extends('layouts.customer')

@section('title', 'My Reviews - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
<style>
.reviews-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.reviews-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.reviews-header h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}

.reviews-header p {
    color: #64748b;
    font-size: 1.125rem;
}

.reviews-stats {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 3rem;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
}

.reviews-stats:hover {
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.1);
    border-color: #10b981;
}

.stat-card {
    text-align: center;
    position: relative;
    padding: 0 2rem;
    transition: all 0.3s ease;
}

.stat-card:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 60px;
    width: 1px;
    background: linear-gradient(180deg, transparent, #e2e8f0, transparent);
}

.stat-card:hover {
    transform: scale(1.05);
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.75px;
}

.review-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.review-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(180deg, #10b981 0%, #059669 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.review-card:hover {
    border-color: #10b981;
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.12);
    transform: translateY(-2px);
}

.review-card:hover::before {
    opacity: 1;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.review-item-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.review-order-link {
    color: #6366f1;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    background: #eef2ff;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.review-order-link:hover {
    background: #6366f1;
    color: white;
    text-decoration: none;
    transform: translateX(2px);
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.stars {
    color: #fbbf24;
    font-size: 1.75rem;
    letter-spacing: 2px;
    text-shadow: 0 1px 2px rgba(251, 191, 36, 0.2);
}

.rating-value {
    color: #475569;
    font-weight: 700;
    font-size: 1.125rem;
}

.review-date {
    color: #94a3b8;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-weight: 500;
}

.review-text {
    color: #334155;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 1.25rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 12px;
    border-left: 3px solid #e2e8f0;
    font-style: italic;
    position: relative;
}

.review-text::before {
    content: '"';
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    font-size: 2.5rem;
    color: #cbd5e1;
    font-family: Georgia, serif;
    line-height: 1;
}

.review-badges {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-size: 0.813rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.badge:hover {
    transform: translateY(-1px);
}

.badge-anonymous {
    background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
    color: #4338ca;
    border: 1px solid #c7d2fe;
}

.badge-admin-response {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.admin-response {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-left: 4px solid #3b82f6;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    margin-top: 1.25rem;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.08);
}

.admin-response-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: #1e40af;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.admin-response-text {
    color: #1e3a8a;
    font-size: 0.938rem;
    line-height: 1.7;
}

.no-reviews {
    text-align: center;
    padding: 5rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 24px;
    border: 2px dashed #cbd5e1;
}

.no-reviews-icon {
    font-size: 5rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.no-reviews h3 {
    font-size: 1.75rem;
    color: #475569;
    margin-bottom: 1rem;
    font-weight: 700;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #64748b;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
    text-decoration: none;
    font-size: 0.938rem;
}

.back-button:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: #f5f3ff;
    transform: translateX(-4px);
    text-decoration: none;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    text-decoration: none;
    color: white;
}

/* Pagination Styling */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 3rem;
}

.pagination .page-link {
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-weight: 600;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    background: #f8fafc;
    border-color: #10b981;
    color: #10b981;
}

.pagination .active .page-link {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-color: #10b981;
}

@media (max-width: 768px) {
    .reviews-header h1 {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }

    .reviews-stats {
        flex-direction: column;
        gap: 2rem;
        padding: 2rem 1.5rem;
    }

    .stat-card {
        padding: 0;
    }

    .stat-card:not(:last-child)::after {
        display: none;
    }

    .review-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .stat-number {
        font-size: 2.5rem;
    }

    .back-button {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="reviews-container">
    <a href="{{ route('customer.account.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Account
    </a>

    <div class="reviews-header">
        <h1><i class="fas fa-star" style="color: #fbbf24;"></i> My Reviews</h1>
        <p>View and manage your menu item reviews</p>
    </div>

    @if($reviews->count() > 0)
        <div class="reviews-stats">
            <div class="stat-card">
                <div class="stat-number">{{ $reviews->total() }}</div>
                <div class="stat-label">Total Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($reviews->avg('rating'), 1) }}</div>
                <div class="stat-label">Average Rating</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $reviews->where('review_text', '!=', null)->count() }}</div>
                <div class="stat-label">With Comments</div>
            </div>
        </div>

        @foreach($reviews as $review)
            <div class="review-card">
                <div class="review-header">
                    <div class="review-item-info">
                        <h3>{{ $review->menuItem->name ?? 'Unknown Item' }}</h3>
                        @if($review->order)
                            <a href="{{ route('customer.orders.show', $review->order->id) }}" class="review-order-link">
                                <i class="fas fa-receipt"></i> Order #{{ $review->order->confirmation_code ?? 'ORD-' . $review->order->id }}
                            </a>
                        @endif
                    </div>
                    <div class="review-date">
                        <i class="fas fa-calendar"></i> {{ $review->created_at->format('M j, Y') }}
                    </div>
                </div>

                <div class="review-rating">
                    <div class="stars">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </div>
                    <span class="rating-value">{{ $review->rating }}.0</span>
                </div>

                @if($review->review_text)
                    <div class="review-text">
                        {{ $review->review_text }}
                    </div>
                @endif

                <div class="review-badges">
                    @if($review->is_anonymous)
                        <span class="badge badge-anonymous">
                            <i class="fas fa-user-secret"></i> Posted Anonymously
                        </span>
                    @endif

                    @if($review->hasAdminResponse())
                        <span class="badge badge-admin-response">
                            <i class="fas fa-reply"></i> Restaurant Responded
                        </span>
                    @endif
                </div>

                @if($review->hasAdminResponse())
                    <div class="admin-response">
                        <div class="admin-response-header">
                            <i class="fas fa-store"></i>
                            Restaurant Response
                        </div>
                        <div class="admin-response-text">
                            {{ $review->admin_response }}
                        </div>
                        @if($review->admin_response_at)
                            <div style="color: #64748b; font-size: 0.813rem; margin-top: 0.75rem; font-weight: 500;">
                                {{ $review->admin_response_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        <div style="margin-top: 2rem;">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="no-reviews">
            <div class="no-reviews-icon">
                <i class="fas fa-comment-slash"></i>
            </div>
            <h3>No Reviews Yet</h3>
            <p style="color: #64748b; margin-bottom: 2rem; font-size: 1.063rem;">You haven't reviewed any menu items yet. Order from us and share your experience!</p>
            <a href="{{ route('customer.menu.index') }}" class="btn btn-primary" style="text-decoration: none;">
                <i class="fas fa-utensils"></i> Browse Menu
            </a>
        </div>
    @endif
</div>
@endsection