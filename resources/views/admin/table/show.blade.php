@extends('layouts.admin')

@section('title', 'Table Details')
@section('page-title', 'Table Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">{{ $table->table_number ?? 'T-03' }}</h2>
        <div class="section-controls">
            <a href="{{ route('admin.table.edit', $table->id ?? 1) }}" class="btn-save">
                <i class="fas fa-edit"></i> Edit Table
            </a>
            <a href="{{ route('admin.table.index') }}" class="btn-cancel">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Basic Information -->
    <div class="form-group">
        <label class="form-label">Basic Information</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TABLE NUMBER</span>
                    <p style="font-size: 18px; font-weight: 700; margin: 4px 0 0 0;">{{ $table->table_number ?? 'T-03' }}</p>
                </div>
                
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">CAPACITY</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0;">{{ $table->capacity ?? '4' }} people</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">TYPE</span>
                    <p style="font-size: 16px; font-weight: 600; margin: 4px 0 0 0; text-transform: capitalize;">{{ $table->table_type ?? 'vip' }}</p>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">STATUS</span>
                    <div style="margin-top: 4px;">
                        <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600; text-transform: capitalize;">
                            {{ $table->status ?? 'available' }}
                        </span>
                    </div>
                </div>

                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">ACTIVE</span>
                    <div style="margin-top: 4px;">
                        <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: 600;">
                            {{ $table->is_active ?? true ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Details -->
    <div class="form-group">
        <label class="form-label">Technical Details</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">QR CODE</span>
                    <p style="font-family: monospace; font-size: 16px; font-weight: 700; margin: 4px 0 0 0;">{{ $table->qr_code ?? 'QR_T03' }}</p>
                </div>
                
                <div>
                    <span style="font-size: 12px; color: #6b7280; font-weight: 600;">NFC TAG</span>
                    <p style="color: #6b7280; margin: 4px 0 0 0;">{{ $table->nfc_tag ?? 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Amenities -->
    <div class="form-group">
        <label class="form-label">Amenities</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            @php
            // Define all possible amenities with their labels and icons
            $allAmenities = [
                'wifi_available' => ['label' => 'WiFi Available', 'icon' => 'wifi'],
                'power_outlet' => ['label' => 'Power Outlet', 'icon' => 'plug'],
                'window_view' => ['label' => 'Window View', 'icon' => 'eye'],
                'air_conditioning' => ['label' => 'Air Conditioning', 'icon' => 'snowflake'],
                'heating' => ['label' => 'Heating', 'icon' => 'fire'],
                'wheelchair_accessible' => ['label' => 'Wheelchair Accessible', 'icon' => 'wheelchair'],
                'high_chair_available' => ['label' => 'High Chair Available', 'icon' => 'baby'],
                'privacy_screen' => ['label' => 'Privacy Screen', 'icon' => 'shield-alt'],
                'soundproof' => ['label' => 'Soundproof', 'icon' => 'volume-mute'],
                'tv_screen' => ['label' => 'TV Screen', 'icon' => 'tv']
            ];

            // Get only the amenities that are saved in the database
            $savedAmenities = is_array($table->amenities) ? $table->amenities : [];
            @endphp
            
            @if(count($savedAmenities) > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                    @foreach($savedAmenities as $amenityKey)
                        @if(isset($allAmenities[$amenityKey]))
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-{{ $allAmenities[$amenityKey]['icon'] }}" style="color: #10b981; width: 16px;"></i>
                                <span style="font-size: 14px; color: #374151;">{{ $allAmenities[$amenityKey]['label'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-info-circle" style="font-size: 48px; color: #6b7280; margin-bottom: 12px;"></i>
                    <p style="color: #6b7280; font-weight: 600; margin: 0;">No amenities selected for this table.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- QR Code Status -->
    <div class="form-group">
        <label class="form-label">QR Code Status</label>
        <div style="border: 1px solid #d1d5db; border-radius: 12px; padding: 16px; background: #f9fafb;">
            <div style="text-align: center; padding: 20px;">
                <i class="fas fa-qrcode" style="font-size: 48px; color: #6b7280; margin-bottom: 12px;"></i>
                <p style="color: #6b7280; font-weight: 600; margin: 0;">No active session. QR code will be generated when a new session is created.</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions">
        <a href="{{ route('admin.table.edit', $table->id ?? 1) }}" class="btn-save">
            <i class="fas fa-edit"></i>
            Edit Table
        </a>
        <a href="{{ route('admin.table.index') }}" class="btn-cancel">
            <i class="fas fa-list"></i>
            Back to List
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Table management scripts can be added here
</script>
@endsection