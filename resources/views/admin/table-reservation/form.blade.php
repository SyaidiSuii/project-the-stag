@extends('layouts.admin')

@section('title', $reservation->id ? 'Edit Table Reservation' : 'Create Table Reservation')
@section('page-title', $reservation->id ? 'Edit Table Reservation' : 'Create Table Reservation')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            @if($reservation->id)
                Edit Table Reservation - #{{ $reservation->id }}
            @else
                Create New Table Reservation
            @endif
        </h2>
        <a href="{{ route('admin.table-reservation.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Reservations
        </a>
    </div>

    @if($reservation->id)
        <form method="POST" action="{{ route('admin.table-reservation.update', $reservation->id) }}" class="reservation-form">
            @method('PUT')
    @else
        <form method="POST" action="{{ route('admin.table-reservation.store') }}" class="reservation-form">
    @endif
        @csrf

        <!-- Guest Information -->
        <div class="form-section">
            <h3 class="section-subtitle">Guest Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="user_id" class="form-label">User ID</label>
                    <select 
                        id="user_id" 
                        name="user_id" 
                        class="form-control @error('user_id') is-invalid @enderror">
                        <option value="">Select User (Optional)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                    {{ old('user_id', $reservation->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Leave empty for walk-in guests</div>
                </div>

                <div class="form-group">
                    <label for="guest_name" class="form-label">Guest Name *</label>
                    <input 
                        type="text" 
                        id="guest_name" 
                        name="guest_name" 
                        class="form-control @error('guest_name') is-invalid @enderror"
                        value="{{ old('guest_name', $reservation->guest_name) }}"
                        placeholder="Enter guest name"
                        required>
                    @error('guest_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="guest_email" class="form-label">Guest Email</label>
                    <input 
                        type="email" 
                        id="guest_email" 
                        name="guest_email" 
                        class="form-control @error('guest_email') is-invalid @enderror"
                        value="{{ old('guest_email', $reservation->guest_email) }}"
                        placeholder="Enter guest email">
                    @error('guest_email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Guest Phone *</label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $reservation->phone) }}"
                        placeholder="Enter phone number"
                        required>
                    @error('phone')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="form-section">
            <h3 class="section-subtitle">Booking Details</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="table_id" class="form-label">Select Table *</label>
                    <select 
                        id="table_id" 
                        name="table_id" 
                        class="form-control @error('table_id') is-invalid @enderror"
                        required>
                        <option value="">Select Table</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" 
                                    {{ old('table_id', $reservation->table_id) == $table->id ? 'selected' : '' }}>
                                Table {{ $table->table_number }} - {{ $table->status }} ({{ $table->capacity }} seats)
                            </option>
                        @endforeach
                    </select>
                    @error('table_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="party_size" class="form-label">Party Size *</label>
                    <input 
                        type="number" 
                        id="party_size" 
                        name="party_size" 
                        class="form-control @error('party_size') is-invalid @enderror"
                        value="{{ old('party_size', $reservation->party_size) }}"
                        min="1"
                        max="20"
                        placeholder="Number of guests"
                        required>
                    @error('party_size')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="booking_date" class="form-label">Booking Date *</label>
                    <input 
                        type="date" 
                        id="booking_date" 
                        name="booking_date" 
                        class="form-control @error('booking_date') is-invalid @enderror"
                        value="{{ old('booking_date', $reservation->booking_date ? $reservation->booking_date->format('Y-m-d') : '') }}"
                        required>
                    @error('booking_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="booking_time" class="form-label">Booking Time *</label>
                    <input 
                        type="time" 
                        id="booking_time" 
                        name="booking_time" 
                        class="form-control @error('booking_time') is-invalid @enderror"
                        value="{{ old('booking_time', $reservation->booking_time ? $reservation->booking_time : '') }}"
                        required>
                    @error('booking_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Special Requests & Status -->
        <div class="form-section">
            <h3 class="section-subtitle">Additional Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="status" class="form-label">Status *</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="form-control @error('status') is-invalid @enderror"
                        required>
                        <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="seated" {{ old('status', $reservation->status) == 'seated' ? 'selected' : '' }}>Seated</option>
                        <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ old('status', $reservation->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="special_requests" class="form-label">Special Requests</label>
                <textarea 
                    id="special_requests" 
                    name="special_requests" 
                    class="form-control @error('special_requests') is-invalid @enderror"
                    rows="3"
                    placeholder="Any special requests or dietary requirements">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                @error('special_requests')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Internal Notes</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    class="form-control @error('notes') is-invalid @enderror"
                    rows="3"
                    placeholder="Internal notes (not visible to guest)">{{ old('notes', $reservation->notes) }}</textarea>
                @error('notes')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Current Confirmation Code -->
        @if($reservation->id && $reservation->confirmation_code)
            <div class="form-section">
                <h3 class="section-subtitle">Current Confirmation Code</h3>
                <div class="confirmation-display">
                    <div class="confirmation-code-box">
                        <span class="confirmation-code">{{ $reservation->confirmation_code }}</span>
                        <small class="text-muted">This code was generated for the guest</small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Current Reservation Information (for edit) -->
        @if($reservation->id)
            <div class="form-section">
                <h3 class="section-subtitle">Reservation Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Reservation ID:</span>
                        <span class="info-value">#{{ $reservation->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created:</span>
                        <span class="info-value">{{ $reservation->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($reservation->updated_at != $reservation->created_at)
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span>
                        <span class="info-value">{{ $reservation->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                {{ $reservation->id ? 'Update Reservation' : 'Save Reservation' }}
            </button>
            <a href="{{ route('admin.table-reservation.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
// Add any specific JavaScript for table reservation form here
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update table capacity info when table is selected
    const tableSelect = document.getElementById('table_id');
    const partySizeInput = document.getElementById('party_size');
    
    if (tableSelect && partySizeInput) {
        tableSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Extract capacity from option text (assuming format includes capacity)
                const text = selectedOption.textContent;
                const capacityMatch = text.match(/\((\d+) seats\)/);
                if (capacityMatch) {
                    const capacity = parseInt(capacityMatch[1]);
                    partySizeInput.setAttribute('max', capacity);
                    
                    // Warn if current party size exceeds capacity
                    if (parseInt(partySizeInput.value) > capacity) {
                        partySizeInput.style.borderColor = '#ef4444';
                        // You could add a warning message here
                    } else {
                        partySizeInput.style.borderColor = '';
                    }
                }
            }
        });
        
        // Trigger on page load if table is already selected
        tableSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection