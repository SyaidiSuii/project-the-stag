@extends('layouts.admin')

@section('title', isset($achievement) ? 'Edit Achievement' : 'Create Achievement')
@section('page-title', isset($achievement) ? 'Edit Achievement' : 'Create New Achievement')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

<div class="form-container">
    <form action="{{ isset($achievement) ? route('admin.rewards.achievements.update', $achievement->id) : route('admin.rewards.achievements.store') }}" method="POST">
        @csrf
        @if(isset($achievement))
            @method('PUT')
        @endif

        <div class="form-section">
            <h3 class="form-section-title">Achievement Information</h3>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name" class="form-label">Achievement Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input"
                           value="{{ old('name', $achievement->name ?? '') }}" placeholder="e.g., First Order" required>
                    @error('name')<div class="form-help" style="color: #ef4444;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="description" class="form-label">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-textarea" placeholder="Describe this achievement..." required>{{ old('description', $achievement->description ?? '') }}</textarea>
                    @error('description')<div class="form-help" style="color: #ef4444;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="points_reward" class="form-label">Points Reward <span class="required">*</span></label>
                    <input type="number" id="points_reward" name="points_reward" class="form-input"
                           value="{{ old('points_reward', $achievement->points_reward ?? '') }}" placeholder="e.g., 50" min="0" required>
                    @error('points_reward')<div class="form-help" style="color: #ef4444;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="icon" class="form-label">Icon Class</label>
                    <input type="text" id="icon" name="icon" class="form-input"
                           value="{{ old('icon', $achievement->icon ?? '') }}" placeholder="e.g., fas fa-trophy">
                    <small class="form-help">FontAwesome icon class</small>
                    @error('icon')<div class="form-help" style="color: #ef4444;">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="admin-btn btn-primary">
                <i class="fas fa-save"></i>
                {{ isset($achievement) ? 'Update Achievement' : 'Create Achievement' }}
            </button>
            <a href="{{ route('admin.rewards.index') }}" class="admin-btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
