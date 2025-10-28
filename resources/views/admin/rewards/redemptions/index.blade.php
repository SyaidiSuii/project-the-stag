@extends('layouts.admin')
@section('title', 'Redemptions & Voucher Usage')
@section('page-title', 'Redemptions & Voucher Usage')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
<style>
    .tabs-container {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 2px solid #e2e8f0;
    }
    .tab-button {
        padding: 12px 24px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s;
    }
    .tab-button:hover {
        color: #334155;
        background: #f8fafc;
    }
    .tab-button.active {
        color: #0ea5e9;
        border-bottom-color: #0ea5e9;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Redemptions & Voucher Usage Management</h2>
    </div>

    <!-- Tabs -->
    <div class="tabs-container">
        <button class="tab-button active" onclick="switchTab('rewards')">
            <i class="fas fa-gift"></i> Reward Redemptions
        </button>
        <button class="tab-button" onclick="switchTab('vouchers')">
            <i class="fas fa-ticket-alt"></i> Voucher Usage
        </button>
    </div>

    <!-- Reward Redemptions Tab -->
    <div id="rewards-tab" class="tab-content active">
        <div class="section-content">
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Reward</th>
                            <th>Points Spent</th>
                            <th>Status</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redemptions as $redemption)
                            <tr>
                                <td>{{ $redemption->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $redemption->customerProfile->user->name ?? 'N/A' }}</td>
                                <td>{{ $redemption->reward->title ?? 'N/A' }}</td>
                                <td>{{ number_format($redemption->points_spent) }} pts</td>
                                <td>
                                    @if($redemption->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($redemption->status == 'redeemed')
                                        <span class="badge badge-success">Redeemed</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($redemption->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $redemption->expires_at ? $redemption->expires_at->format('d M Y') : 'No Expiry' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        @if($redemption->status == 'pending')
                                            <form action="{{ route('admin.rewards.redemptions.mark-redeemed', $redemption->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="admin-btn btn-icon btn-success" title="Mark as Redeemed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                        <i class="fas fa-gift" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                        <p>No reward redemptions found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">
                {{ $redemptions->links() }}
            </div>
        </div>
    </div>

    <!-- Voucher Usage Tab -->
    <div id="vouchers-tab" class="tab-content">
        <div class="section-content">
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date Used</th>
                            <th>Customer</th>
                            <th>Voucher</th>
                            <th>Source</th>
                            <th>Discount Applied</th>
                            <th>Order ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voucherUsage as $voucher)
                            <tr>
                                <td>
                                    @if($voucher->used_at)
                                        {{ $voucher->used_at->format('d M Y, h:i A') }}
                                    @elseif($voucher->redeemed_at)
                                        {{ $voucher->redeemed_at->format('d M Y, h:i A') }}
                                    @else
                                        {{ $voucher->created_at->format('d M Y, h:i A') }}
                                    @endif
                                </td>
                                <td>{{ $voucher->customerProfile->user->name ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ $voucher->voucherTemplate->name ?? 'N/A' }}</strong><br>
                                    <small style="color: #64748b;">{{ $voucher->voucherTemplate->description ?? '' }}</small>
                                </td>
                                <td>
                                    @if($voucher->source == 'reward')
                                        <span class="badge badge-info">Reward</span>
                                    @else
                                        <span class="badge badge-warning">Collection</span>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->voucherTemplate)
                                        @if($voucher->voucherTemplate->discount_type == 'percentage')
                                            {{ $voucher->voucherTemplate->discount_value }}%
                                        @else
                                            RM {{ number_format($voucher->voucherTemplate->discount_value, 2) }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->order_id)
                                        <a href="{{ route('admin.order.show', $voucher->order_id) }}" style="color: #0ea5e9;" target="_blank">
                                            #{{ $voucher->order_id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->status == 'redeemed')
                                        <span class="badge badge-success">Used</span>
                                    @elseif($voucher->status == 'active')
                                        <span class="badge badge-info">Active</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($voucher->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                        <i class="fas fa-ticket-alt" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                        <p>No voucher usage found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">
                {{ $voucherUsage->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.rewards._table_scroll_script')
<script>
    function switchTab(tab) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });

        // Show selected tab
        if (tab === 'rewards') {
            document.getElementById('rewards-tab').classList.add('active');
            document.querySelector('button[onclick="switchTab(\'rewards\')"]').classList.add('active');
        } else if (tab === 'vouchers') {
            document.getElementById('vouchers-tab').classList.add('active');
            document.querySelector('button[onclick="switchTab(\'vouchers\')"]').classList.add('active');
        }
    }
</script>
@endsection
