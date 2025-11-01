@extends('layouts.admin')

@section('title', 'Menu Item Details')
@section('page-title', 'Menu Item Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
<style>
    /* Modern Card Container */
    .menu-detail-container {
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

    /* Action Buttons - More Specific Selectors */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    /* Override existing styles with specific selector */
    .menu-detail-container .action-grid .action-btn,
    .menu-detail-container .action-grid a.action-btn,
    .menu-detail-container .action-grid button.action-btn {
        background: white !important;
        border: 2px solid #d1d5db !important;
        border-radius: 16px !important;
        padding: 20px !important;
        text-align: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        color: #374151 !important;
        display: block !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        width: 100% !important;
        height: auto !important;
        min-height: 80px !important;
        justify-content: center !important;
        align-items: center !important;
        flex-direction: column !important;
    }

    .menu-detail-container .action-grid .action-btn:hover,
    .menu-detail-container .action-grid a.action-btn:hover,
    .menu-detail-container .action-grid button.action-btn:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #667eea !important;
        background: #f9fafb !important;
    }

    /* Primary Button */
    .menu-detail-container .action-grid .action-btn.primary,
    .menu-detail-container .action-grid a.action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3) !important;
    }

    .menu-detail-container .action-grid .action-btn.primary:hover,
    .menu-detail-container .action-grid a.action-btn.primary:hover {
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4) !important;
        background: linear-gradient(135deg, #5568d3 0%, #6941a5 100%) !important;
    }

    /* Success Button */
    .menu-detail-container .action-grid .action-btn.success,
    .menu-detail-container .action-grid button.action-btn.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3) !important;
    }

    .menu-detail-container .action-grid .action-btn.success:hover,
    .menu-detail-container .action-grid button.action-btn.success:hover {
        box-shadow: 0 12px 24px rgba(16, 185, 129, 0.4) !important;
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    }

    /* Danger Button */
    .menu-detail-container .action-grid .action-btn.danger,
    .menu-detail-container .action-grid button.action-btn.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3) !important;
    }

    .menu-detail-container .action-grid .action-btn.danger:hover,
    .menu-detail-container .action-grid button.action-btn.danger:hover {
        box-shadow: 0 12px 24px rgba(239, 68, 68, 0.4) !important;
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    }

    /* Warning Button */
    .menu-detail-container .action-grid .action-btn.warning,
    .menu-detail-container .action-grid button.action-btn.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3) !important;
    }

    .menu-detail-container .action-grid .action-btn.warning:hover,
    .menu-detail-container .action-grid button.action-btn.warning:hover {
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.4) !important;
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
    }

    /* Outlined Button */
    .menu-detail-container .action-grid .action-btn-outlined,
    .menu-detail-container .action-grid button.action-btn-outlined {
        border: 2px solid #667eea !important;
        color: #667eea !important;
        background: white !important;
    }

    .menu-detail-container .action-grid .action-btn-outlined:hover,
    .menu-detail-container .action-grid button.action-btn-outlined:hover {
        background: #f3f4ff !important;
        border-color: #5568d3 !important;
    }

    /* Icons and Text */
    .menu-detail-container .action-grid .action-btn-icon {
        font-size: 28px !important;
        margin-bottom: 8px !important;
        display: block !important;
    }

    .menu-detail-container .action-grid .action-btn-text {
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    /* Ensure white text/icons in gradient buttons */
    .menu-detail-container .action-grid .action-btn.primary .action-btn-icon,
    .menu-detail-container .action-grid .action-btn.primary .action-btn-text,
    .menu-detail-container .action-grid .action-btn.success .action-btn-icon,
    .menu-detail-container .action-grid .action-btn.success .action-btn-text,
    .menu-detail-container .action-grid .action-btn.danger .action-btn-icon,
    .menu-detail-container .action-grid .action-btn.danger .action-btn-text,
    .menu-detail-container .action-grid .action-btn.warning .action-btn-icon,
    .menu-detail-container .action-grid .action-btn.warning .action-btn-text {
        color: white !important;
    }

    /* Outlined button icons/text */
    .menu-detail-container .action-grid .action-btn-outlined .action-btn-icon,
    .menu-detail-container .action-grid .action-btn-outlined .action-btn-text {
        color: #667eea !important;
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

    .image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, transparent 100%);
        padding: 20px;
        color: white;
    }

    .price-tag {
        font-size: 32px;
        font-weight: 800;
        color: #10b981;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
    }

    /* Rating Display */
    .rating-showcase {
        text-align: center;
        padding: 32px;
        background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
        border-radius: 16px;
    }

    .rating-number {
        font-size: 64px;
        font-weight: 800;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        margin-bottom: 12px;
    }

    .rating-stars-large {
        display: flex;
        justify-content: center;
        gap: 8px;
        font-size: 32px;
        margin-bottom: 12px;
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

    .status-badge.available {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-badge.unavailable {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .status-badge.featured {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    /* Allergen Display */
    .allergen-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
    }

    .allergen-tag {
        background: #fef2f2;
        border: 2px solid #fecaca;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        color: #dc2626;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .allergen-tag:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
    }

    .allergen-icon {
        font-size: 24px;
        margin-bottom: 8px;
        display: block;
    }

    /* Description Box */
    .description-box {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-left: 4px solid #667eea;
        padding: 24px;
        border-radius: 12px;
        font-size: 16px;
        line-height: 1.6;
        color: #374151;
    }

    /* Modal Improvements */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 24px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 28px;
    }

    .modal-header-modern h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .modal-body-modern {
        padding: 32px;
    }

    .modal-footer-modern {
        padding: 24px 32px;
        background: #f9fafb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    /* Additional Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .info-card, .action-btn, .image-card {
        animation: fadeIn 0.5s ease forwards;
    }

    .info-card:nth-child(1) { animation-delay: 0.1s; }
    .info-card:nth-child(2) { animation-delay: 0.2s; }
    .info-card:nth-child(3) { animation-delay: 0.3s; }
    .info-card:nth-child(4) { animation-delay: 0.4s; }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modern-header h1 {
            font-size: 28px;
        }

        .header-meta {
            flex-wrap: wrap;
        }

        .action-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .allergen-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }

        .rating-number {
            font-size: 48px;
        }

        .rating-stars-large {
            font-size: 24px;
        }
    }

    /* Loading State */
    .loading {
        position: relative;
        pointer-events: none;
        opacity: 0.6;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 24px;
        height: 24px;
        margin: -12px 0 0 -12px;
        border: 3px solid #667eea;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Tooltip */
    [data-tooltip] {
        position: relative;
    }

    [data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background: #1f2937;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 100;
    }

    [data-tooltip]:hover::after {
        opacity: 1;
    }

    /* Print Styles */
    @media print {
        .action-grid, .modern-header::before {
            display: none;
        }

        .info-card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #e5e7eb;
        }
    }
