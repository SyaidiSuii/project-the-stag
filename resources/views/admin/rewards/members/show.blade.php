@extends('layouts.admin')

@section('title', 'Member Details')
@section('page-title', 'Member Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
<style>
    .detail-card {
        background: white;
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
        border: 1px solid var(--muted);
        margin-bottom: 20px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid var(--muted);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: var(--text-2);
    }

    .detail-value {
        color: var(--text);
    }

    .points-display {
        font-size: 48px;
        font-weight: 700;
        color: var(--brand);
        text-align: center;
        margin: 20px 0;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--muted);
        color: var(--text);
    }

    .btn-secondary:hover {
        background: var(--text-3);
        color: white;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background: white;
        border-radius: var(--radius);
        padding: 30px;
        max-width: 500px;
        width: 90%;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--muted);
        border-radius: 8px;
        font-size: 14px;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -25px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--brand);
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: -21px;
        top: 15px;
        width: 2px;
        height: calc(100% - 10px);
        background: var(--muted);
    }

    .timeline-item:last-child:after {
        display: none;
    }

    .timeline-content {
        background: var(--bg);
        padding: 12px 16px;
        border-radius: 8px;
    }

    .timeline-date {
        font-size: 12px;
        color: var(--text-3);
        margin-bottom: 4px;
    }

    .timeline-description {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .timeline-points {
        font-weight: 700;
    }

    .timeline-points.positive {
        color: var(--success);
    }

    .timeline-points.negative {
        color: var(--danger);
    }
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-user"></i>
            {{ $member->name }}
        </h2>
        <div class="section-controls">
            <a href="{{ route('admin.rewards.members.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="admin-cards">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Points Earned</div>
                <div class="admin-card-icon icon-green"><i class="fas fa-arrow-up"></i></div>
            </div>
            <div class="admin-card-value">{{ number_format($memberStats['total_earned'] ?? 0) }}</div>
            <div class="admin-card-desc">Lifetime earnings</div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Points Spent</div>
                <div class="admin-card-icon icon-red"><i class="fas fa-arrow-down"></i></div>
            </div>
            <div class="admin-card-value">{{ number_format($memberStats['total_spent'] ?? 0) }}</div>
            <div class="admin-card-desc">Used for redemptions</div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Redemptions</div>
                <div class="admin-card-icon icon-blue"><i class="fas fa-gift"></i></div>
            </div>
            <div class="admin-card-value">{{ $memberStats['total_redemptions'] ?? 0 }}</div>
            <div class="admin-card-desc">{{ $memberStats['active_redemptions'] ?? 0 }} active</div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Vouchers</div>
                <div class="admin-card-icon icon-orange"><i class="fas fa-ticket-alt"></i></div>
            </div>
            <div class="admin-card-value">{{ $memberStats['total_vouchers'] ?? 0 }}</div>
            <div class="admin-card-desc">{{ $memberStats['active_vouchers'] ?? 0 }} active</div>
        </div>
    </div>

    <!-- Current Points Balance -->
    <div class="detail-card">
        <h3 style="text-align: center; margin-bottom: 10px; color: var(--text-2);">Current Points Balance</h3>
        <div class="points-display">{{ number_format($member->points_balance ?? 0) }}</div>

        <div class="action-buttons" style="justify-content: center; margin-top: 20px;">
            <button onclick="openAdjustPointsModal()" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Adjust Points
            </button>
            <button onclick="openResetPointsModal()" class="btn btn-danger">
                <i class="fas fa-redo"></i>
                Reset Points
            </button>
        </div>
    </div>

    <!-- Member Information -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Member Information</h3>

        <div class="detail-row">
            <div class="detail-label">Full Name</div>
            <div class="detail-value"><strong>{{ $member->name }}</strong></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Email</div>
            <div class="detail-value">{{ $member->email }}</div>
        </div>

        @if($member->phone_number)
        <div class="detail-row">
            <div class="detail-label">Phone</div>
            <div class="detail-value">{{ $member->phone_number }}</div>
        </div>
        @endif

        <div class="detail-row">
            <div class="detail-label">Loyalty Tier</div>
            <div class="detail-value">
                @if($member->loyaltyTier)
                    <span class="status status-active" style="background: {{ $member->loyaltyTier->color ?? '#6366f1' }}; color: white;">
                        {{ $member->loyaltyTier->name }}
                    </span>
                @else
                    <span class="status status-inactive">No Tier Assigned</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Total Spent</div>
            <div class="detail-value">
                <strong>RM {{ number_format($member->customerProfile->total_spent ?? 0, 2) }}</strong>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Visit Count</div>
            <div class="detail-value">{{ $member->customerProfile->visit_count ?? 0 }} visits</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Member Since</div>
            <div class="detail-value">
                {{ $member->created_at->format('d M Y') }}
                <span style="color: var(--text-3); font-size: 13px;">({{ $member->created_at->diffForHumans() }})</span>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Transaction History</h3>

        @if($transactions->count() > 0)
            <div class="timeline">
                @foreach($transactions as $transaction)
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-date">
                            {{ $transaction->created_at ? $transaction->created_at->format('d M Y, h:i A') : 'N/A' }}
                        </div>
                        <div class="timeline-description">
                            {{ $transaction->description ?? 'No description' }}
                        </div>
                        <div class="timeline-points {{ $transaction->points_change > 0 ? 'positive' : 'negative' }}">
                            {{ $transaction->points_change > 0 ? '+' : '' }}{{ number_format($transaction->points_change ?? 0) }} points
                            <span style="color: var(--text-3); font-size: 12px; font-weight: normal;">
                                (Balance: {{ number_format($transaction->balance_after ?? 0) }})
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="empty-state-text">No transaction history yet</p>
            </div>
        @endif
    </div>
</div>

<!-- Adjust Points Modal -->
<div id="adjustPointsModal" class="modal-overlay">
    <div class="modal">
        <h3 style="margin-bottom: 20px;">Adjust Points</h3>
        <form action="{{ route('admin.rewards.members.adjust-points', $member->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="points">Points (use negative for deduction)</label>
                <input type="number" id="points" name="points" required placeholder="e.g., 100 or -50">
                <small style="color: var(--text-3); display: block; margin-top: 5px;">
                    Enter positive number to add points, negative to deduct
                </small>
            </div>
            <div class="form-group">
                <label for="description">Reason</label>
                <textarea id="description" name="description" rows="3" required placeholder="Reason for adjustment..."></textarea>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
                <button type="button" onclick="closeAdjustPointsModal()" class="btn btn-secondary">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Points Modal -->
<div id="resetPointsModal" class="modal-overlay">
    <div class="modal">
        <h3 style="margin-bottom: 20px; color: var(--danger);">Reset Points to Zero</h3>
        <p style="margin-bottom: 20px; color: var(--text-2);">
            This will reset <strong>{{ $member->name }}'s</strong> points balance to zero. This action cannot be undone.
        </p>
        <form action="{{ route('admin.rewards.members.reset-points', $member->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="reason">Reason for Reset</label>
                <textarea id="reason" name="reason" rows="3" required placeholder="Explain why points are being reset..."></textarea>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Confirm Reset
                </button>
                <button type="button" onclick="closeResetPointsModal()" class="btn btn-secondary">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjustPointsModal() {
    document.getElementById('adjustPointsModal').classList.add('active');
}

function closeAdjustPointsModal() {
    document.getElementById('adjustPointsModal').classList.remove('active');
}

function openResetPointsModal() {
    document.getElementById('resetPointsModal').classList.add('active');
}

function closeResetPointsModal() {
    document.getElementById('resetPointsModal').classList.remove('active');
}

// Close modal when clicking outside
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

@if(session('success'))
    <script>
        setTimeout(() => {
            alert('{{ session('success') }}');
        }, 100);
    </script>
@endif

@if(session('error'))
    <script>
        setTimeout(() => {
            alert('{{ session('error') }}');
        }, 100);
    </script>
@endif
@endsection
