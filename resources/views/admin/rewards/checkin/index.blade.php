@extends('layouts.admin')
@section('title', 'Check-in Settings')
@section('page-title', 'Check-in Settings')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection
@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Daily Check-in Points Configuration</h2>
    </div>
    <div class="section-content">
        <p style="margin-bottom: 20px; color: var(--text-2);">Configure the points awarded for each day of the weekly check-in streak (Sunday to Saturday).</p>

        <form action="{{ route('admin.rewards.checkin.update') }}" method="POST">
            @csrf
            <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                    <div class="form-group">
                        <label for="day_{{ $index }}" class="form-label">{{ $day }}</label>
                        <input type="number" id="day_{{ $index }}" name="day_{{ $index }}" class="form-input"
                               value="{{ old('day_' . $index, $checkinSettings->{'day_' . $index} ?? 10) }}"
                               min="0" required>
                    </div>
                @endforeach
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="admin-btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
