@extends('layouts.admin')
@section('title', 'Reward Redemptions')
@section('page-title', 'Reward Redemptions')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Reward Redemptions History</h2>
    </div>
    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Reward</th>
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
                            <td>
                                @if($redemption->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($redemption->status == 'redeemed')
                                    <span class="badge badge-primary">Redeemed</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($redemption->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $redemption->expiry_date ? $redemption->expiry_date->format('d M Y') : 'No Expiry' }}</td>
                            <td>
                                <div class="action-buttons">
                                    @if($redemption->status == 'active')
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
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                    <i class="fas fa-ticket-alt" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No redemptions found.</p>
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
@endsection

@section('scripts')
@include('admin.rewards._table_scroll_script')
@endsection
