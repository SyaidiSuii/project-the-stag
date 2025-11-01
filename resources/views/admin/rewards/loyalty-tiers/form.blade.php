@extends('layouts.admin')

@section('title', isset($tier) ? 'Edit Loyalty Tier' : 'Create Loyalty Tier')

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

    .info-box {
        background: rgba(99, 102, 241, 0.1);
        border-left: 4px solid var(--brand);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        color: var(--text);
    }

    .info-box h4 {
        margin: 0 0 10px 0;
        color: #1976D2;
        font-size: 16px;
    }

    .info-box ul {
        margin: 0;
        padding-left: 20px;
        color: #1565C0;
    }

    .info-box li {
        margin-bottom: 5px;
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>
            <i class="fas fa-trophy"></i>
            {{ isset($tier) ? 'Edit Loyalty Tier' : 'Create New Loyalty Tier' }}
        </h1>
        <p>{{ isset($tier) ? 'Update tier details' : 'Create a new loyalty tier for your program' }}</p>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-info-circle"></i> Phase 7: Tier System Guidelines</h4>
        <ul>
            <li><strong>Order:</strong> Tier hierarchy (1=Bronze, 2=Silver, 3=Gold, 4=Platinum)</li>
            <li><strong>Points Threshold:</strong> Minimum points required to reach this tier</li>
            <li><strong>Points Multiplier:</strong> Earning bonus (1.2 = 20% bonus, 2.0 = 100% bonus)</li>
            <li>Lower order numbers = lower tiers. Higher order = VIP tiers with better benefits</li>
        </ul>
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

    <form action="{{ isset($tier) ? route('admin.rewards.loyalty-tiers.update', $tier->id) : route('admin.rewards.loyalty-tiers.store') }}" method="POST">
        @csrf
        @if(isset($tier))
            @method('PUT')
        @endif

        <div class="form-card">
            <!-- Basic Information -->
            <h3 style="margin-bottom: 20px; color: #333;">Basic Information</h3>

            <div class="form-group">
                <label for="name">Tier Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $tier->name ?? '') }}" required>
                <small>E.g., Bronze, Silver, Gold, Platinum</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="order">Order (Hierarchy) <span class="required">*</span></label>
                    <input type="number" id="order" name="order" min="1" value="{{ old('order', $tier->order ?? '') }}" required>
                    <small>1 = Lowest tier, 4 = Highest tier</small>
                </div>

                <div class="form-group">
                    <label for="points_threshold">Points Threshold <span class="required">*</span></label>
                    <input type="number" id="points_threshold" name="points_threshold" min="0" step="1" value="{{ old('points_threshold', $tier->points_threshold ?? '') }}" required>
                    <small>Minimum points required to reach this tier (e.g., 100 for Bronze, 500 for Silver)</small>
                </div>
            </div>

            <!-- Phase 7: Points Multiplier -->
            <h3 style="margin: 30px 0 20px; color: #333;">Phase 7: Earning Multiplier</h3>

            <div class="form-group">
                <label for="points_multiplier">Points Multiplier <span class="required">*</span></label>
                <input type="number" id="points_multiplier" name="points_multiplier" step="0.1" min="1.0" max="5.0" value="{{ old('points_multiplier', $tier->points_multiplier ?? 1.0) }}" required>
                <small>
                    Earning bonus for this tier. Examples:
                    <br>• 1.0 = No bonus (standard points)
                    <br>• 1.2 = 20% bonus (Bronze tier)
                    <br>• 1.5 = 50% bonus (Silver tier)
                    <br>• 2.0 = 100% bonus (Gold tier - earn double)
                    <br>• 3.0 = 200% bonus (Platinum tier - earn triple)
                </small>
            </div>

            <!-- Legacy Fields (Optional) -->
            <h3 style="margin: 30px 0 20px; color: #333;">Additional Settings</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="minimum_spending">Minimum Spending (RM)</label>
                    <input type="number" id="minimum_spending" name="minimum_spending" step="0.01" min="0" value="{{ old('minimum_spending', $tier->minimum_spending ?? 0) }}">
                    <small>Spending requirement (optional, in addition to points)</small>
                </div>

                <div class="form-group">
                    <label for="sort_order">Display Order</label>
                    <input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $tier->sort_order ?? 0) }}">
                    <small>UI display order (optional)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="color">Tier Color</label>
                    <input type="text" id="color" name="color" value="{{ old('color', $tier->color ?? '') }}" placeholder="#FFD700">
                    <small>Hex color code for visual display</small>
                </div>

                <div class="form-group">
                    <label for="icon">Icon Class</label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon', $tier->icon ?? '') }}" placeholder="fa-trophy">
                    <small>FontAwesome icon class</small>
                </div>
            </div>

            <!-- Status -->
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $tier->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" style="margin: 0;">Active (available for customers)</label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($tier) ? 'Update Tier' : 'Create Tier' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
