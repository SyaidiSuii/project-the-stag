@extends('layouts.customer')

@section('title', $happyHourDeal->name . ' - The Stag SmartDine')

@section('styles')
<style>
.happy-hour-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    color: #6b7280;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.back-button:hover {
    border-color: #f59e0b;
    color: #f59e0b;
    transform: translateX(-4px);
}

.happy-hour-hero {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 20px;
    padding: 50px 40px;
    text-align: center;
    color: white;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.happy-hour-hero.active {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.happy-hour-hero::before {
    content: '⏰';
    position: absolute;
    font-size: 20rem;
    opacity: 0.1;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.status-badge-large {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.status-badge-large.active {
    background: rgba(255,255,255,0.3);
    animation: pulse-glow 2s ease-in-out infinite;
}

.status-badge-large.upcoming {
    background: rgba(255,255,255,0.2);
}

.status-badge-large.ended {
    background: rgba(0,0,0,0.2);
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(255,255,255,0.3); }
    50% { box-shadow: 0 0 40px rgba(255,255,255,0.5); }
}

.deal-name {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 16px;
}

.deal-discount {
    font-size: 4rem;
    font-weight: 900;
    margin: 20px 0;
    text-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.countdown-timer {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 16px;
}

.time-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.time-info-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
}

.time-info-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.time-info-label {
    font-size: 0.9rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 8px;
}

.time-info-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1f2937;
}

.days-display {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

.day-chip {
    padding: 8px 16px;
    background: #f59e0b;
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.menu-items-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f3f4f6;
}

.menu-items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.menu-item-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.menu-item-card:hover {
    border-color: #f59e0b;
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);
}

.menu-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 12px;
}

.menu-item-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    flex: 1;
}

.menu-item-category {
    font-size: 0.75rem;
    color: #6b7280;
    background: white;
    padding: 4px 10px;
    border-radius: 12px;
    margin-top: 4px;
}

.menu-item-prices {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 12px;
}

.original-price {
    font-size: 1rem;
    color: #9ca3af;
    text-decoration: line-through;
}

.discounted-price {
    font-size: 1.4rem;
    font-weight: 800;
    color: #f59e0b;
}

.savings-badge {
    background: #dcfce7;
    color: #16a34a;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 700;
}

.cta-section {
    text-align: center;
    padding: 40px 20px;
}

.order-now-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 18px 48px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    text-decoration: none;
    border-radius: 14px;
    font-size: 1.3rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.order-now-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(245, 158, 11, 0.4);
}

.order-now-btn.active {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@media (max-width: 768px) {
    .deal-name {
        font-size: 1.8rem;
    }

    .deal-discount {
        font-size: 3rem;
    }

    .menu-items-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="happy-hour-container">
    <!-- Back Button -->
    <a href="{{ route('customer.promotions.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Promotions
    </a>

    <!-- Happy Hour Hero -->
    <div class="happy-hour-hero {{ $isActive ? 'active' : '' }}">
        <div class="status-badge-large {{ $timeStatus['status'] }}">
            <i class="fas fa-{{ $isActive ? 'fire' : 'clock' }}"></i>
            {{ $isActive ? 'ACTIVE NOW!' : strtoupper(str_replace('_', ' ', $timeStatus['status'])) }}
        </div>
        <h1 class="deal-name">{{ $happyHourDeal->name }}</h1>
        <div class="deal-discount">{{ number_format($happyHourDeal->discount_percentage, 0) }}% OFF</div>
        <div class="countdown-timer">
            <i class="fas fa-{{ $isActive ? 'hourglass-half' : 'calendar-alt' }}"></i>
            {{ $timeStatus['message'] }}
        </div>
    </div>

    <!-- Time Information -->
    <div class="time-info-grid">
        <div class="time-info-card">
            <div class="time-info-icon">⏰</div>
            <div class="time-info-label">Time Period</div>
            <div class="time-info-value">
                {{ \Carbon\Carbon::parse($happyHourDeal->start_time)->format('g:i A') }} -
                {{ \Carbon\Carbon::parse($happyHourDeal->end_time)->format('g:i A') }}
            </div>
        </div>

        <div class="time-info-card">
            <div class="time-info-icon">📅</div>
            <div class="time-info-label">Available Days</div>
            <div class="time-info-value">
                <div class="days-display">
                    @foreach($happyHourDeal->days_of_week as $day)
                        <span class="day-chip">{{ ucfirst($day) }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="time-info-card">
            <div class="time-info-icon">🍽️</div>
            <div class="time-info-label">Items on Deal</div>
            <div class="time-info-value">{{ $happyHourDeal->menuItems->count() }} Menu Items</div>
        </div>
    </div>

    <!-- Menu Items -->
    <div class="menu-items-section">
        <h2 class="section-title">Menu Items on Special</h2>

        @if($happyHourDeal->menuItems->count() > 0)
            <div class="menu-items-grid">
                @foreach($happyHourDeal->menuItems as $item)
                    <div class="menu-item-card">
                        <div class="menu-item-header">
                            <div>
                                <div class="menu-item-name">{{ $item->name }}</div>
                                <div class="menu-item-category">{{ $item->category->name }}</div>
                            </div>
                        </div>
                        <div class="menu-item-prices">
                            <span class="original-price">RM {{ number_format($item->price, 2) }}</span>
                            <span class="discounted-price">
                                RM {{ number_format($item->price - $happyHourDeal->calculateDiscount($item->price), 2) }}
                            </span>
                            <span class="savings-badge">
                                Save RM {{ number_format($happyHourDeal->calculateDiscount($item->price), 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #6b7280; padding: 40px;">No menu items currently on this deal.</p>
        @endif
    </div>

    <!-- Call to Action -->
    <div class="cta-section">
        @if($isActive)
            <a href="{{ route('customer.menu.index') }}" class="order-now-btn active">
                <i class="fas fa-fire"></i> Order Now & Save {{ number_format($happyHourDeal->discount_percentage, 0) }}%!
            </a>
        @else
            <a href="{{ route('customer.menu.index') }}" class="order-now-btn">
                <i class="fas fa-utensils"></i> Browse Full Menu
            </a>
        @endif
    </div>
</div>
@endsection
