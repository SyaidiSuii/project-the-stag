@extends('layouts.admin')

@section('title', isset($template) ? 'Edit Voucher Template' : 'Create Voucher Template')
@section('page-title', isset($template) ? 'Edit Voucher Template' : 'Create New Voucher Template')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<div class="form-container">
    <form action="{{ isset($template) ? route('admin.rewards.voucher-templates.update', $template->id) : route('admin.rewards.voucher-templates.store') }}" method="POST">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif

        <div class="form-section">
            <h3 class="form-section-title">Template Information</h3>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Template Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input"
                           value="{{ old('name', $template->name ?? '') }}"
                           placeholder="e.g., 10% Off Voucher" required>
                    @error('name')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount_type" class="form-label">Discount Type <span class="required">*</span></label>
                    <select id="discount_type" name="discount_type" class="form-select" required>
                        <option value="percentage" {{ old('discount_type', $template->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('discount_type', $template->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                    @error('discount_type')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount_value" class="form-label">Discount Value <span class="required">*</span></label>
                    <input type="number" id="discount_value" name="discount_value" class="form-input"
                           value="{{ old('discount_value', $template->discount_value ?? '') }}"
                           placeholder="e.g., 10 or 5.00" step="0.01" min="0" required>
                    <small class="form-help">Enter percentage (e.g., 10) or amount (e.g., 5.00)</small>
                    @error('discount_value')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="expiry_days" class="form-label">Expiry Days <span class="required">*</span></label>
                    <input type="number" id="expiry_days" name="expiry_days" class="form-input"
                           value="{{ old('expiry_days', $template->expiry_days ?? 30) }}"
                           placeholder="e.g., 30" min="1" required>
                    <small class="form-help">Days until voucher expires after generation</small>
                    @error('expiry_days')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($template) ? 'Update Template' : 'Create Template' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
