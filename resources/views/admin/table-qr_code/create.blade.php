@extends('layouts.admin')

@section('title', $tableQrcode->id ? 'Edit QR Code' : 'Generate QR Code')
@section('page-title', $tableQrcode->id ? 'Edit QR Code' : 'Generate QR Code')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
<style>
.qr-generator-container {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.qr-form-section {
    background: var(--card);
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    height: fit-content;
}

.qr-preview-section {
    background: var(--card);
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    text-align: center;
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.qr-preview-placeholder {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 2px dashed #cbd5e1;
    border-radius: var(--radius);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    transition: all 0.3s ease;
}

.qr-preview-placeholder i {
    font-size: 4rem;
    color: #94a3b8;
    margin-bottom: 1rem;
}

.qr-preview-placeholder p {
    color: #64748b;
    font-size: 0.9rem;
}

.qr-code-image {
    max-width: 300px;
    height: auto;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
}

.qr-code-display {
    text-align: center;
}

.qr-info {
    text-align: left !important;
}

.qr-info h4 {
    color: var(--text);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.qr-info p {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    color: var(--text-2);
}

.table-card {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.table-card:hover {
    border-color: var(--brand);
    background: #f1f5f9;
}

.table-card.selected {
    border-color: var(--brand);
    background: #eef2ff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.table-card-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.table-number {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text);
}

.table-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.table-status.available {
    background: #dcfce7;
    color: #166534;
}

.table-status.occupied {
    background: #fee2e2;
    color: #991b1b;
}

.table-capacity {
    color: var(--text-2);
    font-size: 0.9rem;
}

.generate-btn {
    background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: var(--radius);
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--shadow);
    width: 100%;
    margin-top: 1rem;
}

.generate-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.generate-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.qr-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.qr-action-btn {
    flex: 1;
    min-width: 120px;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: var(--radius);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    font-size: 0.9rem;
}

.btn-view {
    background: #3b82f6;
    color: white;
}

.btn-download {
    background: #10b981;
    color: white;
}

.btn-regenerate {
    background: #8b5cf6;
    color: white;
}

.btn-complete {
    background: #059669;
    color: white;
}

.btn-print {
    background: #7c3aed;
    color: white;
    border: none;
    cursor: pointer;
}


@media (max-width: 768px) {
    .qr-generator-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .qr-preview-section {
        position: static;
        order: -1;
    }
    
    .qr-preview-placeholder {
        width: 250px;
        height: 250px;
    }
}
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            @if($tableQrcode->id)
                QR Code Management
            @else
                Generate New QR Code
            @endif
        </h2>
        <div class="section-controls">
            <a href="{{ route('admin.table-qrcodes.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to QR Codes
            </a>
        </div>
    </div>

    <div class="qr-generator-container">
        <!-- Form Section -->
        <div class="qr-form-section">
            <h3 style="margin-bottom: 1.5rem; color: var(--text);">
                <i class="fas fa-cog" style="margin-right: 0.5rem; color: var(--brand);"></i>
                QR Code Configuration
            </h3>

            <form id="qrGenerateForm" action="{{ route('admin.table-qrcodes.store') }}" method="POST">
                @csrf

                <!-- Table Selection -->
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label" style="margin-bottom: 1rem;">
                        <i class="fas fa-table" style="margin-right: 0.5rem;"></i>
                        Select Available Table *
                    </label>
                    
                    @if($tables->where('status', 'available')->count() > 0)
                        <div class="table-selection">
                            @foreach($tables->where('status', 'available') as $table)
                                <div class="table-card" 
                                     data-table-id="{{ $table->id }}"
                                     onclick="selectTable({{ $table->id }})">
                                    <div class="table-card-header">
                                        <span class="table-number">Table {{ $table->table_number }}</span>
                                        <span class="table-status available">Available</span>
                                    </div>
                                    <div class="table-capacity">
                                        <i class="fas fa-users"></i> {{ $table->capacity }} seats
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="table_id" name="table_id" value="{{ old('table_id') }}">
                    @else
                        <div style="padding: 2rem; text-align: center; background: #fef3cd; border-radius: var(--radius); color: #92400e;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                            <p>No available tables found. All tables are currently occupied.</p>
                        </div>
                    @endif
                    
                    @error('table_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Time Selection -->
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label">
                        <i class="fas fa-clock" style="margin-right: 0.5rem;"></i>
                        Session Start Time *
                    </label>
                    <input 
                        type="datetime-local"
                        id="started_at"
                        name="started_at" 
                        class="form-control @error('started_at') is-invalid @enderror"
                        value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}"
                        min="{{ now()->subMinutes(2)->format('Y-m-d\TH:i') }}"
                        required>
                    @error('started_at')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Optional Guest Information -->
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label">
                        <i class="fas fa-user" style="margin-right: 0.5rem;"></i>
                        Guest Name (Optional)
                    </label>
                    <input 
                        type="text"
                        name="guest_name" 
                        class="form-control @error('guest_name') is-invalid @enderror"
                        value="{{ old('guest_name') }}"
                        placeholder="Enter guest name">
                    @error('guest_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone" style="margin-right: 0.5rem;"></i>
                            Phone Number (Optional)
                        </label>
                        <input 
                            type="tel"
                            name="guest_phone" 
                            class="form-control @error('guest_phone') is-invalid @enderror"
                            value="{{ old('guest_phone') }}"
                            placeholder="Enter phone number">
                        @error('guest_phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-users" style="margin-right: 0.5rem;"></i>
                            Guest Count (Optional)
                        </label>
                        <input 
                            type="number"
                            name="guest_count" 
                            class="form-control @error('guest_count') is-invalid @enderror"
                            value="{{ old('guest_count') }}"
                            min="1"
                            max="50"
                            placeholder="Number of guests">
                        @error('guest_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Generate Button -->
                @if($tables->where('status', 'available')->count() > 0)
                    <button type="submit" class="generate-btn" id="generateBtn" disabled>
                        <i class="fas fa-qrcode" style="margin-right: 0.5rem;"></i>
                        Generate QR Code
                    </button>
                @endif
            </form>
        </div>

        <!-- QR Preview Section -->
        <div class="qr-preview-section">
            <h3 style="margin-bottom: 1.5rem; color: var(--text);">
                <i class="fas fa-eye" style="margin-right: 0.5rem; color: var(--brand);"></i>
                QR Code Preview
            </h3>

            @if(session('qr_session'))
                @php $qrSession = session('qr_session'); @endphp
                <!-- QR Code Display -->
                <div class="qr-code-display">
                    <img src="{{ route('admin.table-qrcodes.qr-preview', ['tableQrcode' => $qrSession->id, 'format' => 'png']) }}" 
                         alt="QR Code for Table {{ $qrSession->table->table_number }}" 
                         class="qr-code-image">
                    
                    <div class="qr-info" style="text-align: left; background: #f8fafc; padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem;">
                        <h4 style="margin-bottom: 0.5rem; color: var(--text);">Session Details</h4>
                        <p><strong>Table:</strong> {{ $qrSession->table->table_number }}</p>
                        <p><strong>Session Code:</strong> {{ $qrSession->session_code }}</p>
                        <p><strong>Started:</strong> {{ $qrSession->started_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Expires:</strong> {{ $qrSession->expires_at->format('d/m/Y H:i') }}</p>
                        @if($qrSession->guest_name)
                            <p><strong>Guest:</strong> {{ $qrSession->guest_name }}</p>
                        @endif
                    </div>
                    
                    <div class="qr-actions">
                        <a href="{{ route('admin.table-qrcodes.show', $qrSession) }}" class="qr-action-btn btn-view">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.table-qrcodes.print', $qrSession) }}" target="_blank" class="qr-action-btn btn-print">
                            <i class="fas fa-print"></i> Print QR
                        </a>
                        <a href="{{ route('admin.table-qrcodes.download-qr', ['tableQrcode' => $qrSession, 'format' => 'png']) }}" class="qr-action-btn btn-download">
                            <i class="fas fa-download"></i> Download PNG
                        </a>
                        <a href="{{ route('admin.table-qrcodes.download-qr', ['tableQrcode' => $qrSession, 'format' => 'svg']) }}" class="qr-action-btn btn-download">
                            <i class="fas fa-download"></i> Download SVG
                        </a>
                    </div>
                    
                    <div style="margin-top: 1rem;">
                        <a href="{{ route('admin.table-qrcodes.create') }}" class="qr-action-btn" style="background: #6b7280; color: white; width: 100%;">
                            <i class="fas fa-plus"></i> Generate New QR Code
                        </a>
                    </div>
                </div>
            @else
                <!-- Placeholder -->
                <div class="qr-preview-placeholder">
                    <i class="fas fa-qrcode"></i>
                    <p>QR Code will appear here<br>after generation</p>
                </div>
                <p style="color: var(--text-2); font-size: 0.9rem;">
                    Select a table and time to generate QR code
                </p>
            @endif
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
let selectedTableId = {{ old('table_id') ?? 'null' }};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Pre-select existing table if editing
    if (selectedTableId) {
        selectTable(selectedTableId);
    }
    
    // Check if generate button should be enabled
    checkFormValid();
    
    // Listen for time changes
    document.getElementById('started_at').addEventListener('change', checkFormValid);
});

function selectTable(tableId) {
    // Remove previous selection
    document.querySelectorAll('.table-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Select new table
    const selectedCard = document.querySelector(`[data-table-id="${tableId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        selectedTableId = tableId;
        document.getElementById('table_id').value = tableId;
        
        // Check if form is valid to enable button
        checkFormValid();
    }
}

function checkFormValid() {
    const tableSelected = selectedTableId !== null && selectedTableId !== '';
    const timeSelected = document.getElementById('started_at').value !== '';
    const generateBtn = document.getElementById('generateBtn');
    
    if (generateBtn) {
        generateBtn.disabled = !(tableSelected && timeSelected);
    }
}

// No existing QR management functions needed for create page

// Utility functions for UI feedback
function showLoading() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay active';
    overlay.innerHTML = '<div class="loading-spinner"></div>';
    document.body.appendChild(overlay);
    return overlay;
}

function hideLoading(overlay) {
    if (overlay && overlay.parentNode) {
        overlay.parentNode.removeChild(overlay);
    }
}

// Notification function
function showNotification(message, type) {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}


// Form submission handling
document.getElementById('qrGenerateForm').addEventListener('submit', function(e) {
    // Client-side validation only
    if (!selectedTableId) {
        e.preventDefault();
        showNotification('Please select a table', 'error');
        return;
    }
    
    const startTime = document.getElementById('started_at').value;
    if (!startTime) {
        e.preventDefault();
        showNotification('Please select start time', 'error');
        return;
    }
    
    // Show loading state for valid submissions
    const submitBtn = document.getElementById('generateBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating QR Code...';
    submitBtn.disabled = true;
    
    // Let the form submit normally - don't prevent default for valid forms
});

// Check for success/error messages from session
@if(session('message'))
    showNotification('{{ session('message') }}', 'success');
@endif

@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif
</script>
@endsection