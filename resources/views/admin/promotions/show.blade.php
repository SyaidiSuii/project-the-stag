@extends('layouts.admin')

@section('title', 'Promotion Details')
@section('page-title', 'Promotion Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
.detail-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.detail-row {
    display: flex;
    padding: 16px 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #6b7280;
    width: 200px;
    flex-shrink: 0;
}

.detail-value {
    color: #1f2937;
    flex: 1;
}

.promotion-image {
    width: 100%;
    max-width: 600px;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
}

.no-image-placeholder {
    width: 100%;
    max-width: 600px;
    height: 300px;
    border-radius: 12px;
    border: 2px dashed #d1d5db;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f9fafb;
    margin: 20px 0;
}

.no-image-placeholder i {
    font-size: 64px;
    color: #9ca3af;
    margin-bottom: 12px;
}

.no-image-placeholder p {
    color: #6b7280;
    font-size: 14px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.status-badge.active {
    background: #dcfce7;
    color: #16a34a;
}

.status-badge.inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.discount-info {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 16px;
}

.discount-info.percentage {
    background: #dbeafe;
    color: #2563eb;
}

.discount-info.fixed {
    background: #fef3c7;
    color: #d97706;
}

.promo-code-display {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: #f3f4f6;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 18px;
    color: #1f2937;
    letter-spacing: 1px;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.action-buttons .admin-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-edit {
    background: #3b82f6;
    color: white;
}

.btn-edit:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-back {
    background: #6b7280;
    color: white;
}

.btn-back:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
    cursor: pointer;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
}
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Promotion Details</h2>
        <a href="{{ route('admin.promotions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Promotions
        </a>
    </div>

    <!-- Promotion Image Section -->
    <div class="detail-card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #1f2937;">
            <i class="fas fa-image"></i> Promotion Banner
        </h3>

        @if($promotion->image_path)
            <img
                src="{{ asset('storage/' . $promotion->image_path) }}"
                alt="{{ $promotion->name }}"
                class="promotion-image"
            >
        @else
            <div class="no-image-placeholder">
                <i class="fas fa-image"></i>
                <p>No banner image uploaded for this promotion</p>
            </div>
        @endif
    </div>

    <!-- Promotion Details Section -->
    <div class="detail-card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #1f2937;">
            <i class="fas fa-info-circle"></i> Promotion Information
        </h3>

        <div class="detail-row">
            <div class="detail-label">Promotion Name</div>
            <div class="detail-value" style="font-weight: 600; font-size: 18px;">{{ $promotion->name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Promo Code</div>
            <div class="detail-value">
                @if($promotion->promo_code)
                    <span class="promo-code-display">
                        <i class="fas fa-ticket-alt"></i>
                        {{ $promotion->promo_code }}
                    </span>
                @else
                    <span style="color: #9ca3af; font-style: italic;">No promo code</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Discount Type</div>
            <div class="detail-value">
                <span class="discount-info {{ $promotion->discount_type === 'percentage' ? 'percentage' : 'fixed' }}">
                    @if($promotion->discount_type === 'percentage')
                        <i class="fas fa-percent"></i>
                        {{ number_format($promotion->discount_value, 0) }}% OFF
                    @else
                        <i class="fas fa-money-bill-wave"></i>
                        RM {{ number_format($promotion->discount_value, 2) }} OFF
                    @endif
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Minimum Order Value</div>
            <div class="detail-value">
                @if($promotion->minimum_order_value)
                    <strong style="font-size: 16px;">RM {{ number_format($promotion->minimum_order_value, 2) }}</strong>
                @else
                    <span style="color: #9ca3af; font-style: italic;">No minimum order required</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Valid Period</div>
            <div class="detail-value">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div>
                        <div style="font-weight: 600; color: #16a34a;">
                            <i class="fas fa-calendar-check"></i> Start Date
                        </div>
                        <div style="font-size: 16px; margin-top: 4px;">{{ $promotion->start_date->format('F d, Y') }}</div>
                    </div>
                    <div style="color: #d1d5db; font-size: 20px;">→</div>
                    <div>
                        <div style="font-weight: 600; color: #dc2626;">
                            <i class="fas fa-calendar-times"></i> End Date
                        </div>
                        <div style="font-size: 16px; margin-top: 4px;">{{ $promotion->end_date->format('F d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span class="status-badge {{ $promotion->is_active ? 'active' : 'inactive' }}">
                    <i class="fas fa-{{ $promotion->is_active ? 'check-circle' : 'times-circle' }}"></i>
                    {{ $promotion->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Created At</div>
            <div class="detail-value">{{ $promotion->created_at->format('F d, Y \a\t h:i A') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Last Updated</div>
            <div class="detail-value">{{ $promotion->updated_at->format('F d, Y \a\t h:i A') }}</div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="admin-btn btn-edit">
            <i class="fas fa-edit"></i> Edit Promotion
        </a>

        <a href="{{ route('admin.promotions.index') }}" class="admin-btn btn-back">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>

        <form action="{{ route('admin.promotions.destroy', $promotion->id) }}"
              method="POST"
              style="display: inline;"
              onsubmit="return confirm('Are you sure you want to delete this promotion? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="admin-btn btn-delete">
                <i class="fas fa-trash"></i> Delete Promotion
            </button>
        </form>
    </div>
</div>
@endsection
