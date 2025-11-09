@extends('layouts.admin')

@section('title', 'Loyalty & Rewards Management')
@section('page-title', 'Loyalty & Rewards Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
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

    .rewards-dashboard {
        padding: 20px;
    }

    .rewards-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .rewards-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: var(--text);
    }

    /* Admin Cards - Same as promotions */
    .admin-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .admin-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow);
        transition: transform 0.2s;
    }

    .admin-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .admin-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .admin-card-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-2);
    }

    .admin-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .admin-card-icon.icon-green {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .admin-card-icon.icon-blue {
        background: rgba(99, 102, 241, 0.1);
        color: var(--brand);
    }

    .admin-card-icon.icon-orange {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .admin-card-icon.icon-red {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .admin-card-icon.icon-purple {
        background: rgba(255, 107, 53, 0.1);
        color: var(--accent);
    }

    .admin-card-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .admin-card-desc {
        font-size: 13px;
        color: var(--text-3);
    }

    .rewards-tabs {
        background: var(--card);
        border-radius: 0 0 var(--radius) var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .tab-navigation {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        background: var(--card);
        padding: 12px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        border: 1px solid var(--muted);
    }

    .tab-nav-item {
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        background: transparent;
        color: var(--text-2);
        border: none;
        font-size: 14px;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tab-nav-item:hover:not(.active) {
        background: var(--bg);
    }

    .tab-nav-item.active {
        background: var(--brand);
        color: white;
    }

    .tab-content {
        display: none;
        padding: 30px;
        background: var(--card);
        border-radius: 0 0 var(--radius) var(--radius);
        overflow-x: hidden;
    }

    .tab-content.active {
        display: block;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--text);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius);
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        background: var(--muted);
        color: var(--text);
    }

    .btn:hover {
        background: #cbd5e1;
        box-shadow: var(--shadow);
    }

    .btn-primary {
        background: var(--brand);
        color: white;
    }

    .btn-primary:hover {
        background: var(--brand-2);
        box-shadow: var(--shadow);
    }

    /* .btn-secondary {
        background: #64748b;
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
        box-shadow: var(--shadow);
    } */

    .btn-info {
        background: #0ea5e9;
        color: white;
    }

    .btn-info:hover {
        background: #0284c7;
        box-shadow: var(--shadow);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        box-shadow: var(--shadow);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-3);
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: var(--muted);
    }

    .empty-state h3 {
        font-size: 20px;
        color: var(--text-2);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-3);
        margin-bottom: 20px;
    }

    .table-wrapper {
        overflow-x: auto;
        margin: 20px 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .data-table th {
        background: var(--bg);
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: var(--text);
        border-bottom: 2px solid var(--muted);
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid var(--muted);
        color: var(--text-2);
    }

    .data-table tr:hover {
        background: var(--bg);
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .badge-info {
        background: rgba(99, 102, 241, 0.1);
        color: var(--brand);
    }

    /* ===== RESPONSIVE DESIGN - 4-TIER BREAKPOINT SYSTEM ===== */
    
    /* Large Desktop (≥1600px) - 30-40% larger */
    @media (min-width: 1600px) {
        .rewards-dashboard {
            padding: 30px;
        }
        .admin-cards {
            gap: 28px;
        }
        .admin-card {
            padding: 28px;
        }
        .admin-card-title {
            font-size: 17px;
        }
        .admin-card-value {
            font-size: 36px;
        }
        .tab-nav-item {
            padding: 14px 28px;
            font-size: 16px;
        }
        .tab-content {
            padding: 40px;
        }
        .section-header h2 {
            font-size: 26px;
        }
    }

    /* Tablet (769px-1199px) - 20-25% smaller */
    @media (max-width: 1199px) and (min-width: 769px) {
        .rewards-dashboard {
            padding: 16px;
        }
        .admin-cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .admin-card {
            padding: 18px;
        }
        .admin-card-title {
            font-size: 13px;
        }
        .admin-card-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
        .admin-card-value {
            font-size: 22px;
        }
        .admin-card-desc {
            font-size: 12px;
        }
        .tab-navigation {
            gap: 6px;
            padding: 10px;
        }
        .tab-nav-item {
            padding: 8px 16px;
            font-size: 13px;
        }
        .tab-content {
            padding: 20px;
        }
        .section-header h2 {
            font-size: 17px;
        }
        .btn {
            padding: 8px 16px;
            font-size: 13px;
        }
        .data-table th,
        .data-table td {
            padding: 10px;
            font-size: 13px;
        }
    }

    /* Mobile (≤768px) - 35-40% smaller, single column */
    @media (max-width: 768px) {
        .rewards-dashboard {
            padding: 12px;
        }
        .admin-cards {
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        .admin-card {
            padding: 16px;
        }
        .admin-card-title {
            font-size: 12px;
        }
        .admin-card-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        .admin-card-value {
            font-size: 20px;
        }
        .admin-card-desc {
            font-size: 11px;
        }
        .tab-navigation {
            flex-wrap: wrap;
            gap: 6px;
            padding: 8px;
        }
        .tab-nav-item {
            padding: 8px 14px;
            font-size: 12px;
            flex: 1 1 calc(50% - 6px);
            min-width: 120px;
            justify-content: center;
        }
        .tab-nav-item i {
            font-size: 11px;
        }
        .tab-content {
            padding: 16px;
        }
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .section-header h2 {
            font-size: 16px;
        }
        .btn {
            padding: 8px 14px;
            font-size: 12px;
            width: 100%;
        }
        .btn i {
            font-size: 11px;
        }
        /* Table Responsive */
        .table-wrapper {
            overflow-x: auto;
            margin: 16px 0;
        }
        .data-table {
            min-width: 600px;
        }
        .data-table th,
        .data-table td {
            padding: 8px;
            font-size: 12px;
        }
        .action-buttons {
            flex-direction: column;
            gap: 6px;
        }
        .action-buttons .btn {
            width: 100%;
        }
        .badge {
            font-size: 10px;
            padding: 3px 8px;
        }
        /* Check-in Settings Form */
        .admin-table input[type="number"] {
            width: 80px;
            padding: 6px 10px;
            font-size: 12px;
        }
        .empty-state {
            padding: 40px 16px;
        }
        .empty-state i {
            font-size: 48px;
        }
        .empty-state h3 {
            font-size: 16px;
        }
        .empty-state p {
            font-size: 13px;
        }
    }

    /* Small Mobile (≤480px) - Ultra compact */
    @media (max-width: 480px) {
        .rewards-dashboard {
            padding: 10px;
        }
        .admin-cards {
            gap: 10px;
        }
        .admin-card {
            padding: 12px;
        }
        .admin-card-header {
            margin-bottom: 10px;
        }
        .admin-card-title {
            font-size: 11px;
        }
        .admin-card-icon {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
        .admin-card-value {
            font-size: 18px;
        }
        .admin-card-desc {
            font-size: 10px;
        }
        .tab-navigation {
            padding: 6px;
            gap: 4px;
        }
        .tab-nav-item {
            padding: 6px 10px;
            font-size: 11px;
            flex: 1 1 100%;
        }
        .tab-content {
            padding: 12px;
        }
        .section-header h2 {
            font-size: 14px;
        }
        .btn {
            padding: 6px 12px;
            font-size: 11px;
        }
        .data-table th,
        .data-table td {
            padding: 6px;
            font-size: 11px;
        }
        .badge {
            font-size: 9px;
            padding: 2px 6px;
        }
    }
</style>
@endsection

@section('content')
<div class="rewards-dashboard">
    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div style="background: var(--success); color: white; padding: 16px 20px; border-radius: var(--radius); margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-check-circle" style="font-size: 20px;"></i>
        <span style="font-weight: 500;">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div style="background: var(--danger); color: white; padding: 16px 20px; border-radius: var(--radius); margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i>
        <span style="font-weight: 500;">{{ session('error') }}</span>
    </div>
    @endif

    {{-- <!-- Header -->
    <div class="rewards-header">
        <h1><i class="fas fa-gift"></i> Loyalty & Rewards Management</h1>
    </div> --}}

    <!-- Stats Overview -->
    <div class="admin-cards">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Total Rewards</div>
                <div class="admin-card-icon icon-purple"><i class="fas fa-gift"></i></div>
            </div>
            <div class="admin-card-value">{{ $rewards->count() ?? 0 }}</div>
            <div class="admin-card-desc">{{ $rewards->where('is_active', true)->count() ?? 0 }} active</div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Total Members</div>
                <div class="admin-card-icon icon-blue"><i class="fas fa-users"></i></div>
            </div>
            <div class="admin-card-value">{{ $members->count() ?? 0 }}</div>
            <div class="admin-card-desc">Loyalty program members</div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Total Redemptions</div>
                <div class="admin-card-icon icon-green"><i class="fas fa-exchange-alt"></i></div>
            </div>
            <div class="admin-card-value">{{ $redemptions->count() ?? 0 }}</div>
            <div class="admin-card-desc">This month</div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Loyalty Tiers</div>
                <div class="admin-card-icon icon-orange"><i class="fas fa-trophy"></i></div>
            </div>
            <div class="admin-card-value">{{ $loyaltyTiers->count() ?? 0 }}</div>
            <div class="admin-card-desc">{{ $loyaltyTiers->where('is_active', true)->count() ?? 0 }} active tiers</div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="tab-navigation">
        <button class="tab-nav-item active" data-tab="rewards">
            <i class="fas fa-gift"></i> Rewards
        </button>
        <button class="tab-nav-item" data-tab="voucher-templates">
            <i class="fas fa-ticket-alt"></i> Voucher Templates
        </button>
        {{-- <button class="tab-nav-item" data-tab="voucher-collections">
            <i class="fas fa-layer-group"></i> Voucher Collections
        </button> --}}
        <button class="tab-nav-item" data-tab="bonus-challenges">
            <i class="fas fa-star"></i> Bonus Challenges
        </button>
        <button class="tab-nav-item" data-tab="checkin-settings">
            <i class="fas fa-calendar-check"></i> Check-in Settings
        </button>
        <button class="tab-nav-item" data-tab="tiers">
            <i class="fas fa-trophy"></i> Tiers & Levels
        </button>
        <button class="tab-nav-item" data-tab="redemptions">
            <i class="fas fa-exchange-alt"></i> Redemptions
        </button>
        <button class="tab-nav-item" data-tab="members">
            <i class="fas fa-users"></i> Members
        </button>
    </div>

    <div class="rewards-tabs">

        <!-- Rewards Tab -->
        <div class="tab-content active" id="rewards">
            <div class="section-header">
                <h2>Rewards Catalogue</h2>
                <a href="{{ route('admin.rewards.rewards.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Reward
                </a>
            </div>

            @if($rewards && $rewards->count() > 0)
            <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Points Required</th>
                        <th>Type</th>
                        <th>Required Tier</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rewards as $reward)
                    <tr>
                        <td>{{ $reward->title }}</td>
                        <td>{{ $reward->points_required }} points</td>
                        <td>{{ ucfirst($reward->reward_type ?? 'N/A') }}</td>
                        <td>
                            @if($reward->requiredTier)
                                <span class="badge badge-info">{{ $reward->requiredTier->name }}</span>
                            @else
                                <span class="badge badge-success">All tiers</span>
                            @endif
                        </td>
                        <td>
                            @if($reward->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.rewards.rewards.edit', $reward->id) }}"
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.rewards.rewards.destroy', $reward->id) }}"
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this reward?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-gift"></i>
                <h3>No Rewards Yet</h3>
                <p>Start by creating your first reward</p>
                <a href="{{ route('admin.rewards.rewards.create') }}" class="btn btn-primary">Create First Reward</a>
            </div>
            @endif
        </div>

        <!-- Voucher Templates Tab -->
        <div class="tab-content" id="voucher-templates">
            <div class="section-header">
                <h2>Voucher Templates</h2>
                <a href="{{ route('admin.rewards.voucher-templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Template
                </a>
            </div>

            @if($voucherTemplates && $voucherTemplates->count() > 0)
            <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Discount</th>
                        <th>Usage Limit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voucherTemplates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td>
                            @if($template->discount_type === 'percentage')
                                {{ $template->discount_value }}%
                            @else
                                RM {{ number_format($template->discount_value, 2) }}
                            @endif
                        </td>
                        <td>{{ $template->usage_limit ?? 'Unlimited' }}</td>
                        <td>
                            @if($template->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.rewards.voucher-templates.edit', $template->id) }}"
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.rewards.voucher-templates.destroy', $template->id) }}"
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this voucher template?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3>No Voucher Templates Yet</h3>
                <p>Create voucher templates to issue to customers</p>
                <a href="{{ route('admin.rewards.voucher-templates.create') }}" class="btn btn-primary">Create First Template</a>
            </div>
            @endif
        </div>

        <!-- Voucher Collections Tab -->
        <div class="tab-content" id="voucher-collections">
            <div class="section-header">
                <h2>Voucher Collections</h2>
                <a href="{{ route('admin.rewards.voucher-collections.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Collection
                </a>
            </div>

            @if($voucherCollections && $voucherCollections->count() > 0)
            <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Spending Requirement</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voucherCollections as $collection)
                    <tr>
                        <td>{{ $collection->name }}</td>
                        <td>RM {{ number_format($collection->spending_requirement ?? 0, 2) }}</td>
                        <td>{{ $collection->valid_until ? $collection->valid_until->format('d M Y') : 'No expiry' }}</td>
                        <td>
                            @if($collection->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.rewards.voucher-collections.edit', $collection->id) }}"
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.rewards.voucher-collections.destroy', $collection->id) }}"
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this voucher collection?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <h3>No Voucher Collections Yet</h3>
                <p>Create voucher collections for campaigns</p>
                <a href="{{ route('admin.rewards.voucher-collections.create') }}" class="btn btn-primary">Create First Collection</a>
            </div>
            @endif
        </div>

        <!-- Bonus Challenges Tab -->
        <div class="tab-content" id="bonus-challenges">
            <div class="section-header">
                <h2>Bonus Point Challenges</h2>
                <a href="{{ route('admin.rewards.bonus-challenges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Challenge
                </a>
            </div>

            @if(isset($bonusPointsChallenges) && $bonusPointsChallenges->count() > 0)
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Challenge Name</th>
                            <th>Type & Requirement</th>
                            <th>Bonus Points</th>
                            <th>Claims</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bonusPointsChallenges as $challenge)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $challenge->name }}</div>
                                @if($challenge->description)
                                <div style="font-size: 0.85rem; color: var(--text-2); margin-top: 4px;">
                                    {{ Str::limit($challenge->description, 50) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <strong style="color: var(--brand);">{{ ucfirst(str_replace('_', ' ', $challenge->condition_type ?? 'orders')) }}</strong>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-2); margin-top: 2px;">
                                    @if($challenge->condition_type === 'spending')
                                        Min: RM{{ number_format($challenge->min_requirement ?? 1, 2) }}
                                    @else
                                        Min: {{ $challenge->min_requirement ?? 1 }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--success);">
                                    +{{ $challenge->bonus_points }} pts
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <strong>{{ $challenge->current_claims ?? 0 }}</strong>
                                    @if(($challenge->max_claims_total ?? 0) > 0)
                                        / {{ $challenge->max_claims_total }}
                                    @endif
                                </div>
                                @if(($challenge->max_claims_per_user ?? 1) > 0)
                                <div style="font-size: 0.75rem; color: var(--text-3); margin-top: 2px;">
                                    Max {{ $challenge->max_claims_per_user }}/user
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($challenge->end_date)
                                    {{ $challenge->end_date->format('M j, Y') }}
                                    @if($challenge->end_date < now())
                                        <span class="badge" style="background: var(--danger); margin-left: 8px;">Expired</span>
                                    @endif
                                @else
                                    <span style="color: var(--text-3);">No expiry</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge status-{{ $challenge->status }}">
                                    {{ ucfirst($challenge->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.bonus-challenges.edit', $challenge->id) }}"
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.rewards.bonus-challenges.destroy', $challenge->id) }}"
                                          method="POST" style="display: inline-block;"
                                          onsubmit="return confirm('Are you sure you want to delete this challenge?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-star" style="font-size: 3rem; color: var(--text-3); margin-bottom: 1rem;"></i>
                <h3>No Bonus Challenges Yet</h3>
                <p>Create challenges to encourage customers to earn extra points!</p>
                <a href="{{ route('admin.rewards.bonus-challenges.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Create First Challenge
                </a>
            </div>
            @endif
        </div>

        <!-- Check-in Settings Tab -->
        <div class="tab-content" id="checkin-settings">
            <div class="section-header">
                <h2>Check-in Reward Settings</h2>
            </div>

            @php
                $checkinSettings = $checkinSettings ?? \App\Models\CheckinSetting::first();
                $dailyPoints = $checkinSettings ? $checkinSettings->daily_points : [25, 5, 5, 10, 10, 15, 20];
            @endphp

            <div class="table-container">
                <form action="{{ route('admin.rewards.checkin.update') }}" method="POST">
                    @csrf
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Day</th>
                                <th style="width: 50%;">Points Reward</th>
                                <th style="width: 30%;">Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $dayName)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--text);">
                                        {{ $dayName }}
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-3); margin-top: 2px;">
                                        Day {{ $index + 1 }}
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <input type="number" 
                                               name="daily_points[{{ $index }}]" 
                                               value="{{ $dailyPoints[$index] ?? 5 }}" 
                                               min="0" 
                                               max="1000"
                                               class="form-control" 
                                               style="width: 120px; padding: 8px 12px; border: 1px solid var(--muted); border-radius: 8px;"
                                               required>
                                        <span style="color: var(--text-2); font-weight: 500;">points</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: rgba(99, 102, 241, 0.1); border-radius: 8px;">
                                        <i class="fas fa-gift" style="color: var(--brand);"></i>
                                        <span style="font-weight: 600; color: var(--brand);">+{{ $dailyPoints[$index] ?? 5 }} pts</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="margin-top: 24px; padding: 20px; background: var(--bg); border-radius: var(--radius);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 8px;">
                                    <i class="fas fa-info-circle" style="color: var(--brand);"></i> How It Works
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: var(--text-2); font-size: 0.9rem;">
                                    <li>Customers earn points by checking in daily</li>
                                    <li>Streak continues indefinitely - no reset after 7 days</li>
                                    <li>Bonus points cycle every 7 days (Week 1, Week 2, etc.)</li>
                                    <li>Missing a day resets the streak back to Day 1</li>
                                </ul>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.85rem; color: var(--text-3); margin-bottom: 8px;">
                                    Total for 1 week:
                                </div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--brand);">
                                    {{ array_sum($dailyPoints) }} points
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Streak Milestones Settings -->
                    <div style="margin-top: 20px; padding: 20px; background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(99, 102, 241, 0.1)); border-radius: var(--radius); border: 2px dashed var(--accent);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 16px;">
                            <i class="fas fa-fire" style="color: var(--accent);"></i> Streak Fire Animation Milestones
                        </h3>
                        <p style="color: var(--text-2); font-size: 0.9rem; margin-bottom: 16px;">
                            Set which streak days will trigger the fire overlay animation (like TikTok). Separate multiple days with commas.
                        </p>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <label style="font-weight: 600; color: var(--text); min-width: 180px;">
                                Animation Trigger Days:
                            </label>
                            <input type="text" 
                                   name="streak_milestones" 
                                   value="{{ isset($checkinSettings->streak_milestones) ? implode(', ', $checkinSettings->streak_milestones) : '7, 14, 30, 60, 100' }}" 
                                   class="form-control" 
                                   placeholder="e.g., 7, 14, 30, 60, 100"
                                   style="flex: 1; padding: 10px 16px; border: 2px solid var(--accent); border-radius: 8px; font-weight: 500;">
                        </div>
                        <div style="margin-top: 12px; font-size: 0.85rem; color: var(--text-3);">
                            <i class="fas fa-lightbulb" style="color: var(--warning);"></i> 
                            <strong>Examples:</strong> Day 7 (1 week), Day 14 (2 weeks), Day 30 (1 month), Day 100 (100-day streak)
                        </div>
                    </div>

                    <div style="margin-top: 24px; text-align: right;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tiers Tab -->
        <div class="tab-content" id="tiers">
            <div class="section-header">
                <h2>Loyalty Tiers</h2>
                <a href="{{ route('admin.rewards.loyalty-tiers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Tier
                </a>
            </div>

            @if($loyaltyTiers && $loyaltyTiers->count() > 0)
            <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Points Threshold</th>
                        <th>Points Multiplier</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loyaltyTiers->sortBy('order') as $tier)
                    <tr>
                        <td>{{ $tier->order }}</td>
                        <td>{{ $tier->name }}</td>
                        <td>{{ $tier->points_threshold }} points</td>
                        <td>{{ $tier->points_multiplier }}x</td>
                        <td>
                            @if($tier->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.rewards.loyalty-tiers.edit', $tier->id) }}"
                                   class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.rewards.loyalty-tiers.destroy', $tier->id) }}"
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this loyalty tier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-trophy"></i>
                <h3>No Loyalty Tiers Yet</h3>
                <p>Create loyalty tiers (Bronze, Silver, Gold, Platinum)</p>
                <a href="{{ route('admin.rewards.loyalty-tiers.create') }}" class="btn btn-primary">Create First Tier</a>
            </div>
            @endif
        </div>

        <!-- Redemptions Tab -->
        <div class="tab-content" id="redemptions">
            <div class="section-header">
                <h2>Recent Redemptions</h2>
                <a href="{{ route('admin.rewards.redemptions.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> View All
                </a>
            </div>

            @if($redemptions && $redemptions->count() > 0)
            <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Reward</th>
                        <th>Points Spent</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($redemptions->take(10) as $redemption)
                    <tr>
                        <td>{{ $redemption->customerProfile->user->name ?? 'N/A' }}</td>
                        <td>{{ $redemption->reward->title ?? 'N/A' }}</td>
                        <td>{{ $redemption->points_spent }} points</td>
                        <td>
                            @if($redemption->status === 'active')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($redemption->status === 'redeemed')
                                <span class="badge badge-success">Redeemed</span>
                            @elseif($redemption->status === 'expired')
                                <span class="badge badge-danger">Expired</span>
                            @else
                                <span class="badge badge-info">{{ ucfirst($redemption->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $redemption->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-exchange-alt"></i>
                <h3>No Redemptions Yet</h3>
                <p>Redemptions will appear here when customers redeem rewards</p>
            </div>
            @endif
        </div>

        <!-- Members Tab -->
        <div class="tab-content" id="members">
            <div class="section-header">
                <h2>Loyalty Members</h2>
                <a href="{{ route('admin.rewards.members.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> View All
                </a>
            </div>

            @if($members && $members->count() > 0)
            <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Points Balance</th>
                        <th>Tier</th>
                        <th>Member Since</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members->take(10) as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->points_balance ?? 0 }} points</td>
                        <td>
                            {{-- FIXED: Use calculatedTier (real-time) instead of loyaltyTier (static) --}}
                            @if(isset($member->calculatedTier) && $member->calculatedTier)
                                <span class="badge badge-info">{{ $member->calculatedTier->name }}</span>
                            @else
                                <span class="badge badge-warning">No tier</span>
                            @endif
                        </td>
                        <td>{{ $member->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No Members Yet</h3>
                <p>Members will appear here when they join the loyalty program</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Tab Switching
    document.querySelectorAll('.tab-nav-item').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-nav-item').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });

    // Auto-activate tab from session or URL hash
    document.addEventListener('DOMContentLoaded', function() {
        // Check for session active_tab first
        @if(session('active_tab'))
            const sessionTab = '{{ session("active_tab") }}';
            activateTab(sessionTab);
        @else
            // If no session, check URL hash
            if (window.location.hash) {
                const hashTab = window.location.hash.substring(1);
                activateTab(hashTab);
            }
        @endif
    });

    function activateTab(tabId) {
        // Find the tab button
        const tabButton = document.querySelector(`.tab-nav-item[data-tab="${tabId}"]`);
        if (tabButton) {
            // Remove active from all
            document.querySelectorAll('.tab-nav-item').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Activate the target tab
            tabButton.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }
    }
</script>
@endsection
