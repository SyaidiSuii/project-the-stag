@extends('layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders-management.css') }}">
<style>
    /* Modern Card Container */
    .order-detail-container {
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

    /* Action Buttons - More Specific Selectors */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    /* Override existing styles with specific selector */
    .order-detail-container .action-grid .action-btn,
    .order-detail-container .action-grid a.action-btn,
    .order-detail-container .action-grid button.action-btn {
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
        justify-content: center !important;
        align-items: center !important;
        flex-direction: column !important;
    }

    .order-detail-container .action-grid .action-btn:hover,
    .order-detail-container .action-grid a.action-btn:hover,
    .order-detail-container .action-grid button.action-btn:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #667eea !important;
        background: #f9fafb !important;
    }

    /* Primary Button */
    .order-detail-container .action-grid .action-btn.primary,
    .order-detail-container .action-grid a.action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.primary:hover,
    .order-detail-container .action-grid a.action-btn.primary:hover {
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4) !important;
        background: linear-gradient(135deg, #5568d3 0%, #6941a5 100%) !important;
    }

    /* Success Button */
    .order-detail-container .action-grid .action-btn.success,
    .order-detail-container .action-grid button.action-btn.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.success:hover,
    .order-detail-container .action-grid button.action-btn.success:hover {
        box-shadow: 0 12px 24px rgba(16, 185, 129, 0.4) !important;
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    }

    /* Danger Button */
    .order-detail-container .action-grid .action-btn.danger,
    .order-detail-container .action-grid button.action-btn.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.danger:hover,
    .order-detail-container .action-grid button.action-btn.danger:hover {
        box-shadow: 0 12px 24px rgba(239, 68, 68, 0.4) !important;
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    }

    /* Warning Button */
    .order-detail-container .action-grid .action-btn.warning,
    .order-detail-container .action-grid button.action-btn.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.warning:hover,
    .order-detail-container .action-grid button.action-btn.warning:hover {
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.4) !important;
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
    }

    /* Info Button */
    .order-detail-container .action-grid .action-btn.info,
    .order-detail-container .action-grid button.action-btn.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.info:hover,
    .order-detail-container .action-grid button.action-btn.info:hover {
        box-shadow: 0 12px 24px rgba(59, 130, 246, 0.4) !important;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    }

    /* Purple Button */
    .order-detail-container .action-grid .action-btn.purple,
    .order-detail-container .action-grid button.action-btn.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(139, 92, 246, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.purple:hover,
    .order-detail-container .action-grid button.action-btn.purple:hover {
        box-shadow: 0 12px 24px rgba(139, 92, 246, 0.4) !important;
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
    }

    /* Indigo Button */
    .order-detail-container .action-grid .action-btn.indigo,
    .order-detail-container .action-grid button.action-btn.indigo {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3) !important;
    }

    .order-detail-container .action-grid .action-btn.indigo:hover,
    .order-detail-container .action-grid button.action-btn.indigo:hover {
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.4) !important;
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
    }

    /* Icons and Text */
    .order-detail-container .action-grid .action-btn-icon {
        font-size: 36px !important;
        margin-bottom: 12px !important;
        display: block !important;
    }

    .order-detail-container .action-grid .action-btn-text {
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
    }

    /* Ensure white text/icons in gradient buttons */
    .order-detail-container .action-grid .action-btn.primary .action-btn-icon,
    .order-detail-container .action-grid .action-btn.primary .action-btn-text,
    .order-detail-container .action-grid .action-btn.success .action-btn-icon,
    .order-detail-container .action-grid .action-btn.success .action-btn-text,
    .order-detail-container .action-grid .action-btn.danger .action-btn-icon,
    .order-detail-container .action-grid .action-btn.danger .action-btn-text,
    .order-detail-container .action-grid .action-btn.warning .action-btn-icon,
    .order-detail-container .action-grid .action-btn.warning .action-btn-text,
    .order-detail-container .action-grid .action-btn.info .action-btn-icon,
    .order-detail-container .action-grid .action-btn.info .action-btn-text,
    .order-detail-container .action-grid .action-btn.purple .action-btn-icon,
    .order-detail-container .action-grid .action-btn.purple .action-btn-text,
    .order-detail-container .action-grid .action-btn.indigo .action-btn-icon,
    .order-detail-container .action-grid .action-btn.indigo .action-btn-text {
        color: white !important;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
        margin-bottom: 32px;
    }

    @media (min-width: 1024px) {
        .content-grid-2col {
            grid-template-columns: repeat(2, 1fr);
        }
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

    /* Order Items List */
    .order-items-list {
        border: 1px solid #d1d5db;
        border-radius: 16px;
        background: white;
        overflow: hidden;
    }

    .order-item {
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s ease;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-item:hover {
        background: #f9fafb;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 24px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -28px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

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
    }

    /* Animations */
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

    .info-card, .action-btn {
        animation: fadeIn 0.5s ease forwards;
    }

    .info-card:nth-child(1) { animation-delay: 0.1s; }
    .info-card:nth-child(2) { animation-delay: 0.2s; }
    .info-card:nth-child(3) { animation-delay: 0.3s; }
    .info-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection

@section('content')
<div class="order-detail-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <h1>Order #{{ $order->id }}</h1>
        <div class="header-meta">
            @if($order->confirmation_code)
            <div class="header-badge">
                <i class="fas fa-qrcode"></i>
                <span>{{ $order->confirmation_code }}</span>
            </div>
            @endif
            <div class="header-badge">
                <i class="fas fa-{{ $order->order_type == 'dine_in' ? 'utensils' : ($order->order_type == 'takeaway' ? 'shopping-bag' : 'truck') }}"></i>
                <span>{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-clock"></i>
                <span>{{ $order->order_time->format('M d, Y h:i A') }}</span>
            </div>
            <div class="header-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 18px;">
                <i class="fas fa-dollar-sign"></i>
                <span>RM {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="action-grid">
        <a href="{{ route('admin.order.edit', $order->id) }}" class="action-btn primary">
            <i class="fas fa-edit action-btn-icon"></i>
            <span class="action-btn-text">Edit Order</span>
        </a>

        @if($order->order_status == 'pending')
            <button onclick="updateOrderStatus('preparing')" class="action-btn success">
                <i class="fas fa-check action-btn-icon"></i>
                <span class="action-btn-text">Start Preparing</span>
            </button>
        @elseif($order->order_status == 'preparing')
            <button onclick="updateOrderStatus('ready')" class="action-btn purple">
                <i class="fas fa-bell action-btn-icon"></i>
                <span class="action-btn-text">Mark Ready</span>
            </button>
        @elseif($order->order_status == 'ready')
            @if($order->order_type === 'dine_in' && $order->order_source !== 'qr_scan')
                <button onclick="updateOrderStatus('served')" class="action-btn indigo">
                    <i class="fas fa-utensils action-btn-icon"></i>
                    <span class="action-btn-text">Mark Served</span>
                </button>
            @else
                <button onclick="updateOrderStatus('completed')" class="action-btn success">
                    <i class="fas fa-check-circle action-btn-icon"></i>
                    <span class="action-btn-text">Complete Order</span>
                </button>
            @endif
        @elseif($order->order_status == 'served')
            <button onclick="updateOrderStatus('completed')" class="action-btn success">
                <i class="fas fa-check-circle action-btn-icon"></i>
                <span class="action-btn-text">Complete Order</span>
            </button>
        @endif
        
        @if(!in_array($order->order_status, ['completed', 'cancelled']))
            <form action="{{ route('admin.order.cancel', $order->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="button"
                        onclick="confirmCancelOrder({{ $order->id }})"
                        class="action-btn danger">
                    <i class="fas fa-times action-btn-icon"></i>
                    <span class="action-btn-text">Cancel Order</span>
                </button>
            </form>
        @endif

        <a href="{{ route('admin.order.index') }}" class="action-btn">
            <i class="fas fa-arrow-left action-btn-icon"></i>
            <span class="action-btn-text">Back to Orders</span>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Info Section -->
        <div class="info-section">
            <!-- Order Summary Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="info-card-title">Order Summary</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Order ID</div>
                        <div class="stat-value" style="font-size: 18px;">#{{ $order->id }}</div>
                    </div>
                    
                    @if($order->confirmation_code)
                    <div class="stat-item">
                        <div class="stat-label">Confirmation</div>
                        <div class="stat-value" style="font-family: monospace; font-size: 16px;">{{ $order->confirmation_code }}</div>
                    </div>
                    @endif

                    <div class="stat-item">
                        <div class="stat-label">Total Amount</div>
                        <div class="stat-value" style="color: #10b981; font-size: 20px;">RM {{ number_format($order->total_amount, 2) }}</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">Order Time</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $order->order_time->format('M d, h:i A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="info-card-title">Customer Information</h3>
                </div>
                @if($order->user)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <span class="stat-label">Name</span>
                            <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">{{ $order->user->name }}</p>
                        </div>

                        <div>
                            <span class="stat-label">Email</span>
                            <p style="margin: 4px 0 0 0;">
                                <a href="mailto:{{ $order->user->email }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-envelope" style="margin-right: 4px;"></i>
                                    {{ $order->user->email }}
                                </a>
                            </p>
                        </div>

                        @if($order->user->phone)
                        <div>
                            <span class="stat-label">Phone</span>
                            <p style="margin: 4px 0 0 0;">
                                <a href="tel:{{ $order->user->phone }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-phone" style="margin-right: 4px;"></i>
                                    {{ $order->user->phone }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: #9ca3af;">
                        <i class="fas fa-user-slash" style="font-size: 56px; margin-bottom: 16px; opacity: 0.3;"></i>
                        <div style="font-weight: 600; font-size: 18px; margin-bottom: 8px;">No customer data</div>
                        <div style="font-size: 14px;">Customer information not available</div>
                    </div>
                @endif
            </div>

            <!-- Status Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="info-card-title">Status Information</h3>
                </div>
                <div class="content-grid content-grid-2col" style="gap: 16px;">
                    <!-- Order Status -->
                    <div style="background: 
                        @if($order->order_status == 'pending') linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%)
                        @elseif($order->order_status == 'preparing') linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)
                        @elseif($order->order_status == 'ready') linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%)
                        @elseif($order->order_status == 'served') linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%)
                        @elseif($order->order_status == 'completed') linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)
                        @elseif($order->order_status == 'cancelled') linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)
                        @else linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%) @endif;
                        border-radius: 12px; padding: 20px; text-align: center;">
                        <i class="fas fa-
                            @if($order->order_status == 'pending') clock
                            @elseif($order->order_status == 'preparing') utensils
                            @elseif($order->order_status == 'ready') bell
                            @elseif($order->order_status == 'served') utensils
                            @elseif($order->order_status == 'completed') check-double
                            @elseif($order->order_status == 'cancelled') times-circle
                            @else info-circle @endif" 
                           style="color: 
                            @if($order->order_status == 'pending') #d97706
                            @elseif($order->order_status == 'preparing') #3b82f6
                            @elseif($order->order_status == 'ready') #8b5cf6
                            @elseif($order->order_status == 'served') #6366f1
                            @elseif($order->order_status == 'completed') #10b981
                            @elseif($order->order_status == 'cancelled') #ef4444
                            @else #6b7280 @endif; font-size: 32px; margin-bottom: 12px;"></i>
                        <div class="stat-label" style="margin-bottom: 8px;">Order Status</div>
                        <div style="color: 
                            @if($order->order_status == 'pending') #92400e
                            @elseif($order->order_status == 'preparing') #1e40af
                            @elseif($order->order_status == 'ready') #6b21a8
                            @elseif($order->order_status == 'served') #3730a3
                            @elseif($order->order_status == 'completed') #065f46
                            @elseif($order->order_status == 'cancelled') #991b1b
                            @else #374151 @endif; font-weight: 700; text-transform: capitalize; font-size: 18px;">
                            {{ str_replace('_', ' ', $order->order_status) }}
                        </div>
                        @if($order->is_rush_order)
                            <span style="margin-top: 8px; display: inline-block; background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                <i class="fas fa-bolt"></i> RUSH ORDER
                            </span>
                        @endif
                    </div>

                    <!-- Payment Status -->
                    <div style="background: 
                        @if($order->payment_status == 'paid') linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)
                        @elseif($order->payment_status == 'partial') linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%)
                        @elseif($order->payment_status == 'unpaid') linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)
                        @elseif($order->payment_status == 'refunded') linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%)
                        @else linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%) @endif;
                        border-radius: 12px; padding: 20px; text-align: center;">
                        <i class="fas fa-
                            @if($order->payment_status == 'paid') check-circle
                            @elseif($order->payment_status == 'partial') exclamation-circle
                            @elseif($order->payment_status == 'unpaid') times-circle
                            @elseif($order->payment_status == 'refunded') undo
                            @else question-circle @endif" 
                           style="color: 
                            @if($order->payment_status == 'paid') #10b981
                            @elseif($order->payment_status == 'partial') #d97706
                            @elseif($order->payment_status == 'unpaid') #ef4444
                            @elseif($order->payment_status == 'refunded') #6b7280
                            @else #6b7280 @endif; font-size: 32px; margin-bottom: 12px;"></i>
                        <div class="stat-label" style="margin-bottom: 8px;">Payment Status</div>
                        <div style="color: 
                            @if($order->payment_status == 'paid') #065f46
                            @elseif($order->payment_status == 'partial') #92400e
                            @elseif($order->payment_status == 'unpaid') #991b1b
                            @elseif($order->payment_status == 'refunded') #374151
                            @else #374151 @endif; font-weight: 700; text-transform: capitalize; font-size: 18px;">
                            {{ $order->payment_status }}
                        </div>
                        @if($order->payment_status != 'paid' && !in_array($order->order_status, ['completed', 'cancelled']))
                            <select onchange="updatePaymentStatus(this.value)" style="margin-top: 12px; padding: 6px 12px; border-radius: 8px; border: 2px solid rgba(0,0,0,0.1); background: white; font-weight: 600; cursor: pointer;">
                                <option value="unpaid" @if($order->payment_status == 'unpaid') selected @endif>Unpaid</option>
                                <option value="partial" @if($order->payment_status == 'partial') selected @endif>Partial</option>
                                <option value="paid" @if($order->payment_status == 'paid') selected @endif>Paid</option>
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Details Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="info-card-title">Order Details</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Order Type</div>
                        <div class="stat-value" style="font-size: 16px;">
                            <i class="fas fa-
                                @if($order->order_type == 'dine_in') utensils
                                @elseif($order->order_type == 'takeaway') shopping-bag
                                @elseif($order->order_type == 'delivery') truck
                                @else clipboard @endif" style="margin-right: 4px; color: #6b7280;"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">Order Source</div>
                        <div class="stat-value" style="font-size: 16px;">
                            <i class="fas fa-
                                @if($order->order_source == 'website') globe
                                @elseif($order->order_source == 'mobile_app') mobile-alt
                                @elseif($order->order_source == 'in_person') store
                                @elseif($order->order_source == 'phone') phone
                                @else question @endif" style="margin-right: 4px; color: #6b7280;"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->order_source)) }}
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">Payment Method</div>
                        <div class="stat-value" style="font-size: 16px;">
                            <i class="fas fa-
                                @if($order->payment_method == 'counter') cash-register
                                @elseif($order->payment_method == 'online_banking') university
                                @elseif($order->payment_method == 'credit_card') credit-card
                                @elseif($order->payment_method == 'debit_card') credit-card
                                @elseif($order->payment_method == 'e_wallet') wallet
                                @else money-bill @endif" style="margin-right: 4px; color: #6b7280;"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        </div>
                    </div>

                    @if($order->table || $order->table_number)
                    <div class="stat-item">
                        <div class="stat-label">Table</div>
                        <div class="stat-value" style="font-size: 16px;">
                            @if($order->table)
                                Table {{ $order->table->table_number }}
                            @else
                                {{ $order->table_number }}
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reservation Information -->
            @if($order->reservation)
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="info-card-title">Related Reservation</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <span class="stat-label">Confirmation Code</span>
                        <p style="font-family: monospace; font-size: 18px; font-weight: 700; margin: 4px 0 0 0; color: #ec4899;">{{ $order->reservation->confirmation_code }}</p>
                    </div>
                    <div>
                        <span class="stat-label">Guest Name</span>
                        <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $order->reservation->guest_name }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Special Instructions -->
            @if($order->special_instructions && count($order->special_instructions) > 0)
            <div class="info-card" style="border: 2px solid #f59e0b;">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <h3 class="info-card-title">Special Instructions</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($order->special_instructions as $instruction)
                        @if($instruction)
                            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 16px; border-radius: 12px; border-left: 4px solid #f59e0b;">
                                <div style="display: flex; align-items: start; gap: 12px;">
                                    <i class="fas fa-sticky-note" style="color: #d97706; font-size: 18px; margin-top: 2px;"></i>
                                    <span style="color: #92400e; font-weight: 600; flex: 1;">{{ $instruction }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Timing Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="info-card-title">Timing Information</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Order Placed</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $order->order_time->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $order->order_time->format('h:i A') }}</div>
                    </div>

                    @if($order->estimated_completion_time)
                    <div class="stat-item">
                        <div class="stat-label">Est. Completion</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $order->estimated_completion_time->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $order->estimated_completion_time->format('h:i A') }}</div>
                        @php
                            $now = now();
                            $isOverdue = $order->estimated_completion_time < $now && !$order->actual_completion_time;
                        @endphp
                        @if($isOverdue)
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; margin-top: 8px; display: inline-block;">
                                <i class="fas fa-exclamation-triangle"></i> OVERDUE
                            </span>
                        @endif
                    </div>
                    @endif

                    @if($order->actual_completion_time)
                    <div class="stat-item" style="border-color: #10b981; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
                        <div class="stat-label" style="color: #065f46;">Actual Completion</div>
                        <div class="stat-value" style="font-size: 14px; color: #10b981;">{{ $order->actual_completion_time->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #059669; margin-top: 4px;">{{ $order->actual_completion_time->format('h:i A') }}</div>
                    </div>
                    @endif

                    <div class="stat-item">
                        <div class="stat-label">Created</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $order->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $order->created_at->format('h:i A') }}</div>
                    </div>

                    @if($order->updated_at != $order->created_at)
                    <div class="stat-item">
                        <div class="stat-label">Last Updated</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $order->updated_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $order->updated_at->format('h:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- ETA Information -->
            @if($order->etas && $order->etas->count() > 0)
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <h3 class="info-card-title">Estimated Time of Arrival (ETA)</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($order->etas as $eta)
                    <div style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border-radius: 12px; padding: 20px; border: 2px solid #14b8a6;">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 16px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                    <i class="fas fa-clock" style="color: #0d9488; font-size: 20px;"></i>
                                    <span style="font-size: 18px; font-weight: 700; color: #134e4a;">{{ $eta->initial_estimate }} minutes</span>
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">(Initial)</span>
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Current Estimate</span>
                                    <p style="font-size: 24px; font-weight: 700; color: #0d9488; margin: 4px 0;">{{ $eta->current_estimate }} minutes</p>
                                </div>
                                @if($eta->is_delayed)
                                    <div style="margin-top: 12px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); padding: 12px; border-radius: 8px; border-left: 4px solid #ef4444;">
                                        <div style="display: flex; align-items: start; gap: 8px;">
                                            <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 16px; margin-top: 2px;"></i>
                                            <div style="flex: 1;">
                                                <div style="color: #991b1b; font-weight: 700; margin-bottom: 4px;">Delayed by {{ $eta->delay_duration }} minutes</div>
                                                @if($eta->delay_reason)
                                                    <div style="color: #7f1d1d; font-size: 14px;">Reason: {{ $eta->delay_reason }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($eta->actual_completion_time)
                                    <div style="margin-top: 12px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 18px;"></i>
                                        <span style="color: #065f46; font-weight: 700;">Completed in {{ $eta->actual_completion_time }} minutes</span>
                                    </div>
                                @endif
                            </div>
                            <div style="text-align: right; min-width: 120px;">
                                @php
                                    $estimatedCompletionTime = $order->order_time ? $order->order_time->addMinutes($eta->current_estimate) : null;
                                    $now = now();
                                    $isOverdue = $estimatedCompletionTime && $estimatedCompletionTime < $now && !in_array($order->order_status, ['completed', 'cancelled']);
                                @endphp
                                @if($estimatedCompletionTime)
                                    <div style="margin-bottom: 8px;">
                                        <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Expected</div>
                                        <div style="font-size: 16px; font-weight: 700; color: #134e4a;">{{ $estimatedCompletionTime->format('h:i A') }}</div>
                                    </div>
                                    <span style="
                                        background: {{ $isOverdue ? 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)' : ($eta->is_delayed ? 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)' : 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)') }}; 
                                        color: {{ $isOverdue ? '#991b1b' : ($eta->is_delayed ? '#92400e' : '#065f46') }}; 
                                        padding: 6px 12px; 
                                        border-radius: 12px; 
                                        font-size: 11px; 
                                        font-weight: 700;
                                        display: inline-block;
                                        border: 2px solid {{ $isOverdue ? '#ef4444' : ($eta->is_delayed ? '#f59e0b' : '#10b981') }};">
                                        @if($isOverdue)
                                            <i class="fas fa-exclamation-circle"></i> OVERDUE
                                        @elseif($eta->is_delayed)
                                            <i class="fas fa-clock"></i> DELAYED
                                        @else
                                            <i class="fas fa-check-circle"></i> ON TIME
                                        @endif
                                    </span>
                                @endif
                                @if($eta->customer_notified)
                                    <div style="margin-top: 12px; padding: 6px 10px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 8px; border: 1px solid #10b981;">
                                        <div style="font-size: 10px; color: #065f46; font-weight: 700;">
                                            <i class="fas fa-bell" style="margin-right: 4px;"></i>
                                            NOTIFIED
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Order Tracking History -->
            @if($order->trackings && $order->trackings->count() > 0)
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="info-card-title">Order Tracking History</h3>
                </div>
                <div class="timeline">
                    @foreach($order->trackings->sortByDesc('created_at') as $tracking)
                    <div class="timeline-item">
                        <div class="timeline-marker" style="background: 
                            @if($tracking->status == 'pending') linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)
                            @elseif($tracking->status == 'preparing') linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)
                            @elseif($tracking->status == 'ready') linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%)
                            @elseif($tracking->status == 'served') linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%)
                            @elseif($tracking->status == 'completed') linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)
                            @elseif($tracking->status == 'cancelled') linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)
                            @else linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%) @endif;
                            border: 3px solid 
                            @if($tracking->status == 'pending') #f59e0b
                            @elseif($tracking->status == 'preparing') #3b82f6
                            @elseif($tracking->status == 'ready') #8b5cf6
                            @elseif($tracking->status == 'served') #6366f1
                            @elseif($tracking->status == 'completed') #10b981
                            @elseif($tracking->status == 'cancelled') #ef4444
                            @else #9ca3af @endif;">
                            <i class="fas fa-
                                @if($tracking->status == 'pending') clock
                                @elseif($tracking->status == 'preparing') utensils
                                @elseif($tracking->status == 'ready') bell
                                @elseif($tracking->status == 'served') utensils
                                @elseif($tracking->status == 'completed') check-double
                                @elseif($tracking->status == 'cancelled') times-circle
                                @else info-circle @endif" 
                               style="color: 
                                @if($tracking->status == 'pending') #d97706
                                @elseif($tracking->status == 'preparing') #3b82f6
                                @elseif($tracking->status == 'ready') #8b5cf6
                                @elseif($tracking->status == 'served') #6366f1
                                @elseif($tracking->status == 'completed') #10b981
                                @elseif($tracking->status == 'cancelled') #ef4444
                                @else #6b7280 @endif;"></i>
                        </div>
                        <div style="background: white; border-radius: 12px; padding: 16px; border: 2px solid #f3f4f6;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h4 style="margin: 0; font-size: 18px; font-weight: 700; text-transform: capitalize; color: #1f2937;">
                                    {{ str_replace('_', ' ', $tracking->status) }}
                                </h4>
                                <span style="font-size: 12px; color: #6b7280; font-weight: 600;">{{ $tracking->created_at->format('M d, h:i A') }}</span>
                            </div>
                            @if($tracking->notes)
                                <p style="margin: 0 0 8px 0; font-size: 14px; color: #6b7280; line-height: 1.5;">{{ $tracking->notes }}</p>
                            @endif
                            @if($tracking->updated_by_user)
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #9ca3af;">
                                    <i class="fas fa-user-circle"></i>
                                    <span>Updated by: <strong>{{ $tracking->updatedBy->name ?? 'System' }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Order Items -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="info-card-title">Order Items</h3>
                </div>
                @if($order->items && $order->items->count() > 0)
                    <div class="order-items-list">
                        @foreach($order->items as $item)
                        <div class="order-item">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 20px;">
                                <div style="flex: 1;">
                                    <h4 style="font-size: 18px; font-weight: 700; margin: 0 0 8px 0; color: #1f2937;">{{ $item->menuItem->name ?? 'Item #' . $item->id }}</h4>
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                        <span style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4338ca; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; border: 2px solid #6366f1;">
                                            <i class="fas fa-shopping-cart" style="margin-right: 4px;"></i>
                                            Qty: {{ $item->quantity ?? 1 }}
                                        </span>
                                        <span style="font-size: 14px; color: #6b7280; font-weight: 600;">@ RM {{ number_format($item->unit_price ?? 0, 2) }}</span>
                                    </div>
                                    @if($item->special_note)
                                        <div style="margin-top: 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                            <div style="display: flex; align-items: start; gap: 8px;">
                                                <i class="fas fa-sticky-note" style="color: #d97706; font-size: 14px; margin-top: 2px;"></i>
                                                <span style="color: #92400e; font-weight: 600; font-size: 14px; flex: 1;">{{ $item->special_note }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    @php
                                        $addons = $item->customizations()->where('customization_type', 'addon')->get();
                                    @endphp
                                    @if($addons->count() > 0)
                                        <div style="margin-top: 12px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 12px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                                            <div style="display: flex; align-items: start; gap: 8px;">
                                                <i class="fas fa-puzzle-piece" style="color: #2563eb; font-size: 14px; margin-top: 2px;"></i>
                                                <div style="flex: 1;">
                                                    <div style="color: #1e40af; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Add-ons:</div>
                                                    <span style="color: #1e3a8a; font-size: 14px;">{{ $addons->pluck('customization_value')->join(', ') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div style="text-align: right; min-width: 120px;">
                                    <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 4px;">SUBTOTAL</div>
                                    <div style="font-size: 24px; font-weight: 700; color: #10b981;">RM {{ number_format($item->total_price ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <div style="padding: 24px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-top: 3px solid #10b981;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 14px; color: #065f46; font-weight: 600; margin-bottom: 4px;">GRAND TOTAL</div>
                                    <div style="font-size: 12px; color: #059669;">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</div>
                                </div>
                                <span style="font-size: 32px; font-weight: 800; color: #065f46; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">RM {{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                        <i class="fas fa-shopping-cart" style="font-size: 72px; margin-bottom: 20px; opacity: 0.3;"></i>
                        <div style="font-weight: 700; font-size: 20px; margin-bottom: 8px; color: #6b7280;">No Items Found</div>
                        <div style="font-size: 14px;">This order doesn't have any items yet</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
async function updateOrderStatus(status) {
    const confirmed = await showConfirm(
        'Update Order Status?',
        `Are you sure you want to change the order status to '${status}'?`,
        'warning',
        'Update',
        'Cancel'
    );

    if (!confirmed) {
        return;
    }

    fetch(`{{ route('admin.order.updateStatus', $order->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            Toast.error('Error', 'Error updating order status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.error('Error', 'Error updating order status');
    });
}

function updatePaymentStatus(status) {
    fetch(`{{ route('admin.order.updatePaymentStatus', $order->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            Toast.error('Error', 'Error updating payment status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.error('Error', 'Error updating payment status');
    });
}

async function confirmCancelOrder(orderId) {
    const confirmed = await showConfirm(
        'Cancel Order?',
        'Are you sure you want to cancel this order? This action cannot be undone.',
        'danger',
        'Cancel Order',
        'Keep Order'
    );

    if (!confirmed) {
        return;
    }

    fetch(`{{ route('admin.order.cancel', $order->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toast.success('Success', 'Order cancelled successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            Toast.error('Error', data.message || 'Failed to cancel order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.error('Error', 'Failed to cancel order');
    });
}
</script>
@endsection