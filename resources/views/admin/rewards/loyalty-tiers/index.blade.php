@extends('layouts.admin')
@section('title', 'Loyalty Tiers')
@section('page-title', 'Loyalty Tiers')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Loyalty Tiers</h2>
        <a href="{{ route('admin.rewards.loyalty-tiers.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i> New Tier
        </a>
    </div>
    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead><tr><th>Tier Name</th><th>Min. Spending</th><th>Points Multiplier</th><th>Sort Order</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($tiers as $tier)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                                        @if($tier->icon && !str_contains($tier->icon, 'fa-'))
                                            <span style="font-size: 20px;">{{ $tier->icon }}</span>
                                        @else
                                            <i class="{{ $tier->icon ?? 'fas fa-layer-group' }}" style="color: #94a3b8;"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $tier->name }}</div>
                                        <div style="font-size: 13px; color: #64748b;">{{ Str::limit($tier->benefits, 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>RM {{ number_format($tier->minimum_spending, 2) }}</td>
                            <td><span class="badge badge-primary">{{ $tier->points_multiplier }}x</span></td>
                            <td>{{ $tier->sort_order ?? '-' }}</td>
                            <td>
                                @if($tier->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.loyalty-tiers.edit', $tier->id) }}" class="admin-btn btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.rewards.loyalty-tiers.destroy', $tier->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tier?')">
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
                                    <i class="fas fa-layer-group" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No tiers found.</p>
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
