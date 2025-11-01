@extends('layouts.admin')

@section('title', isset($collection) ? 'Edit Voucher Collection' : 'Create Voucher Collection')

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

    .form-group input,
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

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
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
        accent-color: var(--brand);
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>
            <i class="fas fa-layer-group"></i>
            {{ isset($collection) ? 'Edit Voucher Collection' : 'Create Voucher Collection' }}
        </h1>
    </div>

    @if ($errors->any())
    <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($collection) ? route('admin.rewards.voucher-collections.update', $collection->id) : route('admin.rewards.voucher-collections.store') }}" method="POST">
        @csrf
        @if(isset($collection))
            @method('PUT')
        @endif

        <div class="form-card">
            <h3 style="margin-bottom: 20px;">Basic Information</h3>

            <div class="form-group">
                <label for="name">Collection Name <span style="color: #f44336;">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $collection->name ?? '') }}" required>
                <small>Name for this voucher campaign</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $collection->description ?? '') }}</textarea>
            </div>

            <h3 style="margin: 30px 0 20px;">Discount Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="discount_type">Discount Type <span style="color: #f44336;">*</span></label>
                    <select id="discount_type" name="discount_type" required>
                        <option value="">Select type...</option>
                        <option value="percentage" {{ old('discount_type', $collection->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type', $collection->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="discount_value">Discount Value <span style="color: #f44336;">*</span></label>
                    <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" value="{{ old('discount_value', $collection->discount_value ?? '') }}" required>
                    <small>Percentage (1-100) or fixed amount in RM</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="minimum_spend">Minimum Order (RM)</label>
                    <input type="number" id="minimum_spend" name="minimum_spend" step="0.01" min="0" value="{{ old('minimum_spend', $collection->minimum_spend ?? 0) }}">
                    <small>Minimum order to use voucher</small>
                </div>

                <div class="form-group">
                    <label for="max_discount">Max Discount (RM)</label>
                    <input type="number" id="max_discount" name="max_discount" step="0.01" min="0" value="{{ old('max_discount', $collection->max_discount ?? '') }}">
                    <small>Maximum discount for percentage vouchers</small>
                </div>
            </div>

            <h3 style="margin: 30px 0 20px;">Collection Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="spending_requirement">Spending Requirement (RM)</label>
                    <input type="number" id="spending_requirement" name="spending_requirement" step="0.01" min="0" value="{{ old('spending_requirement', $collection->spending_requirement ?? 0) }}">
                    <small>Minimum spend to collect voucher</small>
                </div>

                <div class="form-group">
                    <label for="expiry_days">Expiry Days</label>
                    <input type="number" id="expiry_days" name="expiry_days" min="1" max="365" value="{{ old('expiry_days', $collection->expiry_days ?? '') }}">
                    <small>Days until voucher expires after collection</small>
                </div>
            </div>

            <h3 style="margin: 30px 0 20px;">Usage Limits</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="max_total_uses">Total Usage Limit</label>
                    <input type="number" id="max_total_uses" name="max_total_uses" min="1" value="{{ old('max_total_uses', $collection->total_uses_limit ?? '') }}">
                    <small>Total redemptions allowed (leave empty for unlimited)</small>
                </div>

                <div class="form-group">
                    <label for="max_uses_per_user">Per User Limit</label>
                    <input type="number" id="max_uses_per_user" name="max_uses_per_user" min="1" value="{{ old('max_uses_per_user', $collection->max_uses_per_user ?? '') }}">
                    <small>Max uses per user (leave empty for unlimited)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="valid_until">Valid Until</label>
                    <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until', isset($collection) && $collection->valid_until ? $collection->valid_until->format('Y-m-d') : '') }}">
                    <small>Collection end date</small>
                </div>

                <div class="form-group" style="display: flex; align-items: center; padding-top: 30px;">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $collection->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active" style="margin: 0;">Active (can be collected)</label>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($collection) ? 'Update' : 'Create' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}#voucher-collections" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
