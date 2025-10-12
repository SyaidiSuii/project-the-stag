@extends('layouts.admin')

@section('title', 'QR Code Session Details')
@section('page-title', 'QR Code Session Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">QR Code Session #{{ $tableQrcode->id }}</h2>
        <div class="section-controls">
            @if($tableQrcode->status === 'active')
                <a href="{{ route('admin.table-qrcodes.qr-preview', [$tableQrcode, 'png']) }}" class="btn-save" target="_blank">
                    <i class="fas fa-qrcode"></i> View QR Code
                </a>
                <a href="{{ route('admin.table-qrcodes.print', $tableQrcode) }}" class="btn-save" target="_blank" style="background: #7c3aed;">
                    <i class="fas fa-print"></i> Print QR
                </a>
                <a href="{{ route('admin.table-qrcodes.download-qr', [$tableQrcode, 'png']) }}" class="btn-save" style="background: #3b82f6;">
                    <i class="fas fa-download"></i> Download QR
                </a>
            @endif
            <a href="{{ route('admin.table-qrcodes.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to QR Codes
            </a>
        </div>
    </div>

    <!-- Session Summary -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Session Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">SESSION ID</span>
                        <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">#{{ $tableQrcode->id }}</p>
                    </div>
                    
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">SESSION CODE</span>
                        <p style="font-family: monospace; font-size: 16px; font-weight: 700; margin: 4px 0 0 0;">{{ $tableQrcode->session_code }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE</span>
                        <p style="font-size: 24px; font-weight: 700; color: #10b981; margin: 4px 0 0 0;">{{ $tableQrcode->table->table_number ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CREATED AT</span>
                        <p style="margin: 4px 0 0 0;">{{ $tableQrcode->started_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Guest Information</label>
            <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
                @if($tableQrcode->guest_name || $tableQrcode->guest_phone)
                    @if($tableQrcode->guest_name)
                    <div style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">GUEST NAME</span>
                        <p style="font-size: 18px; font-weight: 600; margin: 4px 0 0 0;">{{ $tableQrcode->guest_name }}</p>
                    </div>
                    @endif

                    @if($tableQrcode->guest_phone)
                    <div style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PHONE</span>
                        <p style="margin: 4px 0 0 0;">
                            <a href="tel:{{ $tableQrcode->guest_phone }}" style="color: #3b82f6; text-decoration: none;">
                                {{ $tableQrcode->guest_phone }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($tableQrcode->guest_count)
                    <div>
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">GUEST COUNT</span>
                        <p style="margin: 4px 0 0 0;">{{ $tableQrcode->guest_count }} people</p>
                    </div>
                    @endif
                @else
                    <div style="color: #6b7280; text-align: center; padding: 20px;">
                        <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p style="font-weight: 600;">Guest information not provided</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Information -->
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Session Status</label>
            <div class="checkbox-group" style="background: 
                @if($tableQrcode->status == 'active') #d1fae5
                @elseif($tableQrcode->status == 'completed') #e0e7ff
                @elseif($tableQrcode->status == 'expired') #fee2e2
                @else #f3f4f6 @endif;">
                <i class="fas fa-
                    @if($tableQrcode->status == 'active') play-circle
                    @elseif($tableQrcode->status == 'completed') check-circle
                    @elseif($tableQrcode->status == 'expired') times-circle
                    @else info-circle @endif" 
                   style="color: 
                    @if($tableQrcode->status == 'active') #10b981
                    @elseif($tableQrcode->status == 'completed') #6366f1
                    @elseif($tableQrcode->status == 'expired') #ef4444
                    @else #6b7280 @endif;"></i>
                <span style="color: 
                    @if($tableQrcode->status == 'active') #065f46
                    @elseif($tableQrcode->status == 'completed') #3730a3
                    @elseif($tableQrcode->status == 'expired') #991b1b
                    @else #374151 @endif; font-weight: 600; text-transform: capitalize;">
                    {{ ucfirst($tableQrcode->status) }}
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Time Remaining</label>
            <div class="checkbox-group" style="background: 
                @if($tableQrcode->isExpired()) #fee2e2
                @elseif($tableQrcode->time_remaining && $tableQrcode->time_remaining < 30) #fef3c7
                @else #d1fae5 @endif;">
                <i class="fas fa-
                    @if($tableQrcode->isExpired()) clock
                    @elseif($tableQrcode->time_remaining && $tableQrcode->time_remaining < 30) hourglass-half
                    @else hourglass-start @endif" 
                   style="color: 
                    @if($tableQrcode->isExpired()) #ef4444
                    @elseif($tableQrcode->time_remaining && $tableQrcode->time_remaining < 30) #d97706
                    @else #10b981 @endif;"></i>
                <span style="color: 
                    @if($tableQrcode->isExpired()) #991b1b
                    @elseif($tableQrcode->time_remaining && $tableQrcode->time_remaining < 30) #92400e
                    @else #065f46 @endif; font-weight: 600;">
                    @if($tableQrcode->isExpired())
                        Expired
                    @elseif($tableQrcode->time_remaining)
                        {{ $tableQrcode->time_remaining }} minutes remaining
                    @else
                        Active
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Table Information -->
    @if($tableQrcode->table)
    <div class="form-group">
        <label class="form-label">Table Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE NUMBER</span>
                    <p style="font-size: 18px; font-weight: 600; margin: 4px 0;">{{ $tableQrcode->table->table_number }}</p>
                </div>
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE TYPE</span>
                    <p style="margin: 4px 0;">{{ ucfirst($tableQrcode->table->table_type) }}</p>
                </div>
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CAPACITY</span>
                    <p style="margin: 4px 0;">{{ $tableQrcode->table->capacity }} people</p>
                </div>
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE STATUS</span>
                    <p style="margin: 4px 0;">{{ ucfirst($tableQrcode->table->status) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reservation Information -->
    @if($tableQrcode->reservation)
    <div class="form-group">
        <label class="form-label">Related Reservation</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">RESERVATION ID</span>
                    <p style="font-family: monospace; font-size: 16px; font-weight: 700; margin: 4px 0;">RES-{{ $tableQrcode->reservation->id }}</p>
                </div>
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">GUEST NAME</span>
                    <p style="margin: 4px 0;">{{ $tableQrcode->reservation->guest_name }}</p>
                </div>
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">GUEST PHONE</span>
                    <p style="margin: 4px 0;">{{ $tableQrcode->reservation->guest_phone }}</p>
                </div>
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">PARTY SIZE</span>
                    <p style="margin: 4px 0;">{{ $tableQrcode->reservation->party_size }} people</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Special Notes -->
    @if($tableQrcode->notes)
    <div class="form-group">
        <label class="form-label">Special Notes</label>
        <div style="border: 1px solid #f59e0b; border-radius: 12px; padding: 16px; background: #fffbeb;">
            <div style="background: #fef3c7; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <i class="fas fa-sticky-note" style="color: #d97706; margin-right: 8px;"></i>
                <span style="color: #92400e; font-weight: 500;">{{ $tableQrcode->notes }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Timing Information -->
    <div class="form-group">
        <label class="form-label">Timing Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">SESSION STARTED</span>
                    <p style="margin: 4px 0 0 0;">{{ $tableQrcode->started_at->format('M d, Y h:i A') }}</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">EXPIRES AT</span>
                    <p style="margin: 4px 0 0 0;">{{ $tableQrcode->expires_at->format('M d, Y h:i A') }}</p>
                    @if($tableQrcode->isExpired())
                        <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; margin-top: 4px; display: inline-block;">
                            EXPIRED
                        </span>
                    @endif
                </div>

                @if($tableQrcode->completed_at)
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">COMPLETED AT</span>
                    <p style="margin: 4px 0 0 0;">{{ $tableQrcode->completed_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">DURATION</span>
                    <p style="margin: 4px 0 0 0;">{{ $tableQrcode->duration }} minutes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Information -->
    @if($tableQrcode->status === 'active')
    <div class="form-group">
        <label class="form-label">QR Code Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: 1fr 200px; gap: 16px; align-items: center;">
                <div>
                    <div style="margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 600;">QR CODE URL</span>
                        <p style="font-family: monospace; font-size: 14px; margin: 4px 0 0 0; word-break: break-all;">
                            <a href="{{ $tableQrcode->qr_code_url }}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                {{ $tableQrcode->qr_code_url }}
                            </a>
                        </p>
                    </div>
                    
                    <div class="form-actions" style="margin: 0;">
                        <a href="{{ route('admin.table-qrcodes.qr-preview', [$tableQrcode, 'png']) }}" class="btn-save" target="_blank" style="margin-right: 8px;">
                            <i class="fas fa-qrcode"></i> View QR Code
                        </a>
                        <a href="{{ route('admin.table-qrcodes.print', $tableQrcode) }}" class="btn-save" target="_blank" style="background: #7c3aed; margin-right: 8px;">
                            <i class="fas fa-print"></i> Print QR
                        </a>
                        <a href="{{ route('admin.table-qrcodes.download-qr', [$tableQrcode, 'png']) }}" class="btn-save" style="background: #3b82f6; margin-right: 8px;">
                            <i class="fas fa-download"></i> Download PNG
                        </a>
                        <a href="{{ route('admin.table-qrcodes.download-qr', [$tableQrcode, 'svg']) }}" class="btn-save" style="background: #8b5cf6;">
                            <i class="fas fa-download"></i> Download SVG
                        </a>
                    </div>
                </div>
                
                @if($tableQrcode->qr_code_png)
                <div style="text-align: center;">
                    <img src="{{ asset('storage/' . $tableQrcode->qr_code_png) }}" 
                         alt="QR Code for {{ $tableQrcode->session_code }}" 
                         style="max-width: 150px; max-height: 150px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Related Orders -->
    @if($tableQrcode->orders && $tableQrcode->orders->count() > 0)
    <div class="form-group">
        <label class="form-label">Related Orders</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; background: #f9fafb; overflow: hidden;">
            @foreach($tableQrcode->orders as $order)
            <div style="padding: 16px; @if(!$loop->last) border-bottom: 1px solid #e5e7eb; @endif">
                <div style="display: flex; justify-content: between; align-items: start;">
                    <div style="flex: 1;">
                        <p style="font-size: 16px; font-weight: 600; margin: 0 0 4px 0;">
                            <a href="{{ route('admin.order.show', $order->id) }}" style="color: #3b82f6; text-decoration: none;">
                                Order #{{ $order->id }}
                            </a>
                        </p>
                        <p style="font-size: 14px; color: #6b7280; margin: 0 0 4px 0;">
                            {{ $order->items->count() }} items • {{ $order->order_time->format('M d, Y h:i A') }}
                        </p>
                        <div style="display: flex; gap: 8px; margin-top: 4px;">
                            <span style="background: 
                                @if($order->order_status == 'pending') #fef3c7; color: #92400e
                                @elseif($order->order_status == 'preparing') #dbeafe; color: #1e40af
                                @elseif($order->order_status == 'ready') #e9d5ff; color: #6b21a8
                                @elseif($order->order_status == 'completed') #d1fae5; color: #065f46
                                @else #fee2e2; color: #991b1b @endif; 
                                padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                {{ ucfirst($order->order_status) }}
                            </span>
                            <span style="background: 
                                @if($order->payment_status == 'paid') #d1fae5; color: #065f46
                                @elseif($order->payment_status == 'partial') #fef3c7; color: #92400e
                                @else #fee2e2; color: #991b1b @endif; 
                                padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    <div style="text-align: right; margin-left: 16px;">
                        <p style="font-size: 16px; font-weight: 600; margin: 0;">RM {{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="form-group">
        <label class="form-label">Related Orders</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 40px; background: #f9fafb; text-align: center;">
            <i class="fas fa-shopping-cart" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
            <p style="color: #6b7280; font-weight: 600; margin: 0;">No orders placed using this QR code yet</p>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="form-actions">
        @if($tableQrcode->status === 'active')
            <button onclick="completeSession()" class="btn-save" style="background: #10b981;">
                <i class="fas fa-check-circle"></i>
                Complete Session
            </button>
            <button onclick="extendSession()" class="btn-save" style="background: #f59e0b;">
                <i class="fas fa-clock"></i>
                Extend Session
            </button>
            <button onclick="regenerateQR()" class="btn-save" style="background: #8b5cf6;">
                <i class="fas fa-sync"></i>
                Regenerate QR Code
            </button>
        @endif
        <a href="{{ route('admin.table-qrcodes.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
function completeSession() {
    showConfirm(
        'Complete Session?',
        'This will mark the QR code session as complete and make the table available.',
        'info',
        'Complete',
        'Cancel'
    ).then(confirmed => {
        if (!confirmed) return;

        fetch(`{{ route('admin.table-qrcodes.complete', $tableQrcode) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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

function extendSession() {
    // Create a custom input modal
    const overlay = document.createElement('div');
    overlay.className = 'confirm-modal-overlay';
    overlay.innerHTML = `
        <div class="confirm-modal">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon info">⏱</div>
                <div class="confirm-modal-text">
                    <h3 class="confirm-modal-title">Extend Session</h3>
                    <p class="confirm-modal-message">Enter number of hours to extend (1-12)</p>
                </div>
            </div>
            <div style="padding: 0 24px 16px;">
                <input type="number" id="extendHours" min="1" max="12" value="2"
                       style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px;">
            </div>
            <div class="confirm-modal-footer">
                <button class="confirm-modal-btn confirm-modal-btn-cancel">Cancel</button>
                <button class="confirm-modal-btn confirm-modal-btn-confirm info">Extend</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    const input = overlay.querySelector('#extendHours');
    const cancelBtn = overlay.querySelector('.confirm-modal-btn-cancel');
    const confirmBtn = overlay.querySelector('.confirm-modal-btn-confirm');

    const closeModal = () => {
        overlay.classList.add('hiding');
        setTimeout(() => overlay.remove(), 200);
    };

    cancelBtn.onclick = closeModal;
    overlay.onclick = (e) => { if (e.target === overlay) closeModal(); };

    confirmBtn.onclick = () => {
        const hours = parseInt(input.value);
        if (isNaN(hours) || hours < 1 || hours > 12) {
            Toast.warning('Invalid Input', 'Please enter a number between 1 and 12');
            return;
        }

        closeModal();

        fetch(`{{ route('admin.table-qrcodes.extend', $tableQrcode) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ hours })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toast.success('Success!', `Session extended by ${hours} hour(s)`);
                setTimeout(() => location.reload(), 1500);
            } else {
                Toast.error('Error', data.message || 'Failed to extend session');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.error('Error', 'Failed to extend session. Please try again.');
        });
    };

    input.focus();
    input.select();
}

function regenerateQR() {
    showConfirm(
        'Regenerate QR Code?',
        'The old QR code will no longer work. Are you sure you want to continue?',
        'warning',
        'Regenerate',
        'Cancel'
    ).then(confirmed => {
        if (!confirmed) return;

        fetch(`{{ route('admin.table-qrcodes.regenerate-qr', $tableQrcode) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
</script>
@endsection