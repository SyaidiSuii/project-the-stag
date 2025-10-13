@extends('layouts.admin')

@section('title', 'QR Codes Management')
@section('page-title', 'QR Codes Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View QR Codes</h2>
    </div>

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search qr codes by table..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="orderStatusFilter">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <input type="date" class="filter-select" id="dateFilter" value="{{ request('date') }}">
        </div>
        <a href="{{ route('admin.table-qrcodes.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New QR Code
        </a>
    </div>

    <!-- OR Codes Table -->
    @if($sessions->count() > 0)
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="th-order">Table Number</th>
                        <th class="th-customer">Reservation ID</th>
                        <th class="th-time">Stared At</th>
                        <th class="th-time">Expired At</th>
                        <th class="th-status">Status</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td>
                            <div class="table-info">
                                <strong>{{ $session->table->table_number ?? 'Unknown' }}</strong>
                                <div class="table-type">{{ $session->table->table_type ?? '' }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="reservation-info">
                                @if($session->reservation)
                                    <div class="reservation-id">RES-{{ $session->reservation->id }}</div>
                                    <div class="guest-name">{{ $session->reservation->guest_name }}</div>
                                @else
                                    <span class="text-muted">No Reservation</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="time-info">
                                <div class="date">{{ $session->started_at->format('M d, Y') }}</div>
                                <div class="time">{{ $session->started_at->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="time-info">
                                <div class="date">{{ $session->expires_at->format('M d, Y') }}</div>
                                <div class="time">{{ $session->expires_at->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td class="cell-center">
                            <span class="status status-{{ $session->status }}">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td class="cell-center">
                            <div class="table-actions">
                                @if($session->status === 'active')
                                    <a href="{{ route('admin.table-qrcodes.qr-preview', [$session, 'png']) }}" 
                                       class="action-btn view-btn" title="View QR Code" target="_blank">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                    <a href="{{ route('admin.table-qrcodes.download-qr', [$session, 'png']) }}" 
                                       class="action-btn download-btn" title="Download QR PNG">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="action-btn refresh-btn" title="Regenerate QR Code" onclick="regenerateQR({{ $session->id }})">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                @endif
                                
                                <a href="{{ route('admin.table-qrcodes.show', $session) }}" 
                                   class="action-btn view-btn" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($session->status === 'active')
                                    <button class="action-btn complete-btn" title="Complete Session" onclick="completeSession({{ $session->id }})">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endif
                                
                                @if($session->orders()->count() == 0)
                                    <form method="POST" action="{{ route('admin.table-qrcodes.destroy', $session) }}" style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this QR code session?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="Delete Session">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State Outside Table -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="empty-state-title">No QR codes found</div>
            <div class="empty-state-text">
                @if(request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                    No QR codes match your current filters. Try adjusting your search criteria.
                @else
                    No QR codes have been generated yet.
                @endif
            </div>
            @if(!request()->hasAny(['search', 'order_status', 'order_type', 'payment_status', 'date']))
                <div style="margin-top: 20px;">
                    <a href="{{ route('admin.table-qrcodes.create') }}" class="admin-btn btn-primary">
                        <i class="fas fa-plus"></i> Create First QR Code
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Pagination -->
    @if($sessions->hasPages())
        <div class="pagination">
            <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
                <span style="font-size: 14px; color: var(--text-2);">
                    Showing {{ $sessions->firstItem() }} to {{ $sessions->lastItem() }} of {{ $sessions->total() }} results
                </span>
            </div>
            
            @if($sessions->onFirstPage())
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $sessions->previousPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($sessions->getUrlRange(1, $sessions->lastPage()) as $page => $url)
                @if($page == $sessions->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($sessions->hasMorePages())
                <a href="{{ $sessions->nextPageUrl() }}" class="pagination-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>

// Regenerate QR Code function
function regenerateQR(sessionId) {
    showConfirm(
        'Regenerate QR Code?',
        'The old QR code will no longer work. Are you sure you want to continue?',
        'warning',
        'Regenerate',
        'Cancel'
    ).then(confirmed => {
        if (!confirmed) return;

        fetch('{{ url("admin/table-qrcodes") }}/' + sessionId + '/regenerate-qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success('Success!', 'QR code regenerated successfully');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                Toast.error('Error', data.message || 'Failed to regenerate QR code');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to regenerate QR code. Please try again.');
        });
    });
}

// Complete Session function
function completeSession(sessionId) {
    showConfirm(
        'Complete Session?',
        'This will mark the QR code session as complete and make the table available.',
        'info',
        'Complete',
        'Cancel'
    ).then(confirmed => {
        if (!confirmed) return;

        fetch('{{ url("admin/table-qrcodes") }}/' + sessionId + '/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success('Success!', 'Session completed successfully');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                Toast.error('Error', data.message || 'Failed to complete session');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to complete session. Please try again.');
        });
    });
}

// Check for session messages on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(session('message'))
        Toast.success('Success', '{{ session('message') }}');
    @endif

    @if(session('success'))
        Toast.success('Success', '{{ session('success') }}');
    @endif

    @if(session('error'))
        Toast.error('Error', '{{ session('error') }}');
    @endif
});
</script>
@endsection