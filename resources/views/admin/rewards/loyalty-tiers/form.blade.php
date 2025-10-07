@extends('layouts.admin')
@section('title', isset($tier) ? 'Edit Tier' : 'Create Tier')
@section('page-title', isset($tier) ? 'Edit Tier' : 'Create Tier')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="form-container">
    <form action="{{ isset($tier) ? route('admin.rewards.loyalty-tiers.update', $tier->id) : route('admin.rewards.loyalty-tiers.store') }}" method="POST">
        @csrf @if(isset($tier)) @method('PUT') @endif
        <div class="form-section">
            <h3 class="form-section-title">Tier Information</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Tier Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $tier->name ?? '') }}" required>
                </div>
                <div class="form-group full-width">
                    <label for="benefits" class="form-label">Benefits</label>
                    <textarea id="benefits" name="benefits" class="form-textarea">{{ old('benefits', $tier->benefits ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="minimum_spending" class="form-label">Minimum Spending <span class="required">*</span></label>
                    <input type="number" id="minimum_spending" name="minimum_spending" class="form-input" value="{{ old('minimum_spending', $tier->minimum_spending ?? '') }}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="points_multiplier" class="form-label">Points Multiplier <span class="required">*</span></label>
                    <input type="number" id="points_multiplier" name="points_multiplier" class="form-input" value="{{ old('points_multiplier', $tier->points_multiplier ?? 1.00) }}" step="0.01" min="1" required>
                </div>
                <div class="form-group">
                    <label for="icon" class="form-label">Icon</label>
                    <input type="text" id="icon" name="icon" class="form-input" value="{{ old('icon', $tier->icon ?? '') }}">
                </div>
                <div class="form-group">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input" value="{{ old('sort_order', $tier->sort_order ?? 0) }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tier->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:14px;color:#64748b">Active</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary"><i class="fas fa-save"></i> {{ isset($tier) ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.rewards.loyalty-tiers.index') }}" class="admin-btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
@endsection
