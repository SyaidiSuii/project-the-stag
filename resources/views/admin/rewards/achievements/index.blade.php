@extends('layouts.admin')

@section('title', 'Achievements Management')
@section('page-title', 'Achievements')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Achievements</h2>
        <a href="{{ route('admin.rewards.achievements.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            New Achievement
        </a>
    </div>

    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Achievement</th>
                        <th>Description</th>
                        <th>Points Reward</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($achievements as $achievement)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    @if($achievement->icon)
                                        <i class="{{ $achievement->icon }}" style="font-size: 24px; color: #6366f1;"></i>
                                    @else
                                        <i class="fas fa-trophy" style="font-size: 24px; color: #94a3b8;"></i>
                                    @endif
                                    <div style="font-weight: 600;">{{ $achievement->name }}</div>
                                </div>
                            </td>
                            <td>{{ Str::limit($achievement->description, 50) }}</td>
                            <td><span class="badge badge-primary">{{ $achievement->points_reward }} pts</span></td>
                            <td>{{ $achievement->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.achievements.edit', $achievement->id) }}" class="admin-btn btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.rewards.achievements.destroy', $achievement->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="5" style="text-align: center; padding: 40px;">
                                <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                    <i class="fas fa-trophy" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No achievements found.</p>
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
