@extends('layouts.admin')
@section('title', 'Loyalty Program Members')
@section('page-title', 'Loyalty Program Members')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Loyalty Program Members</h2>
    </div>
    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Points Balance</th>
                        <th>Current Tier</th>
                        <th>Total Spent</th>
                        <th>Member Since</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user" style="color: #94a3b8;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $member->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $member->email }}</td>
                            <td><span class="badge badge-primary">{{ $member->points_balance }} pts</span></td>
                            <td>
                                @if($member->customerProfile && $member->customerProfile->loyaltyTier)
                                    <span class="badge badge-success">{{ $member->customerProfile->loyaltyTier->name }}</span>
                                @else
                                    <span class="badge badge-secondary">No Tier</span>
                                @endif
                            </td>
                            <td>RM {{ number_format($member->orders()->where('payment_status', 'paid')->sum('total_amount'), 2) }}</td>
                            <td>{{ $member->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                    <i class="fas fa-users" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No members found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.rewards._table_scroll_script')
@endsection
