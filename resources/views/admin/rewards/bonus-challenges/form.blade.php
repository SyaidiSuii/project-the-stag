@extends('layouts.admin')

@section('title', isset($challenge) ? 'Edit Bonus Challenge' : 'Create Bonus Challenge')

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
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .info-box h4 {
        color: var(--brand);
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .info-box p {
        margin: 0;
        color: var(--text-2);
        font-size: 13px;
        line-height: 1.6;
    }
</style>
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h1>
            <i class="fas fa-star"></i>
            {{ isset($challenge) ? 'Edit Bonus Challenge' : 'Create New Bonus Challenge' }}
        </h1>
        <p>{{ isset($challenge) ? 'Update bonus challenge details' : 'Create a new bonus point challenge for customers' }}</p>
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

    <div class="info-box">
        <h4><i class="fas fa-info-circle"></i> About Bonus Challenges</h4>
        <p>Bonus challenges encourage specific customer behaviors by rewarding bonus points. The system automatically validates if customers meet requirements before allowing claims.</p>
        <ul style="margin: 10px 0 0 20px; padding: 0;">
            <li><strong>Challenge Type:</strong> What customers need to do (orders, spending, visits, etc.)</li>
            <li><strong>Minimum Requirement:</strong> How much is needed (e.g., 1 order, RM50 spending)</li>
            <li><strong>Claim Limits:</strong> Control how many times users can claim and total campaign budget</li>
        </ul>
    </div>

    <form action="{{ isset($challenge) ? route('admin.rewards.bonus-challenges.update', $challenge->id) : route('admin.rewards.bonus-challenges.store') }}" method="POST">
        @csrf
        @if(isset($challenge))
            @method('PUT')
        @endif

        <div class="form-card">
            <!-- Basic Information -->
            <h3 style="margin-bottom: 20px; color: #333;">Challenge Details</h3>

            <div class="form-group">
                <label for="name">Challenge Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $challenge->name ?? '') }}" required>
                <small>Clear and engaging name (e.g., "Weekend Warrior", "Triple Threat")</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $challenge->description ?? '') }}</textarea>
                <small>Detailed explanation of how to complete the challenge</small>
            </div>

            <div class="form-group">
                <label for="condition">Condition/Requirement <span class="required">*</span></label>
                <input type="text" id="condition" name="condition" value="{{ old('condition', $challenge->condition ?? '') }}" required>
                <small>Brief summary/display text for the requirement</small>
            </div>

            <!-- Challenge Requirements -->
            <h3 style="margin: 30px 0 20px; color: #333;">Challenge Requirements</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="condition_type">Challenge Type <span class="required">*</span></label>
                    <select id="condition_type" name="condition_type" required>
                        <option value="orders" {{ old('condition_type', $challenge->condition_type ?? 'orders') == 'orders' ? 'selected' : '' }}>Orders Count</option>
                        <option value="spending" {{ old('condition_type', $challenge->condition_type ?? '') == 'spending' ? 'selected' : '' }}>Total Spending (RM)</option>
                        <option value="visits" {{ old('condition_type', $challenge->condition_type ?? '') == 'visits' ? 'selected' : '' }}>Visit Count</option>
                        <option value="checkin_streak" {{ old('condition_type', $challenge->condition_type ?? '') == 'checkin_streak' ? 'selected' : '' }}>Check-in Streak</option>
                        <option value="referrals" {{ old('condition_type', $challenge->condition_type ?? '') == 'referrals' ? 'selected' : '' }}>Referrals</option>
                        <option value="custom" {{ old('condition_type', $challenge->condition_type ?? '') == 'custom' ? 'selected' : '' }}>Custom (Manual Verification)</option>
                    </select>
                    <small>Type of activity required</small>
                </div>

                <div class="form-group">
                    <label for="min_requirement">Minimum Requirement <span class="required">*</span></label>
                    <input type="number" id="min_requirement" name="min_requirement" min="1" value="{{ old('min_requirement', $challenge->min_requirement ?? '1') }}" required>
                    <small>Minimum value to complete (e.g., 1 order, RM50, 3 visits)</small>
                </div>
            </div>

            <!-- Claim Limits -->
            <h3 style="margin: 30px 0 20px; color: #333;">Claim Limits</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="max_claims_per_user">Max Claims Per User <span class="required">*</span></label>
                    <input type="number" id="max_claims_per_user" name="max_claims_per_user" min="0" value="{{ old('max_claims_per_user', $challenge->max_claims_per_user ?? '1') }}" required>
                    <small>How many times each user can claim (0 = unlimited)</small>
                </div>

                <div class="form-group">
                    <label for="max_claims_total">Max Total Claims <span class="required">*</span></label>
                    <input type="number" id="max_claims_total" name="max_claims_total" min="0" value="{{ old('max_claims_total', $challenge->max_claims_total ?? '0') }}" required>
                    <small>Total claims across all users (0 = unlimited)</small>
                </div>
            </div>

            @if(isset($challenge) && $challenge->current_claims > 0)
            <div class="info-box" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3);">
                <h4 style="color: var(--success);"><i class="fas fa-chart-line"></i> Current Statistics</h4>
                <p><strong>Total Claims:</strong> {{ $challenge->current_claims }} times</p>
            </div>
            @endif

            <!-- Rewards & Duration -->
            <h3 style="margin: 30px 0 20px; color: #333;">Rewards & Duration</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="bonus_points">Bonus Points <span class="required">*</span></label>
                    <input type="number" id="bonus_points" name="bonus_points" min="1" value="{{ old('bonus_points', $challenge->bonus_points ?? '') }}" required>
                    <small>Points awarded upon completion</small>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', isset($challenge) && $challenge->end_date ? $challenge->end_date->format('Y-m-d') : '') }}">
                    <small>Leave empty for no expiration</small>
                </div>
            </div>

            <!-- Status -->
            <h3 style="margin: 30px 0 20px; color: #333;">Status</h3>

            <div class="form-group">
                <label for="status">Status <span class="required">*</span></label>
                <select id="status" name="status" required>
                    <option value="active" {{ old('status', $challenge->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $challenge->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <small>Active challenges are visible to customers</small>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($challenge) ? 'Update Challenge' : 'Create Challenge' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}#bonus-challenges" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
