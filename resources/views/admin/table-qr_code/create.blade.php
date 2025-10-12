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


/* Tables Modal Styles */
.tables-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease;
}

.tables-modal-overlay.active {
    display: flex;
}

.tables-modal {
    background: white;
    border-radius: var(--radius);
    width: 90%;
    max-width: 800px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    animation: slideUp 0.3s ease;
}

.tables-modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
    color: white;
}

.tables-modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.tables-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 1.5rem;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.tables-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.tables-modal-body {
    padding: 1.5rem;
    max-height: calc(80vh - 80px);
    overflow-y: auto;
}

.tables-modal-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.modal-table-card {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius);
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-table-card:hover {
    border-color: var(--brand);
    background: #f1f5f9;
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.modal-table-card.selected {
    border-color: var(--brand);
    background: #eef2ff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

    .tables-modal {
        width: 95%;
        max-height: 90vh;
    }

    .tables-modal-grid {
        grid-template-columns: 1fr;
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
                        @php
                            $availableTables = $tables->where('status', 'available');
                            $totalAvailable = $availableTables->count();
                        @endphp

                        <div class="table-selection" id="tableSelection">
                            @foreach($availableTables->take(3) as $table)
                                <div class="table-card"
                                     data-table-id="{{ $table->id }}"
                                     data-table-number="{{ $table->table_number }}"
                                     data-table-capacity="{{ $table->capacity }}"
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

                        @if($totalAvailable > 3)
                            <button type="button" class="btn-secondary" onclick="showAllTablesModal()" style="width: 100%; margin-top: 1rem; padding: 0.75rem; border-radius: var(--radius); border: 2px solid var(--brand); background: white; color: var(--brand); font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-list"></i> See All Tables ({{ $totalAvailable }} available)
                            </button>
                        @endif

                        <input type="hidden" id="table_id" name="table_id" value="{{ old('table_id') }}">

                        <!-- Hidden div to store all available tables data -->
                        <div id="allTablesData" style="display: none;">
                            @foreach($availableTables as $table)
                                <div data-table-id="{{ $table->id }}"
                                     data-table-number="{{ $table->table_number }}"
                                     data-table-capacity="{{ $table->capacity }}"></div>
                            @endforeach
                        </div>
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

<!-- All Tables Modal -->
<div class="tables-modal-overlay" id="tablesModal" onclick="closeTableModal(event)">
    <div class="tables-modal" onclick="event.stopPropagation()">
        <div class="tables-modal-header">
            <h3><i class="fas fa-table"></i> Select a Table</h3>
            <button class="tables-modal-close" onclick="closeTableModal()">×</button>
        </div>
        <div class="tables-modal-body">
            <div class="tables-modal-grid" id="modalTablesGrid">
                <!-- Tables will be dynamically inserted here -->
            </div>
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

// Show all tables modal
function showAllTablesModal() {
    const modal = document.getElementById('tablesModal');
    const modalGrid = document.getElementById('modalTablesGrid');
    const allTablesData = document.getElementById('allTablesData');

    // Clear previous content
    modalGrid.innerHTML = '';

    // Get all available tables from hidden div
    const tableElements = allTablesData.querySelectorAll('[data-table-id]');

    // Populate modal with all tables
    tableElements.forEach(tableEl => {
        const tableId = tableEl.getAttribute('data-table-id');
        const tableNumber = tableEl.getAttribute('data-table-number');
        const tableCapacity = tableEl.getAttribute('data-table-capacity');

        const card = document.createElement('div');
        card.className = 'modal-table-card';
        if (selectedTableId == tableId) {
            card.classList.add('selected');
        }
        card.setAttribute('data-table-id', tableId);
        card.onclick = () => selectTableFromModal(tableId);

        card.innerHTML = `
            <div class="table-card-header">
                <span class="table-number">Table ${tableNumber}</span>
                <span class="table-status available">Available</span>
            </div>
            <div class="table-capacity">
                <i class="fas fa-users"></i> ${tableCapacity} seats
            </div>
        `;

        modalGrid.appendChild(card);
    });

    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close modal
function closeTableModal(event) {
    const modal = document.getElementById('tablesModal');

    // Only close if clicking overlay or close button, not modal content
    if (event && event.target.closest('.tables-modal') && !event.target.classList.contains('tables-modal-close')) {
        return;
    }

    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Select table from modal and reorder display
function selectTableFromModal(tableId) {
    const allTablesData = document.getElementById('allTablesData');
    const tableSelection = document.getElementById('tableSelection');
    const selectedTableEl = allTablesData.querySelector(`[data-table-id="${tableId}"]`);

    if (!selectedTableEl) return;

    // Get selected table data
    const tableNumber = selectedTableEl.getAttribute('data-table-number');
    const tableCapacity = selectedTableEl.getAttribute('data-table-capacity');

    // Get all available tables
    const allTables = Array.from(allTablesData.querySelectorAll('[data-table-id]')).map(el => ({
        id: el.getAttribute('data-table-id'),
        number: el.getAttribute('data-table-number'),
        capacity: el.getAttribute('data-table-capacity')
    }));

    // Move selected table to first position
    const selectedIndex = allTables.findIndex(t => t.id == tableId);
    if (selectedIndex > -1) {
        const selectedTable = allTables.splice(selectedIndex, 1)[0];
        allTables.unshift(selectedTable);
    }

    // Display first 3 tables with selected one first
    tableSelection.innerHTML = '';
    allTables.slice(0, 3).forEach(table => {
        const card = document.createElement('div');
        card.className = 'table-card';
        if (table.id == tableId) {
            card.classList.add('selected');
        }
        card.setAttribute('data-table-id', table.id);
        card.setAttribute('data-table-number', table.number);
        card.setAttribute('data-table-capacity', table.capacity);
        card.onclick = () => selectTable(table.id);

        card.innerHTML = `
            <div class="table-card-header">
                <span class="table-number">Table ${table.number}</span>
                <span class="table-status available">Available</span>
            </div>
            <div class="table-capacity">
                <i class="fas fa-users"></i> ${table.capacity} seats
            </div>
        `;

        tableSelection.appendChild(card);
    });

    // Update selected table
    selectTable(tableId);

    // Close modal
    closeTableModal();
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTableModal();
    }
});

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