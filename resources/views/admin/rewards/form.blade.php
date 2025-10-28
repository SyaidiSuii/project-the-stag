@extends('layouts.admin')

@section('title', isset($reward) ? 'Edit Reward' : 'Add New Reward')
@section('page-title', isset($reward) ? 'Edit Reward' : 'Add New Reward')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ isset($reward) ? 'Edit Reward' : 'Add New Reward' }}</h2>
        <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rewards
        </a>
    </div>

    <form class="user-form" method="POST" action="{{ isset($reward) ? route('admin.rewards.update', $reward->id) : route('admin.rewards.store') }}">
        @csrf
        @if(isset($reward))
            @method('PUT')
        @endif

        <div class="form-row">
            <div class="form-group">
                <label for="name">Reward Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $reward->name ?? '') }}"
                       required
                       class="@error('name') error @enderror">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="reward_type">Reward Type *</label>
                <select id="reward_type"
                        name="reward_type"
                        required
                        class="@error('reward_type') error @enderror">
                    <option value="">Select Type</option>
                    <option value="points" {{ old('reward_type', $reward->reward_type ?? '') == 'points' ? 'selected' : '' }}>Points</option>
                    <option value="voucher" {{ old('reward_type', $reward->reward_type ?? '') == 'voucher' ? 'selected' : '' }}>Voucher</option>
                    <option value="tier_upgrade" {{ old('reward_type', $reward->reward_type ?? '') == 'tier_upgrade' ? 'selected' : '' }}>Tier Upgrade</option>
                </select>
                @error('reward_type')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="points_required">Points Required</label>
                <input type="number" 
                       id="points_required" 
                       name="points_required" 
                       value="{{ old('points_required', $reward->points_required ?? '') }}" 
                       min="1"
                       class="@error('points_required') error @enderror">
                @error('points_required')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" id="voucherTemplateGroup" style="display: none;">
                <label for="voucher_template_id">Voucher Template</label>
                <select id="voucher_template_id" 
                        name="voucher_template_id"
                        class="@error('voucher_template_id') error @enderror">
                    <option value="">Select Template</option>
                    @foreach($voucherTemplates ?? [] as $template)
                        <option value="{{ $template->id }}" {{ old('voucher_template_id', $reward->voucher_template_id ?? '') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} ({{ $template->discount_type === 'percentage' ? $template->discount_value . '%' : 'RM' . $template->discount_value }})
                        </option>
                    @endforeach
                </select>
                @error('voucher_template_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="@error('description') error @enderror">{{ old('description', $reward->description ?? '') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="expiry_days">Expiry Days (after claim)</label>
                <input type="number" 
                       id="expiry_days" 
                       name="expiry_days" 
                       value="{{ old('expiry_days', $reward->expiry_days ?? '') }}" 
                       min="1"
                       placeholder="Optional - leave blank for no expiry"
                       class="@error('expiry_days') error @enderror">
                @error('expiry_days')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-group">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', $reward->is_active ?? true) ? 'checked' : '' }}>
                    <span class="checkbox-label">Active</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary btn-save">
                <i class="fas fa-save"></i>
                {{ isset($reward) ? 'Update Reward' : 'Create Reward' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rewardTypeSelect = document.getElementById('reward_type');
    const voucherTemplateGroup = document.getElementById('voucherTemplateGroup');

    function handleRewardTypeChange() {
        const rewardType = rewardTypeSelect.value;
        if (rewardType === 'voucher') {
            voucherTemplateGroup.style.display = 'block';
        } else {
            voucherTemplateGroup.style.display = 'none';
        }
    }

    // Handle initial state
    handleRewardTypeChange();

    // Handle changes
    rewardTypeSelect.addEventListener('change', handleRewardTypeChange);

    // Handle form submission with loading state and notifications
    const rewardForm = document.querySelector('.user-form');
    if (rewardForm) {
        rewardForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-save');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
        });
    }
    
    // Check for success/error messages from session
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
});

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection