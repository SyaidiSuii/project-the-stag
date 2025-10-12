@extends('layouts.admin')

@section('title', 'Edit Promotion')
@section('page-title', 'Edit Promotion')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Edit Promotion: {{ $promotion->name }}</h2>
        <a href="{{ route('admin.promotions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Promotions
        </a>
    </div>

    <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST" enctype="multipart/form-data" class="user-form">
        @csrf
        @method('PUT')

        <!-- Promotion Name -->
        <div class="form-group">
            <label for="name" class="form-label">Promotion Name *</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name', $promotion->name) }}"
                placeholder="e.g., Weekend Special, Summer Sale"
                required
            >
            @if($errors->get('name'))
                <div class="form-error">{{ implode(', ', $errors->get('name')) }}</div>
            @endif
        </div>

        <!-- Promotion Image/Banner -->
        <div class="form-group">
            <label for="image" class="form-label">Promotion Banner/Image</label>

            @if($promotion->image_path)
                <div style="margin-bottom: 10px;">
                    <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 8px;">Current Image:</p>
                    <img
                        src="{{ asset('storage/' . $promotion->image_path) }}"
                        alt="Current promotion image"
                        style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e5e7eb; display: block;"
                    >
                </div>
            @endif

            <input
                type="file"
                id="image"
                name="image"
                class="form-control"
                accept="image/jpeg,image/png,image/jpg,image/gif"
                onchange="previewImage(event)"
            >
            @if($errors->get('image'))
                <div class="form-error">{{ implode(', ', $errors->get('image')) }}</div>
            @endif
            <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">
                Maximum file size: 2MB. Accepted formats: JPEG, PNG, JPG, GIF
                @if($promotion->image_path)
                    <br>Upload a new image to replace the current one
                @endif
            </small>

            <!-- Image Preview -->
            <div id="imagePreview" style="display: none; margin-top: 10px;">
                <p style="color: #059669; font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">New Image Preview:</p>
                <img id="preview" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #10b981;">
            </div>
        </div>

        <!-- Promo Code -->
        <div class="form-group">
            <label for="promo_code" class="form-label">Promo Code</label>
            <input
                type="text"
                id="promo_code"
                name="promo_code"
                class="form-control"
                value="{{ old('promo_code', $promotion->promo_code) }}"
                placeholder="e.g., FESTIVE2025, SAVE20"
                style="text-transform: uppercase;"
            >
            @if($errors->get('promo_code'))
                <div class="form-error">{{ implode(', ', $errors->get('promo_code')) }}</div>
            @endif
            <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">
                Customers will use this code at checkout
            </small>
        </div>

        <!-- Discount Type & Value -->
        <div class="form-row">
            <div class="form-group">
                <label for="discount_type" class="form-label">Discount Type *</label>
                <select
                    id="discount_type"
                    name="discount_type"
                    class="form-control"
                    required
                    onchange="updateDiscountLabel()"
                >
                    <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
                </select>
                @if($errors->get('discount_type'))
                    <div class="form-error">{{ implode(', ', $errors->get('discount_type')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="discount_value" class="form-label">
                    <span id="discount_label">
                        Discount Value ({{ $promotion->discount_type == 'percentage' ? '%' : 'RM' }}) *
                    </span>
                </label>
                <input
                    type="number"
                    id="discount_value"
                    name="discount_value"
                    class="form-control"
                    value="{{ old('discount_value', $promotion->discount_value) }}"
                    step="0.01"
                    min="0"
                    required
                >
                @if($errors->get('discount_value'))
                    <div class="form-error">{{ implode(', ', $errors->get('discount_value')) }}</div>
                @endif
            </div>
        </div>

        <!-- Minimum Order Value -->
        <div class="form-group">
            <label for="minimum_order_value" class="form-label">Minimum Order Value (RM)</label>
            <input
                type="number"
                id="minimum_order_value"
                name="minimum_order_value"
                class="form-control"
                value="{{ old('minimum_order_value', $promotion->minimum_order_value) }}"
                step="0.01"
                min="0"
                placeholder="e.g., 50.00"
            >
            @if($errors->get('minimum_order_value'))
                <div class="form-error">{{ implode(', ', $errors->get('minimum_order_value')) }}</div>
            @endif
            <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px;">
                Leave empty for no minimum requirement
            </small>
        </div>

        <!-- Valid Period -->
        <div class="form-row">
            <div class="form-group">
                <label for="start_date" class="form-label">Start Date *</label>
                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    class="form-control"
                    value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}"
                    required
                >
                @if($errors->get('start_date'))
                    <div class="form-error">{{ implode(', ', $errors->get('start_date')) }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="end_date" class="form-label">End Date *</label>
                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    class="form-control"
                    value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}"
                    required
                >
                @if($errors->get('end_date'))
                    <div class="form-error">{{ implode(', ', $errors->get('end_date')) }}</div>
                @endif
            </div>
        </div>

        <!-- Active Status -->
        <div class="form-group">
            <div class="role-checkbox">
                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                >
                <label for="is_active">Promotion is active</label>
            </div>
            <small style="color: #6b7280; font-size: 0.85rem; display: block; margin-top: 4px; margin-left: 24px;">
                <i class="fas fa-info-circle"></i> Checked = Active (visible to customers)<br>
                <i class="fas fa-info-circle"></i> Unchecked = Inactive (hidden from customers)
            </small>
        </div>

        <!-- Form Actions -->
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
<script>
// Preview uploaded image
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
}

// Auto-uppercase promo code
document.getElementById('promo_code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Update discount label based on type
function updateDiscountLabel() {
    const type = document.getElementById('discount_type').value;
    const label = document.getElementById('discount_label');

    if (type === 'percentage') {
        label.textContent = 'Discount Value (%) *';
    } else {
        label.textContent = 'Discount Value (RM) *';
    }
}

// Set minimum end date to start date
document.getElementById('start_date').addEventListener('change', function() {
    const endDateInput = document.getElementById('end_date');
    endDateInput.min = this.value;

    if (endDateInput.value && endDateInput.value < this.value) {
        endDateInput.value = this.value;
    }
});
</script>
@endsection
