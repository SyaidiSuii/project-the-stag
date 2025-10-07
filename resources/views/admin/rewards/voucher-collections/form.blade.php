@extends('layouts.admin')
@section('title', isset($collection) ? 'Edit Collection' : 'Create Collection')
@section('page-title', isset($collection) ? 'Edit Collection' : 'Create Collection')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="form-container">
    <form action="{{ isset($collection) ? route('admin.rewards.voucher-collections.update', $collection->id) : route('admin.rewards.voucher-collections.store') }}" method="POST">
        @csrf @if(isset($collection)) @method('PUT') @endif
        <div class="form-section">
            <h3 class="form-section-title">Collection Information</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Collection Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $collection->name ?? '') }}" placeholder="e.g., Summer Collection" required>
                    @error('name')<div class="form-help" style="color:#ef4444">{{ $message }}</div>@enderror
                </div>
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea" placeholder="Describe this collection...">{{ old('description', $collection->description ?? '') }}</textarea>
                    @error('description')<div class="form-help" style="color:#ef4444">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary"><i class="fas fa-save"></i> {{ isset($collection) ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
@endsection
