@extends('layouts.admin')

@section('title', isset($reward) ? 'Edit Reward' : 'Create Reward')

@section('styles')
<style>
    :root {
        --brand: #6366f1;
        --brand-2: #5856eb;
        --accent: #ff6b35;
        --bg: #f8fafc;
        --card: #ffffff;
        --muted: #e2e8f0;
        --text: #1e293b;
        --text-2: #64748b;
        --text-3: #94a3b8;
        --danger: #ef4444;
        --success: #10b981;
        --warning: #f59e0b;
        --radius: 12px;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .form-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
        background: var(--bg);
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 5px;
    }

    .form-header p {
        color: var(--text-2);
        font-size: 14px;
    }

    .form-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 30px;
        box-shadow: var(--shadow);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: var(--text);
        margin-bottom: 8px;
    }

    .form-group label .required {
        color: var(--danger);
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid var(--muted);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        background: var(--card);
        color: var(--text);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-group small {
        display: block;
        color: var(--text-3);
        margin-top: 5px;
        font-size: 12px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--muted);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: var(--brand);
        color: white;
    }

    .btn-primary:hover {
        background: var(--brand-2);
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    .btn-secondary {
        background: var(--muted);
        color: var(--text);
    }

    .btn-secondary:hover {
        background: var(--text-3);
        color: white;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
        accent-color: var(--brand);
    }

    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border: 1px solid var(--danger);
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>
            <i class="fas fa-gift"></i>
            {{ isset($reward) ? 'Edit Reward' : 'Create New Reward' }}
        </h1>
        <p>{{ isset($reward) ? 'Update reward details' : 'Create a new reward for your loyalty program' }}</p>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($reward) ? route('admin.rewards.rewards.update', $reward->id) : route('admin.rewards.rewards.store') }}" method="POST">
        @csrf
        @if(isset($reward))
            @method('PUT')
        @endif

        <div class="form-card">
            <!-- Basic Information -->
            <h3 style="margin-bottom: 20px; color: #333;">Basic Information</h3>

            <div class="form-group">
                <label for="title">Reward Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $reward->title ?? '') }}" required>
                <small>Clear and attractive reward name (e.g., "Free Coffee", "10% Discount")</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $reward->description ?? '') }}</textarea>
                <small>Detailed description of the reward</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="reward_type">Reward Type <span class="required">*</span></label>
                    <select id="reward_type" name="reward_type" required>
                        <option value="">Select type...</option>
                        <option value="voucher" {{ old('reward_type', $reward->reward_type ?? '') == 'voucher' ? 'selected' : '' }}>Voucher (Discount Code)</option>
                        <option value="product" {{ old('reward_type', $reward->reward_type ?? '') == 'product' ? 'selected' : '' }}>Free Product</option>
                        <option value="points" {{ old('reward_type', $reward->reward_type ?? '') == 'points' ? 'selected' : '' }}>Bonus Points</option>
                    </select>
                    <small>
                        <strong>Voucher:</strong> Customer gets discount voucher to apply at cart<br>
                        <strong>Free Product:</strong> Customer can add menu item to cart with RM0.00<br>
                        <strong>Bonus Points:</strong> Instant points credit (multiplier effect)
                    </small>
                </div>

                <div class="form-group">
                    <label for="points_required">Points Required <span class="required">*</span></label>
                    <input type="number" id="points_required" name="points_required" min="1" value="{{ old('points_required', $reward->points_required ?? '') }}" required>
                    <small>Points needed to redeem</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="reward_value">Reward Value (RM)</label>
                    <input type="number" id="reward_value" name="reward_value" step="0.01" min="0" value="{{ old('reward_value', $reward->reward_value ?? '') }}">
                    <small>Monetary value of reward</small>
                </div>

                <div class="form-group">
                    <label for="minimum_order">Minimum Order (RM)</label>
                    <input type="number" id="minimum_order" name="minimum_order" step="0.01" min="0" value="{{ old('minimum_order', $reward->minimum_order ?? '') }}">
                    <small>Minimum order to use reward</small>
                </div>
            </div>

            <!-- Tier Restriction -->
            <h3 style="margin: 30px 0 20px; color: #333;">Tier Restriction (Phase 7)</h3>

            <div class="form-group">
                <label for="required_tier_id">Required Tier (Optional)</label>
                <select id="required_tier_id" name="required_tier_id">
                    <option value="">None - Available to all tiers</option>
                    @foreach($loyaltyTiers ?? [] as $tier)
                        <option value="{{ $tier->id }}" {{ old('required_tier_id', $reward->required_tier_id ?? '') == $tier->id ? 'selected' : '' }}>
                            {{ $tier->name }} ({{ $tier->points_threshold }} points)
                        </option>
                    @endforeach
                </select>
                <small>Leave empty for general rewards. Select tier for exclusive rewards (e.g., Gold-only, Platinum-only)</small>
            </div>

            <!-- Voucher Association (Show only for voucher type) -->
            <div id="voucher-section" style="display: none;">
                <h3 style="margin: 30px 0 20px; color: #333;">Voucher Association</h3>

                <div class="form-group">
                    <label for="voucher_template_id">Link to Voucher Template <span class="required">*</span></label>
                    <select id="voucher_template_id" name="voucher_template_id">
                        <option value="">Select voucher template...</option>
                        @foreach($voucherTemplates ?? [] as $template)
                            <option value="{{ $template->id }}" {{ old('voucher_template_id', $reward->voucher_template_id ?? '') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                    <small>Auto-issue this voucher when reward is redeemed</small>
                </div>
            </div>

            <!-- Menu Item Selection (Show only for product type) -->
            <div id="product-section" style="display: none;">
                <h3 style="margin: 30px 0 20px; color: #333;">Free Product Selection</h3>

                <div class="form-group">
                    <label for="menu_item_id">Select Menu Item <span class="required">*</span></label>
                    <select id="menu_item_id" name="menu_item_id">
                        <option value="">Select free product...</option>
                        @foreach($menuItems ?? [] as $item)
                            <option value="{{ $item->id }}" {{ old('menu_item_id', $reward->menu_item_id ?? '') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} (RM {{ number_format($item->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                    <small>Customer will receive this menu item for free</small>
                </div>
            </div>

            <!-- Usage & Expiry -->
            <h3 style="margin: 30px 0 20px; color: #333;">Usage & Expiry</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="usage_limit">Usage Limit per User</label>
                    <input type="number" id="usage_limit" name="usage_limit" min="1" value="{{ old('usage_limit', $reward->usage_limit ?? '') }}">
                    <small>Max redemptions per user (leave empty for unlimited)</small>
                </div>

                <div class="form-group">
                    <label for="max_redemptions">Total Max Redemptions</label>
                    <input type="number" id="max_redemptions" name="max_redemptions" min="1" value="{{ old('max_redemptions', $reward->max_redemptions ?? '') }}">
                    <small>Total available globally (leave empty for unlimited)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="expiry_days">Expiry Days</label>
                    <input type="number" id="expiry_days" name="expiry_days" min="1" value="{{ old('expiry_days', $reward->expiry_days ?? 30) }}">
                    <small>Days until redeemed reward expires (applies after redemption)</small>
                </div>

                <div class="form-group">
                    <!-- Redemption method hidden - all rewards are automatic (web-based) -->
                    <input type="hidden" name="redemption_method" value="auto">
                    <label style="color: var(--text-3);">Redemption Method</label>
                    <div style="padding: 10px 15px; background: var(--muted); border-radius: 8px; color: var(--text-2); font-size: 14px;">
                        <i class="fas fa-info-circle"></i> Automatic (Web-based)
                    </div>
                    <small>All rewards are automatically applied online</small>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="form-group">
                <label for="terms_conditions">Terms & Conditions</label>
                <textarea id="terms_conditions" name="terms_conditions">{{ old('terms_conditions', $reward->terms_conditions ?? '') }}</textarea>
                <small>Terms and conditions for this reward</small>
            </div>

            <!-- Status -->
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $reward->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" style="margin: 0;">Active (available for redemption)</label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($reward) ? 'Update Reward' : 'Create Reward' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rewardTypeSelect = document.getElementById('reward_type');
    const voucherSection = document.getElementById('voucher-section');
    const productSection = document.getElementById('product-section');
    const voucherTemplateSelect = document.getElementById('voucher_template_id');
    const menuItemSelect = document.getElementById('menu_item_id');

    function toggleSections() {
        const selectedType = rewardTypeSelect.value;

        // Hide all sections first
        voucherSection.style.display = 'none';
        productSection.style.display = 'none';

        // Remove required attribute from all
        voucherTemplateSelect.removeAttribute('required');
        menuItemSelect.removeAttribute('required');

        // Show relevant section based on type
        if (selectedType === 'voucher') {
            voucherSection.style.display = 'block';
            voucherTemplateSelect.setAttribute('required', 'required');
        } else if (selectedType === 'product') {
            productSection.style.display = 'block';
            menuItemSelect.setAttribute('required', 'required');
        }
    }

    // Initial check on page load
    toggleSections();

    // Listen for changes
    rewardTypeSelect.addEventListener('change', toggleSections);
});
</script>
@endsection
