@extends('layouts.admin')

@section('title', 'QR Code Session Details')
@section('page-title', 'QR Code Session Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
<style>
    /* Modern Card Container */
    .qr-detail-container {
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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .qr-detail-container .action-grid .action-btn,
    .qr-detail-container .action-grid a.action-btn,
    .qr-detail-container .action-grid button.action-btn {
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

    /* Smaller Back Button */
    .qr-detail-container .action-grid .action-btn.back-btn,
    .qr-detail-container .action-grid a.action-btn.back-btn {
        padding: 16px 20px !important;
        min-height: 70px !important;
        border: 2px solid #667eea !important;
        color: #667eea !important;
    }

    .qr-detail-container .action-grid .action-btn.back-btn:hover {
        background: #f3f4ff !important;
        border-color: #5568d3 !important;
    }

    .qr-detail-container .action-grid .action-btn.back-btn .action-btn-icon {
        font-size: 24px !important;
        margin-bottom: 8px !important;
    }

    .qr-detail-container .action-grid .action-btn.back-btn .action-btn-text {
        font-size: 14px !important;
        color: #667eea !important;
    }

    .qr-detail-container .action-grid .action-btn:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #667eea !important;
        background: #f9fafb !important;
    }

    /* Button Colors */
    .qr-detail-container .action-grid .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3) !important;
    }

    .qr-detail-container .action-grid .action-btn.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        border: none !important;
    }

    .qr-detail-container .action-grid .action-btn.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        border: none !important;
    }

    .qr-detail-container .action-grid .action-btn.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        color: white !important;
        border: none !important;
    }

    .qr-detail-container .action-grid .action-btn.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
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

    .qr-detail-container .action-grid .action-btn.primary .action-btn-icon,
    .qr-detail-container .action-grid .action-btn.primary .action-btn-text,
    .qr-detail-container .action-grid .action-btn.success .action-btn-icon,
    .qr-detail-container .action-grid .action-btn.success .action-btn-text,
    .qr-detail-container .action-grid .action-btn.warning .action-btn-icon,
    .qr-detail-container .action-grid .action-btn.warning .action-btn-text,
    .qr-detail-container .action-grid .action-btn.purple .action-btn-icon,
    .qr-detail-container .action-grid .action-btn.purple .action-btn-text,
    .qr-detail-container .action-grid .action-btn.info .action-btn-icon,
    .qr-detail-container .action-grid .action-btn.info .action-btn-text {
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

    /* QR Code Card */
    .qr-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        position: sticky;
        top: 24px;
        height: fit-content;
    }

    .qr-wrapper {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 1;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qr-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        padding: 20px;
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

    /* Responsive */
    @media (max-width: 768px) {
        .modern-header h1 {
            font-size: 28px;
        }
        .action-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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

    .info-card {
        animation: fadeIn 0.5s ease forwards;
    }

    .info-card:nth-child(1) { animation-delay: 0.1s; }
    .info-card:nth-child(2) { animation-delay: 0.2s; }
    .info-card:nth-child(3) { animation-delay: 0.3s; }
    .info-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection

@section('content')
<div class="qr-detail-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <h1>QR Session #{{ $tableQrcode->id }}</h1>
        <div class="header-meta">
            <div class="header-badge">
                <i class="fas fa-qrcode"></i>
                <span>{{ $tableQrcode->session_code }}</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-table"></i>
                <span>Table {{ $tableQrcode->table->table_number ?? 'N/A' }}</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-clock"></i>
                <span>{{ $tableQrcode->duration }} min duration</span>
            </div>
            <div class="header-badge" style="background: 
                @if($tableQrcode->status == 'active') linear-gradient(135deg, #10b981 0%, #059669 100%)
                @elseif($tableQrcode->status == 'completed') linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)
                @else linear-gradient(135deg, #ef4444 0%, #dc2626 100%) @endif;">
                <i class="fas fa-circle" style="font-size: 10px;"></i>
                <span>{{ ucfirst($tableQrcode->status) }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="action-grid">
        @if($tableQrcode->status === 'active')
            <a href="{{ route('admin.table-qrcodes.qr-preview', [$tableQrcode, 'png']) }}" class="action-btn primary" target="_blank">
                <i class="fas fa-qrcode action-btn-icon"></i>
                <span class="action-btn-text">View QR Code</span>
            </a>

            <a href="{{ route('admin.table-qrcodes.print', $tableQrcode) }}" class="action-btn purple" target="_blank">
                <i class="fas fa-print action-btn-icon"></i>
                <span class="action-btn-text">Print QR</span>
            </a>

            <a href="{{ route('admin.table-qrcodes.download-qr', [$tableQrcode, 'png']) }}" class="action-btn info">
                <i class="fas fa-download action-btn-icon"></i>
                <span class="action-btn-text">Download PNG</span>
            </a>

            <button onclick="completeSession()" class="action-btn success">
                <i class="fas fa-check-circle action-btn-icon"></i>
                <span class="action-btn-text">Complete Session</span>
            </button>

            <button onclick="extendSession()" class="action-btn warning">
                <i class="fas fa-clock action-btn-icon"></i>
                <span class="action-btn-text">Extend Session</span>
            </button>

            <button onclick="regenerateQR()" class="action-btn purple">
                <i class="fas fa-sync action-btn-icon"></i>
                <span class="action-btn-text">Regenerate QR</span>
            </button>
        @endif

        <a href="{{ route('admin.table-qrcodes.index') }}" class="action-btn back-btn">
            <i class="fas fa-arrow-left action-btn-icon"></i>
            <span class="action-btn-text">Back to List</span>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column - QR Code Card & Guest Info -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="qr-card">
            @if($tableQrcode->status === 'active' && $tableQrcode->qr_code_png)
            <div class="qr-wrapper">
                <img src="{{ asset('storage/' . $tableQrcode->qr_code_png) }}" 
                     alt="QR Code for {{ $tableQrcode->session_code }}">
            </div>
            @else
            <div class="qr-wrapper" style="padding: 40px;">
                <div style="text-align: center; color: #9ca3af;">
                    <i class="fas fa-qrcode" style="font-size: 64px; margin-bottom: 12px; opacity: 0.3;"></i>
                    <div style="font-weight: 600;">
                        @if($tableQrcode->status !== 'active')
                            QR Code Inactive
                        @else
                            QR Code Not Generated
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Status Badges in QR Card -->
            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 12px;">
                <div style="background: 
                    @if($tableQrcode->status == 'active') linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46
                    @elseif($tableQrcode->status == 'completed') linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #3730a3
                    @else linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b @endif;
                    padding: 12px 20px; border-radius: 12px; font-weight: 700; text-align: center; border: 2px solid
                    @if($tableQrcode->status == 'active') #10b981
                    @elseif($tableQrcode->status == 'completed') #6366f1
                    @else #ef4444 @endif;">
                    <i class="fas fa-
                        @if($tableQrcode->status == 'active') play-circle
                        @elseif($tableQrcode->status == 'completed') check-circle
                        @else times-circle @endif"></i>
                    {{ ucfirst($tableQrcode->status) }}
                </div>

                @if($tableQrcode->status === 'active')
                <div style="background: 
                    @if($tableQrcode->isExpired()) linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 2px solid #ef4444
                    @elseif($tableQrcode->time_remaining && $tableQrcode->time_remaining < 30) linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border: 2px solid #f59e0b
                    @else linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 2px solid #10b981 @endif;
                    padding: 12px 20px; border-radius: 12px; font-weight: 700; text-align: center;">
                    <i class="fas fa-clock"></i>
                    @if($tableQrcode->isExpired())
                        Expired
                    @elseif($tableQrcode->time_remaining)
                        {{ $tableQrcode->time_remaining }} min left
                    @else
                        Active
                    @endif
                </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #f3f4f6;">
                <div class="stat-item" style="margin-bottom: 12px;">
                    <div class="stat-label">Session Code</div>
                    <div class="stat-value" style="font-family: monospace; font-size: 18px;">{{ $tableQrcode->session_code }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Duration</div>
                    <div class="stat-value" style="font-size: 20px;">{{ $tableQrcode->duration }} min</div>
                </div>
                @if($tableQrcode->status === 'active')
                <div style="margin-top: 16px; padding: 16px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 12px; border: 2px solid #3b82f6;">
                    <div class="stat-label" style="color: #1e40af; margin-bottom: 8px;">QR Code URL</div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        @php
                            $qrUrl = route('qr.menu') . '?session=' . $tableQrcode->session_code;
                        @endphp
                        <a href="{{ $qrUrl }}" target="_blank" style="color: #1e40af; text-decoration: none; word-break: break-all; font-size: 13px; font-weight: 500; flex: 1; min-width: 150px;">
                            {{ $qrUrl }}
                        </a>
                        <button onclick="copyToClipboard('{{ $qrUrl }}')" style="background: #3b82f6; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; white-space: nowrap; transition: all 0.2s ease;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Guest Information Card (in left column) -->
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="info-card-title">Guest Information</h3>
            </div>
            @if($tableQrcode->guest_name || $tableQrcode->guest_phone)
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @if($tableQrcode->guest_name)
                    <div>
                        <span class="stat-label">Guest Name</span>
                        <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">{{ $tableQrcode->guest_name }}</p>
                    </div>
                    @endif

                    @if($tableQrcode->guest_phone)
                    <div>
                        <span class="stat-label">Phone</span>
                        <p style="margin: 4px 0 0 0;">
                            <a href="tel:{{ $tableQrcode->guest_phone }}" style="color: #3b82f6; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-phone" style="margin-right: 4px;"></i>
                                {{ $tableQrcode->guest_phone }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($tableQrcode->guest_count)
                    <div>
                        <span class="stat-label">Guest Count</span>
                        <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $tableQrcode->guest_count }} people</p>
                    </div>
                    @endif
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #9ca3af;">
                    <i class="fas fa-user-slash" style="font-size: 56px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <div style="font-weight: 600; font-size: 18px; margin-bottom: 8px;">No guest data</div>
                    <div style="font-size: 14px;">Guest information not provided</div>
                </div>
            @endif
        </div>
    </div>

        <!-- Right Column - Info Section -->
        <div class="info-section">
            <!-- Session Summary Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="info-card-title">Session Summary</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Session ID</div>
                        <div class="stat-value" style="font-size: 18px;">#{{ $tableQrcode->id }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Table Number</div>
                        <div class="stat-value" style="color: #10b981; font-size: 20px;">{{ $tableQrcode->table->table_number ?? 'N/A' }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Created At</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $tableQrcode->started_at->format('M d, h:i A') }}</div>
                    </div>
                    @if($tableQrcode->completed_at)
                    <div class="stat-item" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-color: #10b981;">
                        <div class="stat-label" style="color: #065f46;">Completed</div>
                        <div class="stat-value" style="font-size: 14px; color: #10b981;">{{ $tableQrcode->completed_at->format('M d, h:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Table Information Card -->
            @if($tableQrcode->table)
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-table"></i>
                    </div>
                    <h3 class="info-card-title">Table Information</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Table Number</div>
                        <div class="stat-value" style="font-size: 20px; color: #10b981;">{{ $tableQrcode->table->table_number }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Table Type</div>
                        <div class="stat-value" style="font-size: 16px;">{{ ucfirst($tableQrcode->table->table_type) }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Capacity</div>
                        <div class="stat-value" style="font-size: 16px;">{{ $tableQrcode->table->capacity }} pax</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Table Status</div>
                        <div class="stat-value" style="font-size: 16px;">{{ ucfirst($tableQrcode->table->status) }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Reservation Information Card -->
            @if($tableQrcode->reservation)
            <div class="info-card" style="border: 2px solid #ec4899;">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="info-card-title">Related Reservation</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Reservation ID</div>
                        <div class="stat-value" style="font-family: monospace; font-size: 16px;">RES-{{ $tableQrcode->reservation->id }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Guest Name</div>
                        <div class="stat-value" style="font-size: 16px;">{{ $tableQrcode->reservation->guest_name }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Guest Phone</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $tableQrcode->reservation->guest_phone }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Party Size</div>
                        <div class="stat-value" style="font-size: 16px;">{{ $tableQrcode->reservation->party_size }} pax</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Special Notes Card -->
            @if($tableQrcode->notes)
            <div class="info-card" style="border: 2px solid #f59e0b;">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <h3 class="info-card-title">Special Notes</h3>
                </div>
                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 16px; border-radius: 12px; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; align-items: start; gap: 12px;">
                        <i class="fas fa-sticky-note" style="color: #d97706; font-size: 18px; margin-top: 2px;"></i>
                        <span style="color: #92400e; font-weight: 600; flex: 1;">{{ $tableQrcode->notes }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timing Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="info-card-title">Timing Information</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Session Started</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $tableQrcode->started_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $tableQrcode->started_at->format('h:i A') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Expires At</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $tableQrcode->expires_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $tableQrcode->expires_at->format('h:i A') }}</div>
                        @if($tableQrcode->isExpired())
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; margin-top: 8px; display: inline-block;">
                                <i class="fas fa-exclamation-triangle"></i> EXPIRED
                            </span>
                        @endif
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Duration</div>
                        <div class="stat-value" style="font-size: 18px; color: #6366f1;">{{ $tableQrcode->duration }} min</div>
                    </div>
                    @if($tableQrcode->completed_at)
                    <div class="stat-item" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-color: #10b981;">
                        <div class="stat-label" style="color: #065f46;">Completed At</div>
                        <div class="stat-value" style="font-size: 14px; color: #10b981;">{{ $tableQrcode->completed_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #059669; margin-top: 4px;">{{ $tableQrcode->completed_at->format('h:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Orders Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="info-card-title">Related Orders</h3>
                </div>
                @if($tableQrcode->orders && $tableQrcode->orders->count() > 0)
                    <div class="order-items-list">
                        @foreach($tableQrcode->orders as $order)
                        <div class="order-item">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 20px;">
                                <div style="flex: 1;">
                                    <h4 style="font-size: 18px; font-weight: 700; margin: 0 0 8px 0;">
                                        <a href="{{ route('admin.order.show', $order->id) }}" style="color: #3b82f6; text-decoration: none;">
                                            Order #{{ $order->id }}
                                        </a>
                                    </h4>
                                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">
                                        {{ $order->items->count() }} items • {{ $order->order_time->format('M d, Y h:i A') }}
                                    </div>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <span style="background: 
                                            @if($order->order_status == 'pending') linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border: 2px solid #f59e0b
                                            @elseif($order->order_status == 'preparing') linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: 2px solid #3b82f6
                                            @elseif($order->order_status == 'ready') linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); color: #6b21a8; border: 2px solid #8b5cf6
                                            @elseif($order->order_status == 'completed') linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 2px solid #10b981
                                            @else linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 2px solid #ef4444 @endif; 
                                            padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                        <span style="background: 
                                            @if($order->payment_status == 'paid') linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 2px solid #10b981
                                            @elseif($order->payment_status == 'partial') linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border: 2px solid #f59e0b
                                            @else linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 2px solid #ef4444 @endif; 
                                            padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div style="text-align: right; min-width: 100px;">
                                    <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 4px;">TOTAL</div>
                                    <div style="font-size: 20px; font-weight: 700; color: #10b981;">RM {{ number_format($order->total_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                        <i class="fas fa-shopping-cart" style="font-size: 72px; margin-bottom: 20px; opacity: 0.3;"></i>
                        <div style="font-weight: 700; font-size: 20px; margin-bottom: 8px; color: #6b7280;">No Orders Yet</div>
                        <div style="font-size: 14px;">No orders placed using this QR code</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Toast.success('Success!', 'URL copied to clipboard');
    }).catch(() => {
        Toast.error('Error', 'Failed to copy URL');
    });
}

function completeSession() {
    showConfirm(
        'Complete Session?',
        'This will mark the QR code session as complete and make the table available.',
        'info',
        'Complete',
        'Cancel'
    ).then(confirmed => {
        if (!confirmed) return;

        fetch(`{{ route('admin.table-qrcodes.complete', $tableQrcode) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success('Success!', 'Session completed successfully');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                Toast.error('Error', data.message || 'Failed to complete session');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to complete session. Please try again.');
        });
    });
}

function extendSession() {
    // Create a custom input modal
    const overlay = document.createElement('div');
    overlay.className = 'confirm-modal-overlay';
    overlay.innerHTML = `
        <div class="confirm-modal">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon info">⏱</div>
                <div class="confirm-modal-text">
                    <h3 class="confirm-modal-title">Extend Session</h3>
                    <p class="confirm-modal-message">Enter number of hours to extend (1-12)</p>
                </div>
            </div>
            <div style="padding: 0 24px 16px;">
                <input type="number" id="extendHours" min="1" max="12" value="2"
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px;">
            </div>
            <div class="confirm-modal-footer">
                <button class="confirm-modal-btn confirm-modal-btn-cancel">Cancel</button>
                <button class="confirm-modal-btn confirm-modal-btn-confirm info">Extend</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    const input = overlay.querySelector('#extendHours');
    const cancelBtn = overlay.querySelector('.confirm-modal-btn-cancel');
    const confirmBtn = overlay.querySelector('.confirm-modal-btn-confirm');

    const closeModal = () => {
        overlay.classList.add('hiding');
        setTimeout(() => overlay.remove(), 200);
    };

    cancelBtn.onclick = closeModal;
    overlay.onclick = (e) => { if (e.target === overlay) closeModal(); };

    confirmBtn.onclick = () => {
        const hours = parseInt(input.value);
        if (isNaN(hours) || hours < 1 || hours > 12) {
            Toast.warning('Invalid Input', 'Please enter a number between 1 and 12');
            return;
        }

        closeModal();

        fetch(`{{ route('admin.table-qrcodes.extend', $tableQrcode) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ hours })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success('Success!', `Session extended by ${hours} hour(s)`);
                setTimeout(() => location.reload(), 1500);
            } else {
                Toast.error('Error', data.message || 'Failed to extend session');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to extend session. Please try again.');
        });
    };

    input.focus();
    input.select();
}

function regenerateQR() {
    showConfirm(
        'Regenerate QR Code?',
        'The old QR code will no longer work. Are you sure you want to continue?',
        'warning',
        'Regenerate',
        'Cancel'
    ).then(confirmed => {
        if (!confirmed) return;

        fetch(`{{ route('admin.table-qrcodes.regenerate-qr', $tableQrcode) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success('Success!', 'QR code regenerated successfully');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                Toast.error('Error', data.message || 'Failed to regenerate QR code');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to regenerate QR code. Please try again.');
        });
    });
}
</script>
@endsection