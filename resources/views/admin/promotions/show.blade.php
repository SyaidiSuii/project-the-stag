@extends('layouts.admin')

@section('title', 'Promotion Details')
@section('page-title', 'Promotion Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
    /* Modern Card Container */
    .promotion-detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }

    /* Header Section with Gradient */
    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 32px;
        color: white;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .modern-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.05);
        transform: rotate(45deg);
    }

    .modern-header h1 {
        font-size: 36px;
        font-weight: 700;
        margin: 0 0 12px 0;
        position: relative;
        z-index: 1;
    }

    .header-meta {
        display: flex;
        gap: 24px;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .header-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Action Buttons */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .promotion-detail-container .action-grid .action-btn,
    .promotion-detail-container .action-grid a.action-btn,
    .promotion-detail-container .action-grid button.action-btn {
        background: white !important;
        border: 2px solid #d1d5db !important;
        border-radius: 16px !important;
        padding: 28px 24px !important;
        text-align: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        color: #374151 !important;
        display: block !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        width: 100% !important;
        height: auto !important;
        min-height: 110px !important;
    }

    .promotion-detail-container .action-grid .action-btn:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #667eea !important;
        background: #f9fafb !important;
    }

    .promotion-detail-container .action-grid .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3) !important;
    }

    .promotion-detail-container .action-grid .action-btn.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
        border: none !important;
    }

    .action-btn-icon {
        font-size: 36px !important;
        margin-bottom: 12px !important;
        display: block !important;
    }

    .action-btn-text {
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
    }

    .promotion-detail-container .action-grid .action-btn.primary .action-btn-icon,
    .promotion-detail-container .action-grid .action-btn.primary .action-btn-text,
    .promotion-detail-container .action-grid .action-btn.danger .action-btn-icon,
    .promotion-detail-container .action-grid .action-btn.danger .action-btn-text {
        color: white !important;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 32px;
        margin-bottom: 32px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Image Card */
    .image-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        position: sticky;
        top: 24px;
        height: fit-content;
    }

    .image-wrapper {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 1;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    }

    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image-state {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .no-image-state i {
        font-size: 64px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    /* Info Cards */
    .info-section {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .info-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f3f4f6;
    }

    .info-card-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .info-card-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
    }

    .stat-item {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        border: 2px solid #f3f4f6;
    }

    .stat-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
    }

    .status-badge.active {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 2px solid #10b981;
    }

    .status-badge.inactive {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 2px solid #ef4444;
    }

    /* Discount Display */
    .discount-showcase {
        text-align: center;
        padding: 32px;
        background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
        border-radius: 16px;
        border: 2px solid #f59e0b;
    }

    .discount-number {
        font-size: 64px;
        font-weight: 800;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 12px;
    }

    .discount-label {
        font-size: 18px;
        font-weight: 600;
        color: #92400e;
    }

    /* Promo Code Display */
    .promo-code-box {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 2px solid #3b82f6;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
    }

    .promo-code-text {
        font-family: 'Courier New', monospace;
        font-size: 32px;
        font-weight: 800;
        color: #1e40af;
        letter-spacing: 2px;
        margin: 12px 0;
    }

    /* Date Range Display */
    .date-range-box {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 12px;
    }

    .date-box {
        flex: 1;
        text-align: center;
        padding: 16px;
        background: white;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
    }

    .date-box.start {
        border-color: #10b981;
    }

    .date-box.end {
        border-color: #ef4444;
    }

    .date-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
        color: #6b7280;
    }

    .date-box.start .date-label {
        color: #10b981;
    }

    .date-box.end .date-label {
        color: #ef4444;
    }

    .date-value {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }
</style>
@endsection

