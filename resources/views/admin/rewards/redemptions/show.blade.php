@extends('layouts.admin')

@section('title', 'Redemption Details')
@section('page-title', 'Redemption Details')

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

    .redemption-code-display {
        font-family: monospace;
        font-size: 24px;
        font-weight: 700;
        color: var(--brand);
        padding: 20px;
        background: var(--bg);
        border-radius: var(--radius);
        text-align: center;
        margin: 16px 0;
        letter-spacing: 2px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--muted);
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
        box-shadow: var(--shadow);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
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
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-gift"></i>
            Redemption Details
        </h2>
        <div class="section-controls">
            <a href="{{ route('admin.rewards.redemptions.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Redemption ID Display -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Redemption ID</h3>
        <div class="redemption-code-display">
            #{{ $redemption->id }}
        </div>
        <small style="color: var(--text-3); margin-top: 8px; display: block;">
            Use this ID for tracking and support inquiries
        </small>
    </div>

    <!-- Redemption Information -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Redemption Information</h3>

        <div class="detail-row">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                @php
                    $statusClass = 'status-active';
                    if ($redemption->status == 'pending') $statusClass = 'status-pending';
                    elseif ($redemption->status == 'redeemed') $statusClass = 'status-active';
                    elseif ($redemption->status == 'expired' || $redemption->status == 'cancelled') $statusClass = 'status-inactive';
                @endphp
                <span class="status {{ $statusClass }}">{{ ucfirst($redemption->status) }}</span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Points Spent</div>
            <div class="detail-value">
                <strong style="color: var(--brand); font-size: 18px;">{{ $redemption->points_spent }}</strong> points
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Claimed Date</div>
            <div class="detail-value">
                {{ $redemption->claimed_at ? $redemption->claimed_at->format('d M Y, h:i A') : 'N/A' }}
                @if($redemption->claimed_at)
                    <span style="color: var(--text-3); font-size: 13px;">({{ $redemption->claimed_at->diffForHumans() }})</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Expires At</div>
            <div class="detail-value">
                @if($redemption->expires_at)
                    {{ $redemption->expires_at->format('d M Y, h:i A') }}
                    @if($redemption->expires_at->isPast() && $redemption->status !== 'redeemed')
                        <span style="color: var(--danger); font-weight: 600;">(Expired)</span>
                    @else
                        <span style="color: var(--text-3); font-size: 13px;">({{ $redemption->expires_at->diffForHumans() }})</span>
                    @endif
                @else
                    <span style="color: var(--text-3);">No expiry date</span>
                @endif
            </div>
        </div>

        @if($redemption->redeemed_at)
        <div class="detail-row">
            <div class="detail-label">Redeemed Date</div>
            <div class="detail-value">
                {{ $redemption->redeemed_at->format('d M Y, h:i A') }}
                <span style="color: var(--text-3); font-size: 13px;">({{ $redemption->redeemed_at->diffForHumans() }})</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Customer Information -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Customer Information</h3>

        <div class="detail-row">
            <div class="detail-label">Customer Name</div>
            <div class="detail-value">
                <strong>{{ $redemption->customerProfile->user->name ?? 'N/A' }}</strong>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Email</div>
            <div class="detail-value">
                {{ $redemption->customerProfile->user->email ?? 'N/A' }}
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Phone</div>
            <div class="detail-value">
                {{ $redemption->customerProfile->user->phone_number ?? 'N/A' }}
            </div>
        </div>

        @if($redemption->customerProfile->user->loyalty_tier_id)
        <div class="detail-row">
            <div class="detail-label">Loyalty Tier</div>
            <div class="detail-value">
                <strong style="color: var(--brand);">
                    {{ $redemption->customerProfile->user->loyaltyTier->name ?? 'N/A' }}
                </strong>
            </div>
        </div>
        @endif
    </div>

    <!-- Reward Information -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Reward Information</h3>

        <div class="detail-row">
            <div class="detail-label">Reward Title</div>
            <div class="detail-value">
                <strong>{{ $redemption->reward->title ?? 'N/A' }}</strong>
            </div>
        </div>

        @if($redemption->reward->description)
        <div class="detail-row">
            <div class="detail-label">Description</div>
            <div class="detail-value">
                {{ $redemption->reward->description }}
            </div>
        </div>
        @endif

        <div class="detail-row">
            <div class="detail-label">Reward Type</div>
            <div class="detail-value">
                <span style="text-transform: capitalize;">{{ $redemption->reward->reward_type ?? 'N/A' }}</span>
            </div>
        </div>

        @if($redemption->reward->reward_value)
        <div class="detail-row">
            <div class="detail-label">Reward Value</div>
            <div class="detail-value">
                <strong>RM {{ number_format($redemption->reward->reward_value, 2) }}</strong>
            </div>
        </div>
        @endif

        @if($redemption->reward->minimum_order)
        <div class="detail-row">
            <div class="detail-label">Minimum Order</div>
            <div class="detail-value">
                RM {{ number_format($redemption->reward->minimum_order, 2) }}
            </div>
        </div>
        @endif

        @if($redemption->reward->terms_conditions)
        <div class="detail-row">
            <div class="detail-label">Terms & Conditions</div>
            <div class="detail-value" style="white-space: pre-line;">
                {{ $redemption->reward->terms_conditions }}
            </div>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="detail-card">
        <h3 style="margin-bottom: 16px; color: var(--text);">Actions</h3>

        <div class="action-buttons">
            @if($redemption->status == 'active')
                <form action="{{ route('admin.rewards.redemptions.mark-redeemed', $redemption->id) }}"
                      method="POST"
                      onsubmit="return confirm('Mark this redemption as used? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i>
                        Mark as Redeemed
                    </button>
                </form>
            @endif

            @if(in_array($redemption->status, ['pending', 'active']))
                <form action="{{ route('admin.rewards.redemptions.cancel', $redemption->id) }}"
                      method="POST"
                      onsubmit="return confirm('Cancel this redemption and refund {{ $redemption->points_spent }} points to the customer?');">
                    @csrf
                    <input type="hidden" name="refund_points" value="1">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i>
                        Cancel & Refund Points
                    </button>
                </form>

                <form action="{{ route('admin.rewards.redemptions.cancel', $redemption->id) }}"
                      method="POST"
                      onsubmit="return confirm('Cancel this redemption WITHOUT refunding points?');">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-ban"></i>
                        Cancel Without Refund
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.rewards.redemptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>
</div>

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
