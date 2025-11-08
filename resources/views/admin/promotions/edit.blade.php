@extends('layouts.admin')

@section('title', 'Edit Promotion')
@section('page-title', 'Edit Promotion')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.type-badge-display {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50px;
    color: white;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 24px;
}

.type-badge-display i {
    font-size: 18px;
}

.item-selector {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    max-height: 300px;
    overflow-y: auto;
}

.item-checkbox {
    display: flex;
    align-items: center;
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 4px;
}

.item-checkbox:hover {
    background: #f9fafb;
}

.item-checkbox input {
    margin-right: 8px;
}

.current-image-preview {
    max-width: 300px;
    max-height: 200px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    margin: 10px 0;
}

/* ===== RESPONSIVE DESIGN - 4-TIER BREAKPOINT SYSTEM ===== */

/* Large Desktop (≥1600px) */
@media (min-width: 1600px) {
    .admin-section {
        padding: 32px;
    }
    .section-title {
        font-size: 24px;
    }
    .type-badge-display {
        padding: 14px 28px;
        font-size: 16px;
    }
    .form-label {
        font-size: 16px;
    }
    .form-control {
        padding: 14px 18px;
        font-size: 15px;
    }
}

/* Tablet (769px-1199px) */
@media (max-width: 1199px) and (min-width: 769px) {
    .admin-section {
        padding: 20px;
    }
    .section-title {
        font-size: 16px;
    }
    .type-badge-display {
        padding: 10px 20px;
        font-size: 13px;
    }
    .type-badge-display i {
        font-size: 16px;
    }
    .form-row {
        gap: 12px;
    }
    .form-label {
        font-size: 13px;
    }
    .form-control {
        padding: 10px 14px;
        font-size: 13px;
    }
    .btn-save,
    .btn-cancel {
        padding: 10px 20px;
        font-size: 13px;
    }
    .current-image-preview {
        max-width: 250px;
        max-height: 150px;
    }
}

/* Mobile (≤768px) */
@media (max-width: 768px) {
    .admin-section {
        padding: 16px;
    }
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    .section-title {
        font-size: 15px;
    }
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
    .type-badge-display {
        padding: 10px 18px;
        font-size: 12px;
        margin-bottom: 16px;
    }
    .type-badge-display i {
        font-size: 14px;
    }
    .form-section h3 {
        font-size: 14px;
        margin-bottom: 12px;
    }
    .form-row {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        font-size: 13px;
        margin-bottom: 6px;
    }
    .form-control {
        padding: 10px 14px;
        font-size: 13px;
    }
    .form-control small,
    small {
        font-size: 11px !important;
    }
    .current-image-preview {
        max-width: 100%;
        max-height: 180px;
    }
    .item-selector {
        max-height: 250px;
        padding: 10px;
    }
    .item-checkbox {
        padding: 6px;
        font-size: 12px;
    }
    .form-actions {
        flex-direction: column;
        gap: 10px;
    }
    .btn-save,
    .btn-cancel {
        width: 100%;
        padding: 12px 16px;
        font-size: 13px;
        justify-content: center;
    }
    .admin-section > div[style*="background: #fee2e2"] {
        padding: 12px !important;
        font-size: 13px !important;
    }
}