@section('content')
<div class="promotion-detail-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <h1>{{ $promotion->name }}</h1>
        <div class="header-meta">
            @if($promotion->promo_code)
            <div class="header-badge">
                <i class="fas fa-ticket-alt"></i>
                <span>{{ $promotion->promo_code }}</span>
            </div>
            @endif
            <div class="header-badge">
                <i class="fas fa-{{ $promotion->discount_type === 'percentage' ? 'percent' : 'money-bill-wave' }}"></i>
                <span>{{ $promotion->discount_type === 'percentage' ? number_format($promotion->discount_value, 0) . '% OFF' : 'RM ' . number_format($promotion->discount_value, 2) . ' OFF' }}</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-hashtag"></i>
                <span>ID: {{ $promotion->id }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="action-grid">
        <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="action-btn primary">
            <i class="fas fa-edit action-btn-icon"></i>
            <span class="action-btn-text">Edit Promotion</span>
        </a>

        <a href="{{ route('admin.promotions.index') }}" class="action-btn">
            <i class="fas fa-arrow-left action-btn-icon"></i>
            <span class="action-btn-text">Back to List</span>
        </a>

        <form action="{{ route('admin.promotions.destroy', $promotion->id) }}"
              method="POST"
              style="display: contents;"
              onsubmit="return confirm('Are you sure you want to delete this promotion? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="action-btn danger">
                <i class="fas fa-trash action-btn-icon"></i>
                <span class="action-btn-text">Delete Promotion</span>
            </button>
        </form>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column - Image Card -->
        <div class="image-card">
            <div class="image-wrapper">
                @if($promotion->image_path)
                    <img src="{{ asset('storage/' . $promotion->image_path) }}" alt="{{ $promotion->name }}">
                @else
                    <div class="no-image-state">
                        <i class="fas fa-image"></i>
                        <span style="font-weight: 600;">No Banner Image</span>
                    </div>
                @endif
            </div>

            <!-- Status Badges in Image Card -->
            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 12px;">
                <div class="status-badge {{ $promotion->is_active ? 'active' : 'inactive' }}">
                    <i class="fas fa-{{ $promotion->is_active ? 'check-circle' : 'times-circle' }}"></i>
                    <span>{{ $promotion->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #f3f4f6;">
                <div class="stat-item" style="margin-bottom: 12px;">
                    <div class="stat-label">Discount Type</div>
                    <div class="stat-value" style="font-size: 14px; text-transform: capitalize;">{{ $promotion->discount_type }}</div>
                </div>
                @if($promotion->minimum_order_value)
                <div class="stat-item">
                    <div class="stat-label">Min Order</div>
                    <div class="stat-value" style="font-size: 16px; color: #10b981;">RM {{ number_format($promotion->minimum_order_value, 2) }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Info Section -->
        <div class="info-section">
            <!-- Discount Value Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-{{ $promotion->discount_type === 'percentage' ? 'percent' : 'money-bill-wave' }}"></i>
                    </div>
                    <h3 class="info-card-title">Discount Value</h3>
                </div>
                <div class="discount-showcase">
                    @if($promotion->discount_type === 'percentage')
                        <div class="discount-number">{{ number_format($promotion->discount_value, 0) }}%</div>
                        <div class="discount-label"><i class="fas fa-tag"></i> Percentage Discount</div>
                    @else
                        <div class="discount-number">RM {{ number_format($promotion->discount_value, 2) }}</div>
                        <div class="discount-label"><i class="fas fa-money-bill-wave"></i> Fixed Amount Off</div>
                    @endif
                </div>
            </div>

            <!-- Promo Code Card -->
            @if($promotion->promo_code)
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="info-card-title">Promo Code</h3>
                </div>
                <div class="promo-code-box">
                    <div style="font-size: 14px; font-weight: 600; color: #1e40af; margin-bottom: 8px;">
                        <i class="fas fa-copy"></i> Use this code at checkout
                    </div>
                    <div class="promo-code-text">{{ $promotion->promo_code }}</div>
                </div>
            </div>
            @endif

            <!-- Basic Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="info-card-title">Promotion Details</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Promotion ID</div>
                        <div class="stat-value" style="font-family: monospace; font-size: 18px;">#{{ $promotion->id }}</div>
                    </div>
                    @if($promotion->minimum_order_value)
                    <div class="stat-item">
                        <div class="stat-label">Min Order Value</div>
                        <div class="stat-value" style="font-size: 16px; color: #10b981;">RM {{ number_format($promotion->minimum_order_value, 2) }}</div>
                    </div>
                    @else
                    <div class="stat-item">
                        <div class="stat-label">Min Order Value</div>
                        <div class="stat-value" style="font-size: 14px; color: #6b7280;">No minimum</div>
                    </div>
                    @endif
                    <div class="stat-item">
                        <div class="stat-label">Created</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $promotion->created_at->format('M d, Y') }}</div>
                    </div>
                    @if($promotion->updated_at != $promotion->created_at)
                    <div class="stat-item">
                        <div class="stat-label">Last Updated</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $promotion->updated_at->format('M d, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Valid Period Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="info-card-title">Valid Period</h3>
                </div>
                <div class="date-range-box">
                    <div class="date-box start">
                        <div class="date-label">
                            <i class="fas fa-calendar-check"></i> Start Date
                        </div>
                        <div class="date-value">{{ $promotion->start_date->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $promotion->start_date->format('l') }}</div>
                    </div>
                    
                    <div style="font-size: 24px; color: #d1d5db;">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    
                    <div class="date-box end">
                        <div class="date-label">
                            <i class="fas fa-calendar-times"></i> End Date
                        </div>
                        <div class="date-value">{{ $promotion->end_date->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $promotion->end_date->format('l') }}</div>
                    </div>
                </div>
                
                @php
                    $now = now();
                    $isActive = $promotion->start_date <= $now && $promotion->end_date >= $now;
                    $daysLeft = $now->diffInDays($promotion->end_date, false);
                @endphp
                
                <div style="margin-top: 20px; padding: 16px; background: linear-gradient(135deg, {{ $isActive ? '#ecfdf5 0%, #d1fae5 100%' : '#fee2e2 0%, #fecaca 100%' }}); border-radius: 12px; text-align: center;">
                    @if($isActive)
                        <div style="font-weight: 700; color: #065f46; margin-bottom: 4px;">
                            <i class="fas fa-check-circle"></i> Promotion is Currently Active
                        </div>
                        @if($daysLeft > 0)
                            <div style="font-size: 14px; color: #047857;">{{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} remaining</div>
                        @else
                            <div style="font-size: 14px; color: #047857;">Ends today</div>
                        @endif
                    @elseif($now < $promotion->start_date)
                        <div style="font-weight: 700; color: #1e40af;">
                            <i class="fas fa-clock"></i> Promotion Starts Soon
                        </div>
                        <div style="font-size: 14px; color: #1e3a8a;">Starts in {{ $now->diffInDays($promotion->start_date) }} {{ Str::plural('day', $now->diffInDays($promotion->start_date)) }}</div>
                    @else
                        <div style="font-weight: 700; color: #991b1b;">
                            <i class="fas fa-times-circle"></i> Promotion Has Ended
                        </div>
                        <div style="font-size: 14px; color: #7f1d1d;">Ended {{ $now->diffInDays($promotion->end_date) }} {{ Str::plural('day', $now->diffInDays($promotion->end_date)) }} ago</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
