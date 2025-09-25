@extends('layouts.admin')

@section('title', $table->id ? 'Edit Table' : 'Create Table')
@section('page-title', $table->id ? 'Edit Table' : 'Create Table')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            @if($table->id)
                Edit Table - #{{ $table->id }}
            @else
                Create New Table
            @endif
        </h2>
        <a href="{{ route('admin.table.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Tables
        </a>
    </div>

    @if($table->id)
        <form method="POST" action="{{ route('admin.table.update', $table->id) }}" class="table-form">
            @method('PUT')
    @else
        <form method="POST" action="{{ route('admin.table.store') }}" class="table-form">
    @endif
        @csrf

        <!-- Basic Table Information -->
        <div class="form-section">
            <h3 class="section-subtitle">Basic Table Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="table_number" class="form-label">Table Number *</label>
                    <input 
                        type="text" 
                        id="table_number" 
                        name="table_number" 
                        class="form-control @error('table_number') is-invalid @enderror"
                        value="{{ old('table_number', $table->table_number) }}"
                        placeholder="e.g. T001, A1, B2"
                        required>
                    @if($errors->get('table_number'))
                        <div class="form-error">{{ implode(', ', $errors->get('table_number')) }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="capacity" class="form-label">Capacity (People) *</label>
                    <input 
                        type="number" 
                        id="capacity" 
                        name="capacity" 
                        class="form-control @error('capacity') is-invalid @enderror"
                        value="{{ old('capacity', $table->capacity) }}"
                        min="1"
                        max="20"
                        placeholder="Number of people"
                        required>
                    @if($errors->get('capacity'))
                        <div class="form-error">{{ implode(', ', $errors->get('capacity')) }}</div>
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status" class="form-label">Status *</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="form-control @error('status') is-invalid @enderror"
                        required>
                        <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="reserved" {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="maintenance" {{ old('status', $table->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @if($errors->get('status'))
                        <div class="form-error">{{ implode(', ', $errors->get('status')) }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="table_type" class="form-label">Table Type</label>
                    <select 
                        id="table_type" 
                        name="table_type" 
                        class="form-control @error('table_type') is-invalid @enderror">
                        <option value="indoor" {{ old('table_type', $table->table_type) == 'indoor' ? 'selected' : '' }}>Indoor</option>
                        <option value="vip" {{ old('table_type', $table->table_type) == 'vip' ? 'selected' : '' }}>VIP</option>
                        <option value="outdoor" {{ old('table_type', $table->table_type) == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                    </select>
                    @if($errors->get('table_type'))
                        <div class="form-error">{{ implode(', ', $errors->get('table_type')) }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Technology Integration -->
        <div class="form-section">
            <h3 class="section-subtitle">Technology Integration</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="qr_code" class="form-label">QR Code</label>
                    <input 
                        type="text" 
                        id="qr_code" 
                        name="qr_code" 
                        class="form-control @error('qr_code') is-invalid @enderror"
                        value="{{ old('qr_code', $table->qr_code) }}"
                        placeholder="QR code for digital menu">
                    @if($errors->get('qr_code'))
                        <div class="form-error">{{ implode(', ', $errors->get('qr_code')) }}</div>
                    @endif
                    <div class="form-hint">Leave empty to auto-generate</div>
                </div>

                <div class="form-group">
                    <label for="nfc_tag_id" class="form-label">NFC Tag ID (Optional)</label>
                    <input 
                        type="text" 
                        id="nfc_tag_id" 
                        name="nfc_tag_id" 
                        class="form-control @error('nfc_tag_id') is-invalid @enderror"
                        value="{{ old('nfc_tag_id', $table->nfc_tag_id) }}"
                        placeholder="NFC tag identifier">
                    @if($errors->get('nfc_tag_id'))
                        <div class="form-error">{{ implode(', ', $errors->get('nfc_tag_id')) }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Location & Description -->
        <div class="form-section">
            <h3 class="section-subtitle">Location & Description</h3>
            <div class="form-group">
                <label for="location_description" class="form-label">Location Description</label>
                <textarea 
                    id="location_description" 
                    name="location_description" 
                    class="form-control @error('location_description') is-invalid @enderror"
                    rows="3"
                    placeholder="Describe the table location (e.g., Near window, Corner booth, Main dining area)">{{ old('location_description', $table->location_description) }}</textarea>
                @if($errors->get('location_description'))
                    <div class="form-error">{{ implode(', ', $errors->get('location_description')) }}</div>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="latitude" class="form-label">Latitude (Optional)</label>
                    <input 
                        type="number" 
                        id="latitude" 
                        name="latitude" 
                        class="form-control @error('latitude') is-invalid @enderror"
                        value="{{ old('latitude', $table->latitude) }}"
                        step="0.000001"
                        placeholder="GPS latitude coordinate">
                    @if($errors->get('latitude'))
                        <div class="form-error">{{ implode(', ', $errors->get('latitude')) }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="longitude" class="form-label">Longitude (Optional)</label>
                    <input 
                        type="number" 
                        id="longitude" 
                        name="longitude" 
                        class="form-control @error('longitude') is-invalid @enderror"
                        value="{{ old('longitude', $table->longitude) }}"
                        step="0.000001"
                        placeholder="GPS longitude coordinate">
                    @if($errors->get('longitude'))
                        <div class="form-error">{{ implode(', ', $errors->get('longitude')) }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Amenities -->
        <div class="form-section">
            <h3 class="section-subtitle">Amenities</h3>
            <div class="amenities-grid">
                <div class="amenity-item">
                    <input type="checkbox" id="wifi_available" name="amenities[]" value="wifi_available" 
                           {{ in_array('wifi_available', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="wifi_available">WiFi Available</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="power_outlet" name="amenities[]" value="power_outlet"
                           {{ in_array('power_outlet', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="power_outlet">Power Outlet</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="window_view" name="amenities[]" value="window_view"
                           {{ in_array('window_view', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="window_view">Window View</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="air_conditioning" name="amenities[]" value="air_conditioning"
                           {{ in_array('air_conditioning', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="air_conditioning">Air Conditioning</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="heating" name="amenities[]" value="heating"
                           {{ in_array('heating', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="heating">Heating</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="wheelchair_accessible" name="amenities[]" value="wheelchair_accessible"
                           {{ in_array('wheelchair_accessible', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="wheelchair_accessible">Wheelchair Accessible</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="high_chair_available" name="amenities[]" value="high_chair_available"
                           {{ in_array('high_chair_available', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="high_chair_available">High Chair Available</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="privacy_screen" name="amenities[]" value="privacy_screen"
                           {{ in_array('privacy_screen', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="privacy_screen">Privacy Screen</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="soundproof" name="amenities[]" value="soundproof"
                           {{ in_array('soundproof', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="soundproof">Soundproof</label>
                </div>
                <div class="amenity-item">
                    <input type="checkbox" id="tv_screen" name="amenities[]" value="tv_screen"
                           {{ in_array('tv_screen', old('amenities', $table->amenities ?? [])) ? 'checked' : '' }}>
                    <label for="tv_screen">TV Screen</label>
                </div>
            </div>
            @if($errors->get('amenities'))
                <div class="form-error">{{ implode(', ', $errors->get('amenities')) }}</div>
            @endif
        </div>

        <!-- Active Status -->
        <div class="form-section">
            <h3 class="section-subtitle">Table Status</h3>
            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $table->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="checkbox-label">Active Table</label>
                    <div class="form-hint">Uncheck to disable this table temporarily</div>
                </div>
                @if($errors->get('is_active'))
                    <div class="form-error">{{ implode(', ', $errors->get('is_active')) }}</div>
                @endif
            </div>
        </div>

        <!-- Current Table Information (for edit) -->
        @if($table->id)
            <div class="form-section">
                <h3 class="section-subtitle">Table Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Table ID:</span>
                        <span class="info-value">#{{ $table->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created:</span>
                        <span class="info-value">{{ $table->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($table->updated_at != $table->created_at)
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span>
                        <span class="info-value">{{ $table->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($table->qr_code)
                    <div class="info-item">
                        <span class="info-label">QR Code:</span>
                        <span class="info-value">{{ $table->qr_code }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                Save
            </button>
            <a href="{{ route('admin.table.index') }}" class="btn-cancel">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
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

// Add JavaScript for amenities handling and GPS coordinates
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate QR code if empty on new table
    const qrCodeInput = document.getElementById('qr_code');
    const tableNumberInput = document.getElementById('table_number');
    
    if (!qrCodeInput.value && tableNumberInput) {
        tableNumberInput.addEventListener('blur', function() {
            if (!qrCodeInput.value && this.value) {
                // Generate a simple QR code identifier
                const qrCode = 'QR_' + this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                qrCodeInput.value = qrCode;
            }
        });
    }
    
    // GPS coordinates validation
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    function validateCoordinate(input, min, max) {
        input.addEventListener('blur', function() {
            if (this.value && (parseFloat(this.value) < min || parseFloat(this.value) > max)) {
                this.style.borderColor = '#ef4444';
                // You could add error message here
            } else {
                this.style.borderColor = '';
            }
        });
    }
    
    if (latInput) validateCoordinate(latInput, -90, 90);
    if (lngInput) validateCoordinate(lngInput, -180, 180);
    
    // Handle form submission loading state
    const tableForm = document.querySelector('.table-form');
    if (tableForm) {
        tableForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-save');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
            // Let the form submit normally - don't prevent default
        });
    }
    
    // Check for success message from session
    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
    
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
});
</script>
@endsection