@extends('layouts.admin')

@section('title', isset($template) ? 'Edit Voucher Template' : 'Create Voucher Template')

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
    .form-group input[type="date"],
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
            <i class="fas fa-ticket-alt"></i>
            {{ isset($template) ? 'Edit Voucher Template' : 'Create New Voucher Template' }}
        </h1>
        <p>{{ isset($template) ? 'Update voucher template details' : 'Create a voucher template for rewards or campaigns' }}</p>
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

    <form action="{{ isset($template) ? route('admin.rewards.voucher-templates.update', $template->id) : route('admin.rewards.voucher-templates.store') }}" method="POST">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif

        <div class="form-card">
            <!-- Basic Information -->
            <h3 style="margin-bottom: 20px; color: #333;">Basic Information</h3>

            <div class="form-group">
                <label for="name">Template Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $template->name ?? '') }}" required>
                <small>Internal name for this voucher template</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $template->description ?? '') }}</textarea>
                <small>Description of the voucher offer</small>
            </div>

            <div class="form-group">
                <label for="source_type">Source Type <span class="required">*</span></label>
                <select id="source_type" name="source_type" required>
                    <option value="">Select source type...</option>
                    <option value="reward" {{ old('source_type', $template->source_type ?? '') == 'reward' ? 'selected' : '' }}>Reward (Loyalty Points)</option>
                    <option value="collection" {{ old('source_type', $template->source_type ?? '') == 'collection' ? 'selected' : '' }}>Collection (Campaign)</option>
                    <option value="promotion" {{ old('source_type', $template->source_type ?? '') == 'promotion' ? 'selected' : '' }}>Promotion</option>
                </select>
                <small>Where this voucher originates from</small>
            </div>

            <!-- Discount Settings -->
            <h3 style="margin: 30px 0 20px; color: #333;">Discount Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="discount_type">Discount Type <span class="required">*</span></label>
                    <select id="discount_type" name="discount_type" required>
                        <option value="">Select type...</option>
                        <option value="percentage" {{ old('discount_type', $template->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type', $template->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="discount_value">Discount Value <span class="required">*</span></label>
                    <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" value="{{ old('discount_value', $template->discount_value ?? '') }}" required>
                    <small>Percentage (1-100) or fixed amount in RM</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="minimum_spend">Minimum Order (RM)</label>
                    <input type="number" id="minimum_spend" name="minimum_spend" step="0.01" min="0" value="{{ old('minimum_spend', $template->minimum_spend ?? 0) }}">
                    <small>Minimum order to use voucher</small>
                </div>

                <div class="form-group">
                    <label for="max_discount">Max Discount (RM)</label>
                    <input type="number" id="max_discount" name="max_discount" step="0.01" min="0" value="{{ old('max_discount', $template->max_discount ?? '') }}">
                    <small>Maximum discount for percentage vouchers</small>
                </div>
            </div>

            <!-- Usage Limits -->
            <h3 style="margin: 30px 0 20px; color: #333;">Usage Limits</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_uses_limit">Total Usage Limit</label>
                    <input type="number" id="total_uses_limit" name="total_uses_limit" min="1" value="{{ old('total_uses_limit', $template->total_uses_limit ?? '') }}">
                    <small>Total redemptions allowed (leave empty for unlimited)</small>
                </div>

                <div class="form-group">
                    <label for="max_uses_per_user">Per User Limit</label>
                    <input type="number" id="max_uses_per_user" name="max_uses_per_user" min="1" value="{{ old('max_uses_per_user', $template->max_uses_per_user ?? '') }}">
                    <small>Max uses per user (leave empty for unlimited)</small>
                </div>
            </div>

            <!-- Validity Period -->
            <h3 style="margin: 30px 0 20px; color: #333;">Validity Period</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="expiry_days">Expiry Days</label>
                    <input type="number" id="expiry_days" name="expiry_days" min="1" value="{{ old('expiry_days', $template->expiry_days ?? '') }}">
                    <small>Days until voucher expires after issue (leave empty for no expiry)</small>
                </div>

                <div class="form-group">
                    <label for="valid_until">Valid Until</label>
                    <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until', isset($template) && $template->valid_until ? $template->valid_until->format('Y-m-d') : '') }}">
                    <small>Absolute end date (alternative to expiry days)</small>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="form-group">
                <label for="terms_conditions">Terms & Conditions</label>
                <textarea id="terms_conditions" name="terms_conditions" rows="4">{{ old('terms_conditions', $template->terms_conditions ?? '') }}</textarea>
                <small>Terms and conditions for voucher usage</small>
            </div>

            <!-- Status -->
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" style="margin: 0;">Active (can be issued)</label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($template) ? 'Update Template' : 'Create Template' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}#voucher-templates" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