/* Small Mobile (≤480px) */
@media (max-width: 480px) {
    .admin-section {
        padding: 12px;
    }
    .section-title {
        font-size: 14px;
    }
    .type-badge-display {
        padding: 8px 14px;
        font-size: 11px;
    }
    .type-badge-display i {
        font-size: 12px;
    }
    .form-section h3 {
        font-size: 13px;
    }
    .form-label {
        font-size: 12px;
    }
    .form-control {
        padding: 8px 12px;
        font-size: 12px;
    }
    .btn-save,
    .btn-cancel {
        padding: 10px 14px;
        font-size: 12px;
    }
    .current-image-preview {
        max-height: 150px;
    }
    .item-checkbox {
        font-size: 11px;
    }
}
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Edit Promotion: {{ $promotion->name }}</h2>
        <a href="{{ route('admin.promotions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Promotions
        </a>
    </div>

    @if(session('error'))
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="color: #dc2626;">Error:</strong>
            <p style="color: #991b1b; margin-top: 4px;">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="color: #dc2626;">Validation Errors:</strong>
            <ul style="margin-top: 8px; padding-left: 20px; color: #991b1b;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST" enctype="multipart/form-data" class="user-form" id="promotionForm">
        @csrf
        @method('PUT')

        {{-- Display Current Promotion Type --}}
        <div class="form-section">
            <h3 style="margin-bottom: 16px; color: var(--text);">Promotion Type</h3>
            <div class="type-badge-display">
                @if($promotion->promotion_type === 'promo_code')
                    <i class="fas fa-ticket-alt"></i> Promo Code
                @elseif($promotion->promotion_type === 'combo_deal')
                    <i class="fas fa-layer-group"></i> Combo Deal
                @elseif($promotion->promotion_type === 'item_discount')
                    <i class="fas fa-percent"></i> Item Discount
                @elseif($promotion->promotion_type === 'buy_x_free_y')
                    <i class="fas fa-gift"></i> Buy X Free Y
                @elseif($promotion->promotion_type === 'bundle')
                    <i class="fas fa-box-open"></i> Bundle
                @elseif($promotion->promotion_type === 'seasonal')
                    <i class="fas fa-calendar-alt"></i> Seasonal
                @endif
            </div>
            <input type="hidden" name="promotion_type" value="{{ $promotion->promotion_type }}">
            <small style="color: #6b7280; font-size: 0.85rem;">Promotion type cannot be changed after creation</small>
        </div>

        {{-- Basic Information --}}
        <div class="form-section">
            <h3 style="margin-bottom: 16px; color: var(--text);">Basic Information</h3>

            <div class="form-group">
                <label for="name" class="form-label">Promotion Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $promotion->name) }}" placeholder="e.g., Weekend Special, Summer Sale" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Describe this promotion">{{ old('description', $promotion->description ?? '') }}</textarea>
            </div>

            @if(in_array($promotion->promotion_type, ['combo_deal', 'bundle', 'seasonal']))
                <div class="form-group">
                    <label for="banner_image" class="form-label">Banner Image</label>

                    @if($promotion->banner_image)
                        <div style="margin-bottom: 10px;">
                            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 8px;">Current Banner:</p>
                            <img src="{{ asset('storage/' . $promotion->banner_image) }}" alt="Current banner" class="current-image-preview">
                        </div>
                    @endif

                    <input type="file" id="banner_image" name="banner_image" class="form-control" accept="image/*" onchange="previewImage(event, 'bannerPreview')">
                    <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">
                        Large banner for promotion (Maximum: 2MB)
                        @if($promotion->banner_image)
                            <br>Upload a new image to replace the current one
                        @endif
                    </small>
                    <div id="bannerImagePreview" style="display: none; margin-top: 10px;">
                        <p style="color: #059669; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">New Banner Preview:</p>
                        <img id="bannerPreview" src="" alt="Preview" class="current-image-preview" style="border-color: #10b981;">
                    </div>
                </div>
            @else
                <div class="form-group">
                    <label for="image" class="form-label">Promotion Image</label>

                    @if($promotion->image_path)
                        <div style="margin-bottom: 10px;">
                            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 8px;">Current Image:</p>
                            <img src="{{ asset('storage/' . $promotion->image_path) }}" alt="Current image" class="current-image-preview">
                        </div>
                    @endif

                    <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(event, 'preview')">
                    <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">
                        Maximum file size: 2MB
                        @if($promotion->image_path)
                            <br>Upload a new image to replace the current one
                        @endif
                    </small>
                    <div id="imagePreview" style="display: none; margin-top: 10px;">
                        <p style="color: #059669; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">New Image Preview:</p>
                        <img id="preview" src="" alt="Preview" class="current-image-preview" style="border-color: #10b981;">
                    </div>
                </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date *</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-group">
                <div class="role-checkbox">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Activate this promotion</label>
                </div>
            </div>
        </div>

        {{-- Type-Specific Fields (Partials) --}}
        @if($promotion->promotion_type === 'promo_code')
            @include('admin.promotions.partials.edit-promo-code')
        @elseif($promotion->promotion_type === 'combo_deal')
            @include('admin.promotions.partials.edit-combo-deal')
        @elseif($promotion->promotion_type === 'bundle')
            @include('admin.promotions.partials.edit-bundle')
        @elseif($promotion->promotion_type === 'item_discount')
            @include('admin.promotions.partials.edit-item-discount')
        @elseif($promotion->promotion_type === 'buy_x_free_y')
            @include('admin.promotions.partials.edit-buy-x-free-y')
        @elseif($promotion->promotion_type === 'seasonal')
            @include('admin.promotions.partials.edit-seasonal')
        @endif

        {{-- Usage Limits & Restrictions --}}
        @include('admin.promotions.partials.usage-limits', ['promotion' => $promotion])

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Update Promotion
            </button>
            <a href="{{ route('admin.promotions.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/promotion-edit.js') }}"></script>
<script>
var menuItemsData = {!! json_encode($menuItems->values()->toArray()) !!};
initializeMenuItems(menuItemsData);
setIndexes(2, 2);
</script>
@endsection
