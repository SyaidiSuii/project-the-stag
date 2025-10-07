@extends('layouts.admin')
@section('title', 'Voucher Collections')
@section('page-title', 'Voucher Collections')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Voucher Collections</h2>
        <a href="{{ route('admin.rewards.voucher-collections.create') }}" class="admin-btn btn-primary">
            <i class="fas fa-plus"></i> New Collection
        </a>
    </div>
    <div class="section-content">
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Collection Name</th><th>Description</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($collections as $collection)
                        <tr>
                            <td><div style="font-weight: 600;">{{ $collection->name }}</div></td>
                            <td>{{ Str::limit($collection->description, 60) }}</td>
                            <td>{{ $collection->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rewards.voucher-collections.edit', $collection->id) }}" class="admin-btn btn-icon"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.rewards.voucher-collections.destroy', $collection->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this collection?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="admin-btn btn-icon btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; padding: 40px;"><div style="display: flex; flex-direction: column; align-items: center; color: #94a3b8;"><i class="fas fa-folder-open" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i><p>No collections found.</p></div></td></tr>
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
