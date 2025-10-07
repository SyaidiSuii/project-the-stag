@extends('layouts.admin')

@section('title', 'Bonus Points Challenges')
@section('page-title', 'Bonus Points Challenges')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Bonus Points Challenges</h2>
        <a href="{{ route('admin.rewards.bonus-challenges.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i> New Challenge
        </a>
    </div>

    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Challenge Name</th>
                        <th>Description</th>
                        <th>Bonus Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($challenges as $challenge)
                        <tr>
                            <td><div style="font-weight: 600;">{{ $challenge->name }}</div></td>
                            <td>{{ Str::limit($challenge->description, 60) }}</td>
                            <td><span class="badge badge-primary">{{ $challenge->bonus_points }} pts</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.bonus-challenges.edit', $challenge->id) }}" class="admin-btn btn-icon"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.rewards.bonus-challenges.destroy', $challenge->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this challenge?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-btn btn-icon btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px;">
                                <div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;">
                                    <i class="fas fa-star" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
                                    <p>No challenges found.</p>
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
