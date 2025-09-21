@extends('layouts.admin')

@section('title', 'Booking Management')
@section('page-title', 'Booking Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Bookings Today</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-calendar-day"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Total reservations for today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Pending Bookings</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Require confirmation</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Confirmed Today</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Guests arriving today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Tables</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-chair"></i></div>
        </div>
        <div class="admin-card-value">0</div>
        <div class="admin-card-desc">Available for booking</div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Bookings</h2>
    </div>
    
    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="search-filter">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search bookings by ID, customer, confirmation..." id="searchInput" value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="bookingStatusFilter">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="seated" {{ request('status') == 'seated' ? 'selected' : '' }}>Seated</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
            </select>
            <input type="date" class="filter-select" id="dateFilter" value="{{ request('date') }}">
        </div>
        <a href="{{ route('admin.table-reservation.create') }}" class="admin-btn btn-primary">
            <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
            Create New Booking
        </a>
    </div>

    <!-- booking Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="th-order">Booking ID</th>
                    <th class="th-customer">Customer</th>
                    <th class="th-type">Date & Time</th>
                    <th class="th-amount">Part Size</th>
                    <th class="th-status">Table</th>
                    <th class="th-eta">Status</th>
                    <th class="th-time">Order Time</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                <tr>
                    <td>
                        <div class="booking-info">
                            <div class="booking-id">BK-{{ $reservation->id }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-name">
                                {{ $reservation->user ? $reservation->user->name : $reservation->guest_name }}
                            </div>
                            @if($reservation->guest_phone)
                                <div class="customer-phone">{{ $reservation->guest_phone }}</div>
                            @endif
                            @if(!$reservation->user && $reservation->guest_email)
                                <div class="customer-email">{{ $reservation->guest_email }}</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="datetime-info">
                            <div class="booking-date">
                                @if($reservation->booking_date instanceof \Carbon\Carbon)
                                    {{ $reservation->booking_date->format('M d, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($reservation->booking_date)->format('M d, Y') }}
                                @endif
                            </div>
                            <div class="booking-time">
                                @if($reservation->booking_time instanceof \Carbon\Carbon)
                                    {{ $reservation->booking_time->format('g:i A') }}
                                @else
                                    {{ \Carbon\Carbon::parse($reservation->booking_time)->format('g:i A') }}
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="party-size">{{ $reservation->party_size }} guests</div>
                    </td>
                    <td class="cell-center">
                        <div class="table-info">
                            @if($reservation->table)
                                <strong>Table {{ $reservation->table->table_number }}</strong>
                                <div class="table-type">{{ $reservation->table->table_type }}</div>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </div>
                    </td>
                    <td class="cell-center">
                        <span class="status status-booking status-{{ str_replace('_', '-', $reservation->status) }}">
                            {{ str_replace('_', ' ', ucfirst($reservation->status)) }}
                        </span>
                    </td>
                    <td class="cell-center">
                        <div class="time-info">
                            <div class="order-date">{{ $reservation->created_at->format('M d') }}</div>
                            <div class="order-time">{{ $reservation->created_at->format('g:i A') }}</div>
                        </div>
                    </td>
                    <td class="cell-center">
                        <div class="table-actions">
                            <!-- Status Update Buttons -->
                            @if($reservation->status === 'pending')
                                <button class="action-btn confirm-btn" title="Confirm Booking" onclick="updateBookingStatus({{ $reservation->id }}, 'confirmed')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="action-btn cancel-btn" title="Cancel Booking" onclick="updateBookingStatus({{ $reservation->id }}, 'cancelled')">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($reservation->status === 'confirmed')
                                <button class="action-btn ready-btn" title="Mark as Seated" onclick="updateBookingStatus({{ $reservation->id }}, 'seated')">
                                    <i class="fas fa-chair"></i>
                                </button>
                                <button class="action-btn cancel-btn" title="Mark as No Show" onclick="updateBookingStatus({{ $reservation->id }}, 'no_show')">
                                    <i class="fas fa-user-times"></i>
                                </button>
                            @elseif($reservation->status === 'seated')
                                <button class="action-btn complete-btn" title="Mark as Completed" onclick="updateBookingStatus({{ $reservation->id }}, 'completed')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            @endif

                            <!-- Default Action Buttons -->
                            <a href="{{ route('admin.table-reservation.show', $reservation->id) }}" 
                               class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.table-reservation.edit', $reservation->id) }}" 
                               class="action-btn edit-btn" title="Edit Booking">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!in_array($reservation->status, ['completed', 'cancelled']))
                                <form method="POST" action="{{ route('admin.table-reservation.destroy', $reservation->id) }}" style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete Booking">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="empty-state-title">No bookings found</div>
                        <div class="empty-state-text">
                            @if(request()->hasAny(['search', 'status', 'date']))
                                No bookings match your current filters. Try adjusting your search criteria.
                            @else
                                No bookings have been made yet.
                            @endif
                        </div>
                        @if(!request()->hasAny(['search', 'status', 'date']))
                            <div style="margin-top: 20px;">
                                <a href="{{ route('admin.table-reservation.create') }}" class="admin-btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Booking
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('bookingStatusFilter');
    const dateFilter = document.getElementById('dateFilter');

    // Function to apply filters
    function applyFilters() {
        const params = new URLSearchParams();
        
        // Add search parameter
        if (searchInput.value.trim()) {
            params.append('search', searchInput.value.trim());
        }
        
        // Add status filter
        if (statusFilter.value) {
            params.append('status', statusFilter.value);
        }
        
        // Add date filter
        if (dateFilter.value) {
            params.append('date', dateFilter.value);
        }
        
        // Redirect with parameters
        const url = '{{ route("admin.table-reservation.index") }}' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    }

    // Search input handler with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500); // 500ms delay
    });

    // Status filter change handler
    statusFilter.addEventListener('change', applyFilters);

    // Date filter change handler
    dateFilter.addEventListener('change', applyFilters);

    // Clear filters function
    window.clearFilters = function() {
        searchInput.value = '';
        statusFilter.value = '';
        dateFilter.value = '';
        window.location.href = '{{ route("admin.table-reservation.index") }}';
    };
});

// Function to update booking status
function updateBookingStatus(bookingId, status) {
    if (!confirm(`Are you sure you want to mark this booking as ${status.replace('_', ' ')}?`)) {
        return;
    }

    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/table-reservation/${bookingId}/status`;
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add method override for PATCH
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PATCH';
    form.appendChild(methodInput);
    
    // Add status
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);
    
    // Submit form
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection