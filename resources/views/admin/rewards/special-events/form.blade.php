@extends('layouts.admin')
@section('title', isset($event) ? 'Edit Event' : 'Create Event')
@section('page-title', isset($event) ? 'Edit Event' : 'Create Event')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="form-container">
    <form action="{{ isset($event) ? route('admin.rewards.special-events.update', $event->id) : route('admin.rewards.special-events.store') }}" method="POST">
        @csrf @if(isset($event)) @method('PUT') @endif
        <div class="form-section">
            <h3 class="form-section-title">Event Information</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Event Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $event->name ?? '') }}" required>
                </div>
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea">{{ old('description', $event->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="points_multiplier" class="form-label">Points Multiplier <span class="required">*</span></label>
                    <input type="number" id="points_multiplier" name="points_multiplier" class="form-input" value="{{ old('points_multiplier', $event->points_multiplier ?? 1) }}" step="0.1" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $event->is_active ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-size:14px;color:#64748b">Active</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" value="{{ old('start_date', $event->start_date ?? '') }}">
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" value="{{ old('end_date', $event->end_date ?? '') }}">
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary"><i class="fas fa-save"></i> {{ isset($event) ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
@endsection
