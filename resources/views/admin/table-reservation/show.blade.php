@extends('layouts.admin')

@section('title', 'Booking Details - BK' . $tableReservation->id)
@section('page-title', 'Booking Details - BK' . $tableReservation->id)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Booking BK{{ $tableReservation->id }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.table-reservation.edit', $tableReservation->id) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Reservation
            </a>
            <a href="{{ route('admin.table-reservation.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Quick Status Update -->
    <div class="form-group">
        <label class="form-label">Quick Status Update</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <form method="POST" action="{{ route('admin.table-reservation.update-status', $tableReservation->id) }}" style="display: flex; gap: 12px; align-items: end;">
                @csrf
                <div style="flex: 1;">
                    <label style="font-size: 12px; color: #6b7280; font-weight: 600; display: block; margin-bottom: 4px;">New Status</label>
                    <select name="status" class="form-control" style="margin-bottom: 0;">
                        <option value="pending" {{ $tableReservation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $tableReservation->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="seated" {{ $tableReservation->status == 'seated' ? 'selected' : '' }}>Seated</option>
                        <option value="completed" {{ $tableReservation->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $tableReservation->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ $tableReservation->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 12px; color: #6b7280; font-weight: 600; display: block; margin-bottom: 4px;">Notes (Optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="Add a note about this status change..." style="margin-bottom: 0;">
                </div>
                <button type="submit" class="btn-save" style="margin-bottom: 0;">Update Status</button>
            </form>
        </div>
    </div>

    <!-- Reservation Details -->
    <div class="form-group">
        <label class="form-label">Reservation Details</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CONFIRMATION CODE</span>
                    <p style="font-family: monospace; font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">{{ $tableReservation->confirmation_code }}</p>
                </div>
                
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">STATUS</span>
                    <div style="margin-top: 4px;">
                        <span style="background: 
                            @if($tableReservation->status == 'pending') #fef3c7; color: #92400e;
                            @elseif($tableReservation->status == 'confirmed') #dbeafe; color: #1e40af;
                            @elseif($tableReservation->status == 'seated') #e9d5ff; color: #6b21a8;
                            @elseif($tableReservation->status == 'completed') #d1fae5; color: #065f46;
                            @elseif($tableReservation->status == 'cancelled') #fee2e2; color: #991b1b;
                            @elseif($tableReservation->status == 'no_show') #f3f4f6; color: #374151;
                            @else #f3f4f6; color: #374151; @endif
                            padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600; text-transform: capitalize;">
                            {{ str_replace('_', ' ', $tableReservation->status) }}
                        </span>
                    </div>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">DATE</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">
                        @if($tableReservation->booking_date instanceof \Carbon\Carbon)
                            {{ $tableReservation->booking_date->format('M d, Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($tableReservation->booking_date)->format('M d, Y') }}
                        @endif
                    </p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TIME</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">
                        @if($tableReservation->booking_time instanceof \Carbon\Carbon)
                            {{ $tableReservation->booking_time->format('g:i A') }}
                        @else
                            {{ \Carbon\Carbon::parse($tableReservation->booking_time)->format('g:i A') }}
                        @endif
                    </p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">NUMBER OF GUESTS</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $tableReservation->party_size }} people</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE</span>
                    @if($tableReservation->table)
                        <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $tableReservation->table->table_number }}</p>
                        <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0; text-transform: capitalize;">
                            {{ $tableReservation->table->table_type }} ({{ $tableReservation->table->capacity }} capacity)
                        </p>
                    @else
                        <p style="color: #6b7280; margin: 4px 0 0 0;">No table assigned</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Information -->
    <div class="form-group">
        <label class="form-label">Guest Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">NAME</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">
                        {{ $tableReservation->user ? $tableReservation->user->name : $tableReservation->guest_name }}
                    </p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PHONE</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $tableReservation->guest_phone }}</p>
                </div>

                @if($tableReservation->guest_email)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">EMAIL</span>
                    <p style="font-size: 16px; margin: 4px 0 0 0;">{{ $tableReservation->guest_email }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="form-group">
        <label class="form-label">System Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED BY</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">
                        {{ $tableReservation->user ? $tableReservation->user->name : 'System' }}
                    </p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED AT</span>
                    <p style="font-size: 16px; margin: 4px 0 0 0;">{{ $tableReservation->created_at->format('M d, Y g:i A') }}</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">REMINDER SENT</span>
                    <p style="font-size: 16px; margin: 4px 0 0 0;">{{ $tableReservation->reminder_sent ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Timeline -->
    <div class="form-group">
        <label class="form-label">Status Timeline</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="padding: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-plus" style="color: #065f46; font-size: 16px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="font-size: 16px; font-weight: 600; margin: 0 0 4px 0;">Reservation created</p>
                        <p style="font-size: 14px; color: #6b7280; margin: 0;">{{ $tableReservation->created_at->format('M d, g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Table management scripts can be added here
</script>
@endsection