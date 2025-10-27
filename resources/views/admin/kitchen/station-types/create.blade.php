@extends('layouts.admin')

@section('title', 'Add New Station Type')
@section('page-title', 'Add New Station Type')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kitchen-dashboard.css') }}">
<style>
.form-container {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #334155;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-group input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group small {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.text-danger {
    color: #ef4444;
}

.icon-preview {
    margin-top: 8px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    text-align: center;
}

.icon-preview span {
    font-size: 48px;
}
</style>
@endsection

@section('content')

<div class="kitchen-page">
    <div class="kitchen-section">
        <div class="admin-section" style="margin-bottom: 0;">
            <div class="form-container">
                <form action="{{ route('admin.kitchen.station-types.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="station_type">Station Type Name <span class="text-danger">*</span></label>
                        <input type="text" id="station_type" name="station_type" value="{{ old('station_type') }}" required placeholder="e.g., grill, bakery, sushi_bar">
                        <small>Use lowercase and underscores (e.g., hot_kitchen, salad_bar)</small>
                        @error('station_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon (HTML Entity or Emoji)</label>
                        <input type="text" id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g., &#x1F525; or 🔥">
                        <small>Use HTML entity format (&#x1F525;) or direct emoji. <a href="https://emojipedia.org" target="_blank">Find emojis here</a></small>
                        @error('icon')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <div class="icon-preview" id="iconPreview" style="display: none;">
                            <span id="iconDisplay"></span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.kitchen.station-types.index') }}" class="admin-btn btn-secondary" style="padding: 10px 24px;">Cancel</a>
                        <button type="submit" class="admin-btn btn-primary" style="padding: 10px 24px;">
                            Create Station Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('icon').addEventListener('input', function() {
    const iconValue = this.value.trim();
    const iconPreview = document.getElementById('iconPreview');
    const iconDisplay = document.getElementById('iconDisplay');

    if (iconValue) {
        iconDisplay.innerHTML = iconValue;
        iconPreview.style.display = 'block';
    } else {
        iconPreview.style.display = 'none';
    }
});
</script>
@endsection
