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
        <div class="admin-card-value">{{ $todayBooking ?? 0 }}</div>
        <div class="admin-card-desc">Total reservations for today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Pending Bookings</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-clock"></i></div>
        </div>
        <div class="admin-card-value">{{ $pendingTables ?? 0 }}</div>
        <div class="admin-card-desc">Require confirmation</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Confirmed Today</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="admin-card-value">{{ $confirmedTables ?? 0 }}</div>
        <div class="admin-card-desc">Guests arriving today</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Tables</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-chair"></i></div>
        </div>
        <div class="admin-card-value">{{ $totalTables ?? 0 }}</div>
        <div class="admin-card-desc">Available for booking</div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">View Bookings</h2>
    </div>

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
    @if($reservations->count() > 0)
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
                @foreach($reservations as $reservation)
                <tr>
                    <td data-label="Booking ID">
                        <div class="booking-info">
                            <div class="booking-id">BK-{{ $reservation->id }}</div>
                        </div>
                    </td>
                    <td data-label="Customer">
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
                    <td data-label="Date & Time">
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
                    <td data-label="Party Size" class="cell-center">
                        <div class="party-size">{{ $reservation->party_size }} guests</div>
                    </td>
                    <td data-label="Table" class="cell-center">
                        <div class="table-info">
                            @if($reservation->table)
                            <strong>Table {{ $reservation->table->table_number }}</strong>
                            <div class="table-type">{{ $reservation->table->table_type }}</div>
                            @else
                            <span class="text-muted">Not assigned</span>
                            @endif
                        </div>
                    </td>
                    <td data-label="Status" class="cell-center">
                        <span class="status status-booking status-{{ str_replace('_', '-', $reservation->status) }}">
                            {{ str_replace('_', ' ', ucfirst($reservation->status)) }}
                        </span>
                    </td>
                    <td data-label="Order Time" class="cell-center">
                        <div class="time-info">
                            <div class="order-date">{{ $reservation->created_at->format('M d') }}</div>
                            <div class="order-time">{{ $reservation->created_at->format('g:i A') }}</div>
                        </div>
                    </td>
                    <td data-label="Actions" class="cell-center">
                        <div class="table-actions">
                            @switch($reservation->status)
                                @case('pending')
                                    <button class="action-btn confirm-btn" title="Confirm Booking" onclick="updateBookingStatus({{ $reservation->id }}, 'confirmed')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="action-btn cancel-btn" title="Cancel Booking" onclick="updateBookingStatus({{ $reservation->id }}, 'cancelled')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @break

                                @case('confirmed')
                                    <button class="action-btn seat-btn" title="Seat Customer" onclick="updateBookingStatus({{ $reservation->id }}, 'seated')">
                                        <i class="fas fa-chair"></i>
                                    </button>
                                    <button class="action-btn cancel-btn" title="Mark as No Show" onclick="updateBookingStatus({{ $reservation->id }}, 'no_show')">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                    <button class="action-btn cancel-btn" title="Cancel Booking" onclick="updateBookingStatus({{ $reservation->id }}, 'cancelled')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @break

                                @case('seated')
                                    <button class="action-btn complete-btn" title="Complete Booking" onclick="updateBookingStatus({{ $reservation->id }}, 'completed')">
                                        <i class="fas fa-flag-checkered"></i>
                                    </button>
                                    @break

                                @default
                                    <a href="{{ route('admin.table-reservation.show', $reservation->id) }}" class="action-btn view-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                            @endswitch
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
        <div style="display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-calendar-check" style="font-size: 48px; opacity: 0.5; margin-bottom: 16px;"></i>
            <p style="font-weight: 600; margin-bottom: 8px; font-size: 16px;">No bookings found</p>
            <p style="font-size: 14px; margin-bottom: 0;">
                @if(request()->hasAny(['search', 'status', 'date']))
                No bookings match your current filters. Try adjusting your search criteria.
                @else
                No bookings have been made yet.
                @endif
            </p>
            @if(!request()->hasAny(['search', 'status', 'date']))
            <div style="margin-top: 20px;">
                <a href="{{ route('admin.table-reservation.create') }}" class="admin-btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Booking
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Pagination -->
    @if($reservations->hasPages())
    <div class="pagination">
        <div style="display: flex; align-items: center; gap: 16px; margin-right: auto;">
            <span style="font-size: 14px; color: var(--text-2);">
                Showing {{ $reservations->firstItem() }} to {{ $reservations->lastItem() }} of {{ $reservations->total() }} results
            </span>
        </div>

        @if($reservations->onFirstPage())
        <span class="pagination-btn" style="opacity: 0.5; cursor: not-allowed;">
            <i class="fas fa-chevron-left"></i>
        </span>
        @else
        <a href="{{ $reservations->previousPageUrl() }}" class="pagination-btn">
            <i class="fas fa-chevron-left"></i>
        </a>
        @endif

        @foreach($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
        @if($page == $reservations->currentPage())
        <span class="pagination-btn active">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
        @endif
        @endforeach

        @if($reservations->hasMorePages())
        <a href="{{ $reservations->nextPageUrl() }}" class="pagination-btn">
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
    // Check for session messages on page load using the global Toast object
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

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('bookingStatusFilter');
        const dateFilter = document.getElementById('dateFilter');

        // Function to apply filters
        function applyFilters() {
            const params = new URLSearchParams(window.location.search);

            // Update or remove search parameter
            if (searchInput.value.trim()) {
                params.set('search', searchInput.value.trim());
            } else {
                params.delete('search');
            }

            // Update or remove status filter
            if (statusFilter.value) {
                params.set('status', statusFilter.value);
            } else {
                params.delete('status');
            }

            // Update or remove date filter
            if (dateFilter.value) {
                params.set('date', dateFilter.value);
            } else {
                params.delete('date');
            }

            // Redirect with parameters
            const url = '{{ route("admin.table-reservation.index") }}' + '?' + params.toString();
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
    });

    // Function to update booking status
    function updateBookingStatus(bookingId, status) {
        const messages = {
            'confirmed': 'Are you sure you want to confirm this booking?',
            'seated': 'Are you sure you want to mark this customer as seated?',
            'completed': 'Are you sure you want to complete this booking?',
            'cancelled': 'Are you sure you want to cancel this booking?',
            'no_show': 'Are you sure you want to mark this as a no-show?',
        };

        const message = messages[status] || `Are you sure you want to mark this booking as ${status.replace('_', ' ')}?`;

        showConfirm('Confirm Action', message, 'warning', 'Yes, Proceed', 'Cancel')
            .then((confirmed) => {
                if (confirmed) {
                    // Create a form and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/table-reservation/${bookingId}/update-status`;

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

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
            });
    }
</script>
@endsection