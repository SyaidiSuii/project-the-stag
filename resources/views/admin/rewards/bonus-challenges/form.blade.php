@extends('layouts.admin')
@section('title', isset($challenge) ? 'Edit Challenge' : 'Create Challenge')
@section('page-title', isset($challenge) ? 'Edit Challenge' : 'Create Challenge')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="form-container">
    <form action="{{ isset($challenge) ? route('admin.rewards.bonus-challenges.update', $challenge->id) : route('admin.rewards.bonus-challenges.store') }}" method="POST">
        @csrf @if(isset($challenge)) @method('PUT') @endif
        <div class="form-section">
            <h3 class="form-section-title">Challenge Information</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Challenge Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $challenge->name ?? '') }}" required>
                </div>
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea">{{ old('description', $challenge->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="bonus_points" class="form-label">Bonus Points <span class="required">*</span></label>
                    <input type="number" id="bonus_points" name="bonus_points" class="form-input" value="{{ old('bonus_points', $challenge->bonus_points ?? '') }}" min="0" required>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary"><i class="fas fa-save"></i> {{ isset($challenge) ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
@endsection
