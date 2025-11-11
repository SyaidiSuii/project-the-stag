@extends('layouts.admin')

@section('title', 'Customer Feedback')
@section('page-title', 'Customer Feedback')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
    .rating-stars {
        color: #ffc107;
    }
    .message-cell {
        max-width: 400px;
        white-space: normal;
        word-wrap: break-word;
    }
</style>
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Customer Feedback & Ratings</h2>
    </div>

    @if($feedbacks->count() > 0)
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th class="cell-center">Rating</th>
                    <th>Message</th>
                    <th>Date</th>
                    {{-- <th>Actions</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($feedbacks as $feedback)
                <tr>
                    <td data-label="Customer">
                        <div class="customer-info">
                            <div class="customer-avatar">
                                @if($feedback->user && $feedback->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $feedback->user->profile_photo_path) }}" alt="{{ $feedback->name }}">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div>
                                <div class="customer-name">{{ $feedback->name }}</div>
                                <div class="customer-email">{{ $feedback->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Rating" class="cell-center">
                        <div class="rating-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $feedback->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div style="font-weight: 700; font-size: 1.1rem;">{{ $feedback->rating }}/5</div>
                    </td>
                    <td data-label="Message" class="message-cell">
                       {{ $feedback->message }}
                    </td>
                    <td data-label="Date">
                        <div style="font-size: 13px;">{{ $feedback->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: var(--text-3);">{{ $feedback->created_at->diffForHumans() }}</div>
                    </td>
                    {{-- <td data-label="Actions">
                        <div class="table-actions">
                            {{-- Add actions like 'view' or 'delete' in the future if needed
                            <a href="#" class="action-btn view-btn" title="View Details (Not Implemented)">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-comment-slash"></i>
            </div>
            <div class="empty-state-title">No Feedback Yet</div>
            <p class="empty-state-text">
                No customers have submitted feedback through the website yet.
            </p>
        </div>
    @endif

    @if($feedbacks->hasPages())
    <div class="pagination">
        {{ $feedbacks->links('vendor.pagination.custom-admin') }}
    </div>
    @endif
</div>
@endsection