</style>
@endsection

@section('content')
<div class="menu-detail-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <h1>{{ $menuItem->name }}</h1>
        <div class="header-meta">
            <div class="header-badge">
                <i class="fas fa-utensils"></i>
                <span>{{ $menuItem->category ? $menuItem->category->name : 'No Category' }}</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-clock"></i>
                <span>{{ $menuItem->preparation_time }} min prep</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-hashtag"></i>
                <span>ID: {{ $menuItem->id }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="action-grid">
        <a href="{{ route('admin.menu-items.edit', $menuItem->id) }}" class="action-btn primary">
            <i class="fas fa-edit action-btn-icon"></i>
            <span class="action-btn-text">Edit Menu Item</span>
        </a>

        <form method="POST" action="{{ route('admin.menu-items.toggle-availability', $menuItem->id) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="action-btn {{ $menuItem->availability ? 'danger' : 'success' }}" style="width: 100%; border: none;">
                <i class="fas fa-{{ $menuItem->availability ? 'eye-slash' : 'eye' }} action-btn-icon"></i>
                <span class="action-btn-text">{{ $menuItem->availability ? 'Mark Unavailable' : 'Mark Available' }}</span>
            </button>
        </form>

        <form method="POST" action="{{ route('admin.menu-items.toggle-featured', $menuItem->id) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="action-btn {{ $menuItem->is_featured ? '' : 'warning' }}" style="width: 100%;">
                <i class="fas fa-star action-btn-icon"></i>
                <span class="action-btn-text">{{ $menuItem->is_featured ? 'Remove Featured' : 'Add to Featured' }}</span>
            </button>
        </form>

        <button onclick="document.getElementById('rating-modal').style.display='flex'" class="action-btn action-btn-outlined">
            <i class="fas fa-star action-btn-icon"></i>
            <span class="action-btn-text">Add Rating</span>
        </button>

        <a href="{{ route('admin.menu-items.index') }}" class="action-btn">
            <i class="fas fa-arrow-left action-btn-icon"></i>
            <span class="action-btn-text">Back to List</span>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column - Image Card -->
        <div class="image-card">
            <div class="image-wrapper">
                @if($menuItem->image)
                    <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}">
                    <div class="image-overlay">
                        <div class="price-tag">RM {{ number_format($menuItem->price, 2) }}</div>
                    </div>
                @else
                    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #9ca3af;">
                        <i class="fas fa-utensils" style="font-size: 64px; margin-bottom: 12px;"></i>
                        <span style="font-weight: 600;">No Image Available</span>
                    </div>
                @endif
            </div>

            <!-- Status Badges in Image Card -->
            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 12px;">
                <div class="status-badge {{ $menuItem->availability ? 'available' : 'unavailable' }}">
                    <i class="fas fa-{{ $menuItem->availability ? 'check-circle' : 'times-circle' }}"></i>
                    <span>{{ $menuItem->availability ? 'Available' : 'Unavailable' }}</span>
                </div>

                @if($menuItem->is_featured)
                <div class="status-badge featured">
                    <i class="fas fa-star"></i>
                    <span>Featured Item</span>
                </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #f3f4f6;">
                <div class="stat-item" style="margin-bottom: 12px;">
                    <div class="stat-label">Price</div>
                    <div class="stat-value" style="color: #10b981;">RM {{ number_format($menuItem->price, 2) }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Prep Time</div>
                    <div class="stat-value">{{ $menuItem->preparation_time }} min</div>
                </div>
            </div>
        </div>

        <!-- Right Column - Info Section -->
        <div class="info-section">
            <!-- Description Card -->
            @if($menuItem->description)
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon">
                        <i class="fas fa-align-left"></i>
                    </div>
                    <h3 class="info-card-title">Description</h3>
                </div>
                <div class="description-box">
                    {{ $menuItem->description }}
                </div>
            </div>
            @endif

            <!-- Basic Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="info-card-title">Basic Information</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Category</div>
                        <div class="stat-value" style="font-size: 16px;">
                            {{ $menuItem->category ? $menuItem->category->name : 'N/A' }}
                        </div>
                        @if($menuItem->category && $menuItem->category->parent)
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                            Parent: {{ $menuItem->category->parent->name }}
                        </div>
                        @endif
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Item ID</div>
                        <div class="stat-value" style="font-family: monospace; font-size: 16px;">#{{ $menuItem->id }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Created</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $menuItem->created_at->format('M d, Y') }}</div>
                    </div>
                    @if($menuItem->updated_at != $menuItem->created_at)
                    <div class="stat-item">
                        <div class="stat-label">Last Updated</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $menuItem->updated_at->format('M d, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rating Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="info-card-title">Customer Rating</h3>
                </div>
                @if($menuItem->rating_count > 0)
                <div class="rating-showcase">
                    <div class="rating-number">{{ number_format($menuItem->rating_average, 1) }}</div>
                    <div class="rating-stars-large">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($menuItem->rating_average))
                                <i class="fas fa-star" style="color: #f59e0b;"></i>
                            @elseif($i - 0.5 <= $menuItem->rating_average)
                                <i class="fas fa-star-half-alt" style="color: #f59e0b;"></i>
                            @else
                                <i class="far fa-star" style="color: #d97706;"></i>
                            @endif
                        @endfor
                    </div>
                    <div style="color: #92400e; font-weight: 600;">
                        Based on {{ $menuItem->rating_count }} {{ Str::plural('review', $menuItem->rating_count) }}
                    </div>
                </div>
                @else
                <div style="text-align: center; padding: 40px; color: #9ca3af;">
                    <i class="fas fa-star" style="font-size: 56px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <div style="font-weight: 600; font-size: 18px; margin-bottom: 8px;">No ratings yet</div>
                    <div style="font-size: 14px;">Be the first to rate this item</div>
                </div>
                @endif
            </div>

            <!-- Allergen Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="info-card-title">Allergen Information</h3>
                </div>
                @if($menuItem->allergens && count($menuItem->allergens) > 0)
                    <div class="allergen-grid">
                        @foreach($menuItem->allergens as $allergen)
                            <div class="allergen-tag">
                                <i class="fas fa-exclamation-circle allergen-icon"></i>
                                <span>{{ $allergen }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top: 20px; padding: 16px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 12px; border-left: 4px solid #dc2626;">
                        <div style="display: flex; align-items: start; gap: 12px; color: #991b1b;">
                            <i class="fas fa-info-circle" style="font-size: 20px; margin-top: 2px;"></i>
                            <div>
                                <div style="font-weight: 700; margin-bottom: 4px;">Allergen Warning</div>
                                <div style="font-size: 14px;">This item contains the allergens listed above. Please inform staff of any allergies before ordering.</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="padding: 32px; text-align: center; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-radius: 12px;">
                        <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 12px;"></i>
                        <div style="font-weight: 700; color: #065f46; margin-bottom: 8px; font-size: 16px;">No specific allergens listed</div>
                        <div style="color: #047857; font-size: 14px;">However, please inform staff of any allergies as ingredients may vary.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modern Rating Modal -->
<div id="rating-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header-modern">
            <h3>Add Rating for {{ $menuItem->name }}</h3>
            <button type="button" onclick="document.getElementById('rating-modal').style.display='none'"
                    style="position: absolute; top: 28px; right: 28px; background: rgba(255,255,255,0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.menu-items.rating', $menuItem->id) }}">
            @csrf
            @method('PATCH')

            <div class="modal-body-modern">
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">How would you rate this item?</div>
                    <div style="font-size: 14px; color: #6b7280;">Click on the stars to rate</div>
                </div>

                <div id="star-container" style="display: flex; justify-content: center; gap: 12px; margin: 32px 0;">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="rating-star" data-rating="{{ $i }}"
                                style="background: none; border: none; cursor: pointer; color: #d1d5db; font-size: 48px; transition: all 0.2s ease;">
                            <i class="fas fa-star"></i>
                        </button>
                    @endfor
                </div>

                <div id="rating-text" style="text-align: center; min-height: 30px; font-size: 16px; font-weight: 600; color: #667eea;"></div>

                <input type="hidden" name="rating" id="rating-value" required>
            </div>

            <div class="modal-footer-modern">
                <button type="button" onclick="document.getElementById('rating-modal').style.display='none'"
                        style="padding: 12px 24px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; color: #6b7280; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    Cancel
                </button>
                <button type="submit" id="submit-rating-btn" disabled
                        style="padding: 12px 24px; border-radius: 12px; border: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease; opacity: 0.5;">
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Rating modal functionality
const stars = document.querySelectorAll('.rating-star');
const ratingInput = document.getElementById('rating-value');
const ratingText = document.getElementById('rating-text');
const submitBtn = document.getElementById('submit-rating-btn');
const starContainer = document.getElementById('star-container');

const ratingLabels = {
    1: '⭐ Poor',
    2: '⭐⭐ Fair',
    3: '⭐⭐⭐ Good',
    4: '⭐⭐⭐⭐ Very Good',
    5: '⭐⭐⭐⭐⭐ Excellent'
};

// Click handler
stars.forEach((star) => {
    star.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        ratingInput.value = rating;
        ratingText.textContent = ratingLabels[rating];

        // Enable submit button
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';

        // Update all stars
        updateStars(rating);

        // Add bounce animation
        this.style.transform = 'scale(1.3)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 200);
    });

    // Hover handler
    star.addEventListener('mouseenter', function() {
        const rating = parseInt(this.dataset.rating);
        updateStars(rating);
        ratingText.textContent = ratingLabels[rating];
    });
});

// Reset on mouse leave container
starContainer.addEventListener('mouseleave', function() {
    const currentRating = parseInt(ratingInput.value) || 0;
    if (currentRating > 0) {
        updateStars(currentRating);
        ratingText.textContent = ratingLabels[currentRating];
    } else {
        updateStars(0);
        ratingText.textContent = '';
    }
});

// Update star colors
function updateStars(rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.style.color = '#f59e0b';
            star.style.transform = 'scale(1.1)';
        } else {
            star.style.color = '#d1d5db';
            star.style.transform = 'scale(1)';
        }
    });
}

// Close modal when clicking outside
document.getElementById('rating-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
        resetModal();
    }
});

// Reset modal state
function resetModal() {
    ratingInput.value = '';
    ratingText.textContent = '';
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.5';
    updateStars(0);
}

// Button hover effects
document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.02)';
    });

    btn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Add smooth scroll for page
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endsection