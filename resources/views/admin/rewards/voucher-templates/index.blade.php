@extends('layouts.admin')

@section('title', 'Voucher Templates Management')
@section('page-title', 'Voucher Templates')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Templates</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-ticket-alt"></i></div>
        </div>
        <div class="admin-card-value">{{ $templates->count() }}</div>
        <div class="admin-card-desc">Voucher templates</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Rewards</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-gift"></i></div>
        </div>
        <div class="admin-card-value">{{ $templates->sum('rewards_count') }}</div>
        <div class="admin-card-desc">Generated from templates</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Percentage Discounts</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-percentage"></i></div>
        </div>
        <div class="admin-card-value">{{ $templates->where('discount_type', 'percentage')->count() }}</div>
        <div class="admin-card-desc">Templates</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Fixed Amount</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="admin-card-value">{{ $templates->where('discount_type', 'fixed')->count() }}</div>
        <div class="admin-card-desc">Templates</div>
    </div>
</div>

<!-- Voucher Templates Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Voucher Templates</h2>
        <a href="{{ route('admin.rewards.voucher-templates.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            New Template
        </a>
    </div>

    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Discount Type</th>
                        <th>Discount Value</th>
                        <th>Expiry Days</th>
                        <th>Rewards Generated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $template->name }}</div>
                            </td>
                            <td>
                                @if($template->discount_type === 'percentage')
                                    <span class="badge badge-primary">Percentage</span>
                                @else
                                    <span class="badge badge-success">Fixed Amount</span>
                                @endif
                            </td>
                            <td>
                                @if($template->discount_type === 'percentage')
                                    {{ $template->discount_value }}%
                                @else
                                    RM {{ number_format($template->discount_value, 2) }}
                                @endif
                            </td>
                            <td>{{ $template->expiry_days }} days</td>
                            <td>{{ $template->rewards_count }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.voucher-templates.edit', $template->id) }}" class="admin-btn btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.rewards.voucher-templates.destroy', $template->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn btn-icon btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                    <i class="fas fa-ticket-alt" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No voucher templates found. Create your first template!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@include('admin.rewards._table_scroll_script')
@endsection
