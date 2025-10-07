@extends('layouts.admin')

@section('title', isset($reward) ? 'Edit Reward' : 'Create Reward')
@section('page-title', isset($reward) ? 'Edit Reward' : 'Create New Reward')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<div class="form-container">
    <form action="{{ isset($reward) ? route('admin.rewards.rewards.update', $reward->id) : route('admin.rewards.rewards.store') }}" method="POST">
        @csrf
        @if(isset($reward))
            @method('PUT')
        @endif

        <div class="form-section">
            <h3 class="form-section-title">Reward Information</h3>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="reward-name" class="form-label">Reward Name <span class="required">*</span></label>
                    <input type="text" id="reward-name" name="title" class="form-input"
                           value="{{ old('title', $reward->title ?? '') }}"
                           placeholder="e.g., Free Coffee" required>
                    @error('title')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="reward-description" class="form-label">Description</label>
                    <textarea id="reward-description" name="description" class="form-textarea" rows="3"
                              placeholder="Describe the reward...">{{ old('description', $reward->description ?? '') }}</textarea>
                    @error('description')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reward-type" class="form-label">Reward Type <span class="required">*</span></label>
                    <select id="reward-type" name="reward_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="points" {{ old('reward_type', $reward->reward_type ?? '') == 'points' ? 'selected' : '' }}>Points</option>
                        <option value="voucher" {{ old('reward_type', $reward->reward_type ?? '') == 'voucher' ? 'selected' : '' }}>Voucher</option>
                        <option value="tier_upgrade" {{ old('reward_type', $reward->reward_type ?? '') == 'tier_upgrade' ? 'selected' : '' }}>Tier Upgrade</option>
                    </select>
                    @error('reward_type')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reward-points" class="form-label">Points Required <span class="required">*</span></label>
                    <input type="number" id="reward-points" name="points_required" class="form-input"
                           value="{{ old('points_required', $reward->points_required ?? '') }}"
                           placeholder="e.g., 100" min="0" required>
                    @error('points_required')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="voucher-template" class="form-label">Voucher Template (if type = voucher)</label>
                    <select id="voucher-template" name="voucher_template_id" class="form-select">
                        <option value="">None</option>
                        @foreach(\App\Models\VoucherTemplate::all() as $template)
                            <option value="{{ $template->id }}" {{ old('voucher_template_id', $reward->voucher_template_id ?? '') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('voucher_template_id')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="expiry-days" class="form-label">Expiry Days (after claim)</label>
                    <input type="number" id="expiry-days" name="expiry_days" class="form-input"
                           value="{{ old('expiry_days', $reward->expiry_days ?? '') }}"
                           placeholder="e.g., 30" min="1">
                    @error('expiry_days')
                        <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $reward->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:14px;color:#64748b">Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($reward) ? 'Update Reward' : 'Create Reward' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
@endsection
 