@extends('layouts.admin')

@section('title', 'Table Details')
@section('page-title', 'Table Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
<style>
    /* Modern Card Container */
    .table-detail-container {
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

    .table-detail-container .action-grid .action-btn,
    .table-detail-container .action-grid a.action-btn,
    .table-detail-container .action-grid button.action-btn {
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

    .table-detail-container .action-grid .action-btn:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #667eea !important;
        background: #f9fafb !important;
    }

    .table-detail-container .action-grid .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3) !important;
    }

    .table-detail-container .action-grid .action-btn.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
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

    .table-detail-container .action-grid .action-btn.primary .action-btn-icon,
    .table-detail-container .action-grid .action-btn.primary .action-btn-text,
    .table-detail-container .action-grid .action-btn.success .action-btn-icon,
    .table-detail-container .action-grid .action-btn.success .action-btn-text {
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

    /* Table Icon Card */
    .table-icon-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        position: sticky;
        top: 24px;
        height: fit-content;
    }

    .icon-wrapper {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 1;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .icon-wrapper i {
        font-size: 120px;
        opacity: 0.9;
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

    .status-badge.available {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 2px solid #10b981;
    }

    .status-badge.occupied {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 2px solid #f59e0b;
    }

    .status-badge.reserved {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        border: 2px solid #3b82f6;
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

    /* Amenity Grid */
    .amenity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .amenity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-radius: 12px;
        border: 2px solid #a7f3d0;
        transition: all 0.3s ease;
    }

    .amenity-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .amenity-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .amenity-label {
        font-size: 14px;
        font-weight: 600;
        color: #065f46;
    }
</style>
@endsection

@section('content')
<div class="table-detail-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <h1>{{ $table->table_number ?? 'T-03' }}</h1>
        <div class="header-meta">
            <div class="header-badge">
                <i class="fas fa-layer-group"></i>
                <span>{{ ucfirst($table->table_type ?? 'vip') }}</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-users"></i>
                <span>{{ $table->capacity ?? '4' }} people capacity</span>
            </div>
            <div class="header-badge">
                <i class="fas fa-hashtag"></i>
                <span>ID: {{ $table->id ?? 1 }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="action-grid">
        <a href="{{ route('admin.table.edit', $table->id ?? 1) }}" class="action-btn primary">
            <i class="fas fa-edit action-btn-icon"></i>
            <span class="action-btn-text">Edit Table</span>
        </a>

        <a href="{{ route('admin.table.index') }}" class="action-btn">
            <i class="fas fa-arrow-left action-btn-icon"></i>
            <span class="action-btn-text">Back to List</span>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column - Table Icon Card -->
        <div class="table-icon-card">
            <div class="icon-wrapper" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                <i class="fas fa-qrcode" style="color: #3b82f6;"></i>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <div style="font-weight: 700; color: #1e40af; margin-bottom: 8px; font-size: 18px;">No Active Session</div>
                <div style="color: #1e3a8a; font-size: 14px;">QR code will be generated when a new session is created.</div>
            </div>

            <!-- Status Badges in Icon Card -->
            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 12px;">
                <div class="status-badge {{ $table->status === 'available' ? 'available' : ($table->status === 'occupied' ? 'occupied' : 'reserved') }}">
                    <i class="fas fa-{{ $table->status === 'available' ? 'check-circle' : ($table->status === 'occupied' ? 'user' : 'calendar-check') }}"></i>
                    <span>{{ ucfirst($table->status ?? 'available') }}</span>
                </div>

                <div class="status-badge {{ ($table->is_active ?? true) ? 'active' : 'inactive' }}">
                    <i class="fas fa-{{ ($table->is_active ?? true) ? 'toggle-on' : 'toggle-off' }}"></i>
                    <span>{{ ($table->is_active ?? true) ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #f3f4f6;">
                <div class="stat-item" style="margin-bottom: 12px;">
                    <div class="stat-label">Table Number</div>
                    <div class="stat-value" style="color: #667eea;">{{ $table->table_number ?? 'T-03' }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Capacity</div>
                    <div class="stat-value" style="font-size: 20px;">{{ $table->capacity ?? '4' }}</div>
                </div>
            </div>
        </div>

        <!-- Right Column - Info Section -->
        <div class="info-section">
            <!-- Basic Information Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="info-card-title">Basic Information</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Table Number</div>
                        <div class="stat-value" style="font-size: 20px; color: #667eea;">{{ $table->table_number ?? 'T-03' }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Capacity</div>
                        <div class="stat-value" style="font-size: 18px;">{{ $table->capacity ?? '4' }} pax</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Type</div>
                        <div class="stat-value" style="font-size: 16px; text-transform: capitalize;">{{ $table->table_type ?? 'vip' }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Status</div>
                        <div class="stat-value" style="font-size: 16px; text-transform: capitalize;">{{ $table->status ?? 'available' }}</div>
                    </div>
                </div>
            </div>

            <!-- Technical Details Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="info-card-title">Technical Details</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-left: 4px solid #8b5cf6; padding: 20px; border-radius: 12px;">
                        <div class="stat-label" style="text-align: left; margin-bottom: 8px;">QR Code</div>
                        <div style="font-family: monospace; font-size: 18px; font-weight: 700; color: #1f2937;">
                            {{ $table->qr_code ?? 'QR_T03' }}
                        </div>
                    </div>

                    @if($table->nfc_tag ?? false)
                    <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-left: 4px solid #10b981; padding: 20px; border-radius: 12px;">
                        <div class="stat-label" style="text-align: left; margin-bottom: 8px;">NFC Tag</div>
                        <div style="font-family: monospace; font-size: 16px; font-weight: 600; color: #1f2937;">
                            {{ $table->nfc_tag }}
                        </div>
                    </div>
                    @else
                    <div style="text-align: center; padding: 24px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px;">
                        <i class="fas fa-info-circle" style="font-size: 32px; color: #d97706; margin-bottom: 8px;"></i>
                        <div style="color: #92400e; font-weight: 600; font-size: 14px;">NFC Tag not configured</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Amenities Card -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="info-card-title">Amenities</h3>
                </div>
                @php
                // Define all possible amenities with their labels and icons
                $allAmenities = [
                    'wifi_available' => ['label' => 'WiFi Available', 'icon' => 'wifi'],
                    'power_outlet' => ['label' => 'Power Outlet', 'icon' => 'plug'],
                    'window_view' => ['label' => 'Window View', 'icon' => 'eye'],
                    'air_conditioning' => ['label' => 'Air Conditioning', 'icon' => 'snowflake'],
                    'heating' => ['label' => 'Heating', 'icon' => 'fire'],
                    'wheelchair_accessible' => ['label' => 'Wheelchair Accessible', 'icon' => 'wheelchair'],
                    'high_chair_available' => ['label' => 'High Chair Available', 'icon' => 'baby'],
                    'privacy_screen' => ['label' => 'Privacy Screen', 'icon' => 'shield-alt'],
                    'soundproof' => ['label' => 'Soundproof', 'icon' => 'volume-mute'],
                    'tv_screen' => ['label' => 'TV Screen', 'icon' => 'tv']
                ];

                // Get only the amenities that are saved in the database
                $savedAmenities = is_array($table->amenities) ? $table->amenities : [];
                @endphp
                
                @if(count($savedAmenities) > 0)
                    <div class="amenity-grid">
                        @foreach($savedAmenities as $amenityKey)
                            @if(isset($allAmenities[$amenityKey]))
                            <div class="amenity-item">
                                <div class="amenity-icon">
                                    <i class="fas fa-{{ $allAmenities[$amenityKey]['icon'] }}"></i>
                                </div>
                                <span class="amenity-label">{{ $allAmenities[$amenityKey]['label'] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: #9ca3af;">
                        <i class="fas fa-info-circle" style="font-size: 56px; margin-bottom: 16px; opacity: 0.3;"></i>
                        <div style="font-weight: 600; font-size: 18px; margin-bottom: 8px;">No amenities selected</div>
                        <div style="font-size: 14px;">This table has no special amenities configured.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Table management scripts can be added here
</script>
@endsection