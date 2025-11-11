@extends('layouts.customer')

@section('title', 'Order Details - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
<style>
    /* Full screen container */
    .order-details-fullscreen {
        min-height: calc(100vh - 80px);
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        padding: 32px 0;
    }

    .order-details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* Back button - simple text */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        color: #6b7280;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 24px;
        text-decoration: none;
    }

    .back-button:hover {
        border-color: #6366f1;
        color: #6366f1;
        text-decoration: none;
    }

    /* Main card */
    .order-details-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    /* Order header */
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        padding-bottom: 32px;
        border-bottom: 2px solid #f3f4f6;
        margin-bottom: 32px;
    }

    .order-id-large {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .order-date-time {
        font-size: 15px;
        color: #6b7280;
        margin-bottom: 16px;
    }

    .order-meta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 16px;
    }

    .meta-item {
        background: #f9fafb;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .meta-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .meta-value {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
    }

    /* Progress Stepper Styles */
    .progress-section {
        margin: 32px 0;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 24px;
    }

    .progress-stepper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 32px;
        background: white;
        border-radius: 16px;
        border: 2px solid #e5e7eb;
        position: relative;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .step-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #f3f4f6;
        border: 4px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
        transition: all 0.3s;
    }

    .step.active .step-circle {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-color: #6366f1;
        color: white;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .step.completed .step-circle {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-color: #10b981;
        color: white;
    }

    @keyframes pulse {

        0%,
        100% {
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        50% {
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.6);
        }
    }

    .step-label {
        font-size: 14px;
        font-weight: 600;
        color: #9ca3af;
        text-align: center;
    }

    .step.active .step-label {
        color: #6366f1;
    }

    .step.completed .step-label {
        color: #10b981;
    }

    .step-time {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
    }

    /* Progress line */
    .progress-stepper::before {
        content: '';
        position: absolute;
        top: calc(32px + 28px);
        left: 10%;
        right: 10%;
        height: 4px;
        background: #e5e7eb;
        z-index: 1;
    }

    .progress-line-fill {
        position: absolute;
        top: calc(32px + 28px);
        left: 10%;
        height: 4px;
        background: linear-gradient(90deg, #10b981 0%, #6366f1 100%);
        z-index: 1;
        transition: width 0.5s ease;
    }

    /* Payment stepper - 2 steps */
    .payment-stepper {
        padding: 24px 60px;
    }

    .payment-stepper::before {
        left: calc(25% + 28px);
        right: calc(25% + 28px);
    }

    .payment-stepper .progress-line-fill {
        left: calc(25% + 28px);
    }

    /* Order items */
    .order-items-section {
        margin: 32px 0;
    }

    /* Promotion Group Styles */
    .order-promotion-group {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #38bdf8;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.15);
    }

    .promotion-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(56, 189, 248, 0.3);
    }

    .promotion-group-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .promotion-group-title i {
        color: #8b5cf6;
        font-size: 20px;
    }

    .promotion-type-badge {
        background: #8b5cf6;
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .promotion-savings {
        font-size: 14px;
        font-weight: 700;
        color: #10b981;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .promotion-savings i {
        font-size: 16px;
    }

    .promotion-group-items {
        margin-bottom: 16px;
    }

    .promotion-item {
        background: rgba(255, 255, 255, 0.8) !important;
        border: 1px solid rgba(56, 189, 248, 0.3) !important;
        margin-bottom: 12px;
    }

    .promotion-item:last-child {
        margin-bottom: 0;
    }

    .free-badge {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        margin-left: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }

    .original-price {
        display: inline-block;
        margin-left: 8px;
        color: #9ca3af;
        text-decoration: line-through;
        font-size: 13px;
    }

    .promotion-group-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 2px solid rgba(56, 189, 248, 0.3);
        font-size: 18px;
        font-weight: 700;
        color: #8b5cf6;
    }

    /* Item Discount Styles */
    .item-discount-group {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 2px solid #fbbf24;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(251, 191, 36, 0.15);
    }

    .item-discount-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(251, 191, 36, 0.3);
    }

    .item-discount-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .item-discount-title i {
        color: #f59e0b;
        font-size: 20px;
    }

    .item-discount-badge {
        background: #f59e0b;
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .item-discount-savings {
        font-size: 14px;
        font-weight: 700;
        color: #10b981;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .item-discount-savings i {
        font-size: 16px;
    }

    .item-discount-items {
        margin-bottom: 16px;
    }

    .item-discount-item {
        background: rgba(255, 255, 255, 0.8) !important;
        border: 1px solid rgba(251, 191, 36, 0.3) !important;
        margin-bottom: 12px;
    }

    .item-discount-item:last-child {
        margin-bottom: 0;
    }

    .order-item {
        display: flex;
        gap: 20px;
        padding: 20px;
        background: #f9fafb;
        border-radius: 16px;
        margin-bottom: 16px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }

    .order-item:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    .item-image {
        width: 100px;
        height: 100px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .item-quantity-price {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 8px;
    }

    .item-quantity {
        font-size: 14px;
        color: #6b7280;
        background: white;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .item-unit-price {
        font-size: 14px;
        color: #6b7280;
    }

    .item-notes {
        font-size: 13px;
        color: #6b7280;
        background: white;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        margin-top: 8px;
    }

    .item-price {
        font-size: 20px;
        font-weight: 700;
        color: #6366f1;
        align-self: center;
    }

    /* Order summary */
    .order-summary-card {
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border-radius: 20px;
        padding: 32px;
        border: 2px solid #e5e7eb;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 14px 0;
        font-size: 16px;
        color: #6b7280;
    }

    .summary-row.total {
        border-top: 2px solid #e5e7eb;
        margin-top: 12px;
        padding-top: 20px;
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
    }

    /* ETA Section */
    .eta-card {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 2px solid #6366f1;
        border-radius: 16px;
        padding: 24px;
        margin: 24px 0;
        text-align: center;
    }

    .eta-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .eta-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .eta-time {
        font-size: 32px;
        font-weight: 900;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .eta-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin-top: 8px;
    }

    /* Review Section Styles */
    .review-section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 2px solid #e5e7eb;
    }

    .review-items-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 24px;
    }

    .review-item-card {
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s;
    }

    .review-item-card:hover {
        border-color: #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
    }

    .review-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .review-item-name {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .review-item-quantity {
        background: white;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 14px;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }

    .rating-section {
        margin-bottom: 16px;
    }

    .rating-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .star-rating {
        display: flex;
        gap: 8px;
        font-size: 32px;
    }

    .star {
        cursor: pointer;
        color: #d1d5db;
        transition: all 0.2s;
    }

    .star:hover {
        transform: scale(1.15);
    }

    .star.active {
        color: #fbbf24;
    }

    .review-textarea {
        width: 100%;
        min-height: 100px;
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        resize: vertical;
        font-family: inherit;
        transition: border-color 0.2s;
    }

    .review-textarea:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .anonymous-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
    }

    .anonymous-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .anonymous-checkbox label {
        font-size: 14px;
        color: #6b7280;
        cursor: pointer;
        margin: 0;
    }

    .submit-review-section {
        margin-top: 32px;
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-submit-review {
        padding: 14px 32px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit-review:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
    }

    .btn-submit-review:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .success-message,
    .error-message {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
    }

    .success-message {
        background: #d1fae5;
        border: 2px solid #10b981;
        color: #065f46;
    }

    .error-message {
        background: #fee2e2;
        border: 2px solid #ef4444;
        color: #991b1b;
    }

    .already-reviewed-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
    }

    /* Responsive Design - Mobile (768px) ~20% reduction */
    @media (max-width: 768px) {

        /* Container & Layout */
        .order-details-fullscreen {
            min-height: calc(100vh - 70px);
            padding: 20px 0;
            /* ~38% reduction */
        }

        .order-details-container {
            padding: 0 16px;
            /* ~33% reduction */
        }

        .order-details-card {
            padding: 24px;
            /* ~40% reduction */
            border-radius: 16px;
        }

        /* Back Button */
        .back-button {
            padding: 10px 16px;
            /* ~20% reduction */
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* Order Header */
        .order-header {
            flex-direction: column;
            padding-bottom: 20px;
            /* ~38% reduction */
            margin-bottom: 20px;
        }

        .order-id-large {
            font-size: 24px;
            /* ~25% reduction */
            margin-bottom: 6px;
        }

        .order-date-time {
            font-size: 13px;
            /* ~13% reduction */
            margin-bottom: 12px;
        }

        .order-meta {
            grid-template-columns: 1fr;
            gap: 10px;
            /* ~38% reduction */
            margin-top: 12px;
        }

        .meta-item {
            padding: 12px;
            /* ~25% reduction */
            border-radius: 10px;
        }

        .meta-label {
            font-size: 11px;
            /* ~15% reduction */
        }

        .meta-value {
            font-size: 14px;
            /* ~13% reduction */
        }

        /* Progress Section */
        .progress-section {
            margin: 20px 0;
            /* ~38% reduction */
        }

        .section-title {
            font-size: 16px;
            /* ~20% reduction */
            margin-bottom: 16px;
            /* ~33% reduction */
        }

        .progress-stepper {
            padding: 24px 16px;
            /* Better spacing for mobile */
            border-radius: 12px;
        }

        .step-circle {
            width: 48px;
            /* Slightly larger for better visibility */
            height: 48px;
            font-size: 20px;
            /* Larger emoji/icon size */
            margin-bottom: 10px;
            border-width: 3px;
        }

        .step-label {
            font-size: 12px;
            /* More readable text size */
            line-height: 1.3;
            max-width: 70px;
            /* Prevent awkward wrapping */
            word-wrap: break-word;
        }

        .step-time {
            font-size: 10px;
            margin-top: 2px;
        }

        /* Progress line adjustments */
        .progress-stepper::before {
            top: 46px;
            /* Perfectly centered: 24px padding + 24px (half of 48px circle) - 1.5px (half of 3px line) */
            left: 12%;
            right: 12%;
            height: 3px;
        }

        .progress-line-fill {
            top: 46px;
            /* Perfectly centered: 24px padding + 24px (half of 48px circle) - 1.5px (half of 3px line) */
            left: 12%;
            height: 3px;
        }

        .payment-stepper {
            padding: 24px 50px;
            /* Better spacing for 2-step layout */
        }

        .payment-stepper::before {
            left: calc(25% + 24px);
            /* Updated for 48px circles */
            right: calc(25% + 24px);
        }

        .payment-stepper .progress-line-fill {
            left: calc(25% + 24px);
            /* Updated for 48px circles */
        }

        /* ETA Card */
        .eta-card {
            padding: 18px;
            /* ~25% reduction */
            border-radius: 12px;
            margin: 18px 0;
            /* ~25% reduction */
        }

        .eta-icon {
            font-size: 36px;
            /* ~25% reduction */
            margin-bottom: 8px;
            /* ~33% reduction */
        }

        .eta-title {
            font-size: 15px;
            /* ~17% reduction */
            margin-bottom: 6px;
            /* ~25% reduction */
        }

        .eta-time {
            font-size: 24px;
            /* ~25% reduction */
        }

        .eta-subtitle {
            font-size: 12px;
            /* ~14% reduction */
            margin-top: 6px;
            /* ~25% reduction */
        }

        /* Order Items Section */
        .order-items-section {
            margin: 20px 0;
            /* ~38% reduction */
        }

        /* Promotion Groups */
        .order-promotion-group {
            padding: 18px;
            /* ~25% reduction */
            border-radius: 12px;
            margin-bottom: 18px;
            /* ~25% reduction */
        }

        .promotion-group-header {
            margin-bottom: 12px;
            /* ~25% reduction */
            padding-bottom: 10px;
            /* ~17% reduction */
        }

        .promotion-group-title {
            font-size: 15px;
            /* ~17% reduction */
            gap: 8px;
            /* ~20% reduction */
        }

        .promotion-group-title i {
            font-size: 16px;
            /* ~20% reduction */
        }

        .promotion-type-badge {
            font-size: 9px;
            /* ~18% reduction */
            padding: 3px 8px;
            /* ~25% reduction */
            border-radius: 5px;
        }

        .promotion-savings {
            font-size: 12px;
            /* ~14% reduction */
            gap: 5px;
            /* ~17% reduction */
        }

        .promotion-savings i {
            font-size: 13px;
            /* ~19% reduction */
        }

        .promotion-group-items {
            margin-bottom: 12px;
            /* ~25% reduction */
        }

        .promotion-item {
            margin-bottom: 10px;
            /* ~17% reduction */
        }

        .promotion-group-total {
            padding-top: 12px;
            /* ~25% reduction */
            font-size: 15px;
            /* ~17% reduction */
        }

        /* Item Discount Group */
        .item-discount-group {
            padding: 18px;
            /* ~25% reduction */
            border-radius: 12px;
            margin-bottom: 18px;
            /* ~25% reduction */
        }

        .item-discount-header {
            margin-bottom: 12px;
            /* ~25% reduction */
            padding-bottom: 10px;
            /* ~17% reduction */
        }

        .item-discount-title {
            font-size: 15px;
            /* ~17% reduction */
            gap: 8px;
            /* ~20% reduction */
        }

        .item-discount-title i {
            font-size: 16px;
            /* ~20% reduction */
        }

        .item-discount-badge {
            font-size: 9px;
            /* ~18% reduction */
            padding: 3px 8px;
            /* ~25% reduction */
            border-radius: 5px;
        }

        .item-discount-savings {
            font-size: 12px;
            /* ~14% reduction */
            gap: 5px;
            /* ~17% reduction */
        }

        .item-discount-savings i {
            font-size: 13px;
            /* ~19% reduction */
        }

        .item-discount-items {
            margin-bottom: 12px;
            /* ~25% reduction */
        }

        .item-discount-item {
            margin-bottom: 10px;
            /* ~17% reduction */
        }

        /* Order Item Card */
        .order-item {
            gap: 14px;
            /* ~30% reduction */
            padding: 14px;
            /* ~30% reduction */
            border-radius: 12px;
            margin-bottom: 12px;
            /* ~25% reduction */
        }

        .item-image {
            width: 70px;
            /* ~30% reduction */
            height: 70px;
            border-radius: 10px;
        }

        .item-name {
            font-size: 14px;
            /* ~22% reduction */
            margin-bottom: 6px;
            /* ~25% reduction */
        }

        .item-quantity-price {
            gap: 12px;
            /* ~25% reduction */
            margin-bottom: 6px;
            /* ~25% reduction */
        }

        .item-quantity {
            font-size: 11px;
            /* ~21% reduction */
            padding: 5px 10px;
            /* ~17% reduction */
            border-radius: 6px;
        }

        .item-unit-price {
            font-size: 11px;
            /* ~21% reduction */
        }

        .item-notes {
            font-size: 11px;
            /* ~15% reduction */
            padding: 6px 10px;
            /* ~25% & ~17% reduction */
            border-radius: 6px;
            margin-top: 6px;
            /* ~25% reduction */
        }

        .item-price {
            font-size: 16px;
            /* ~20% reduction */
        }

        .free-badge {
            font-size: 8px;
            /* ~20% reduction */
            padding: 2px 6px;
            /* ~33% & ~25% reduction */
            border-radius: 3px;
            margin-left: 6px;
            /* ~25% reduction */
        }

        .original-price {
            margin-left: 6px;
            /* ~25% reduction */
            font-size: 11px;
            /* ~15% reduction */
        }

        /* Order Summary */
        .order-summary-card {
            padding: 20px;
            /* ~38% reduction */
            border-radius: 16px;
        }

        .summary-row {
            padding: 10px 0;
            /* ~29% reduction */
            font-size: 13px;
            /* ~19% reduction */
        }

        .summary-row.total {
            margin-top: 10px;
            /* ~17% reduction */
            padding-top: 16px;
            /* ~20% reduction */
            font-size: 18px;
            /* ~25% reduction */
        }

        /* Review Section */
        .review-section {
            margin-top: 20px;
            /* ~38% reduction */
            padding-top: 20px;
            /* ~38% reduction */
        }

        .review-items-list {
            gap: 16px;
            /* ~20% reduction */
            margin-top: 18px;
            /* ~25% reduction */
        }

        .review-item-card {
            padding: 18px;
            /* ~25% reduction */
            border-radius: 12px;
        }

        .review-item-header {
            margin-bottom: 12px;
            /* ~25% reduction */
            padding-bottom: 12px;
            /* ~25% reduction */
        }

        .review-item-name {
            font-size: 15px;
            /* ~17% reduction */
        }

        .review-item-quantity {
            padding: 5px 12px;
            /* ~17% & ~25% reduction */
            font-size: 12px;
            /* ~14% reduction */
        }

        .rating-section {
            margin-bottom: 12px;
            /* ~25% reduction */
        }

        .rating-label {
            margin-bottom: 6px;
            /* ~25% reduction */
            font-size: 12px;
            /* ~14% reduction */
        }

        .star-rating {
            gap: 6px;
            /* ~25% reduction */
            font-size: 26px;
            /* ~19% reduction */
        }

        .review-textarea {
            min-height: 80px;
            /* ~20% reduction */
            padding: 10px;
            /* ~17% reduction */
            border-radius: 10px;
            font-size: 12px;
            /* ~14% reduction */
        }

        .anonymous-checkbox {
            gap: 6px;
            /* ~25% reduction */
            margin-top: 10px;
            /* ~17% reduction */
        }

        .anonymous-checkbox input[type="checkbox"] {
            width: 16px;
            /* ~11% reduction */
            height: 16px;
        }

        .anonymous-checkbox label {
            font-size: 12px;
            /* ~14% reduction */
        }

        .submit-review-section {
            margin-top: 20px;
            /* ~38% reduction */
            gap: 10px;
            /* ~17% reduction */
            flex-direction: column;
        }

        .btn-submit-review {
            width: 100%;
            padding: 12px 24px;
            /* ~14% & ~25% reduction */
            font-size: 14px;
            /* ~13% reduction */
            border-radius: 10px;
        }

        .success-message,
        .error-message {
            padding: 12px;
            /* ~25% reduction */
            border-radius: 10px;
            margin-bottom: 16px;
            /* ~20% reduction */
            font-size: 14px;
        }

        .already-reviewed-badge {
            padding: 10px 18px;
            /* ~17% & ~25% reduction */
            border-radius: 10px;
            font-size: 14px;
            /* ~13% reduction */
        }
    }

    /* Small Mobile (480px) - Extra compact ~30% total reduction */
    @media (max-width: 480px) {
        .order-details-fullscreen {
            padding: 16px 0;
            /* ~50% reduction */
        }

        .order-details-container {
            padding: 0 12px;
            /* ~50% reduction */
        }

        .order-details-card {
            padding: 16px;
            /* ~60% reduction */
            border-radius: 14px;
        }

        .back-button {
            padding: 8px 14px;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .order-id-large {
            font-size: 20px;
            /* ~38% reduction */
            margin-bottom: 5px;
        }

        .order-date-time {
            font-size: 12px;
            /* ~20% reduction */
            margin-bottom: 10px;
        }

        .order-meta {
            gap: 8px;
            margin-top: 10px;
        }

        .meta-item {
            padding: 10px;
            /* ~38% reduction */
            border-radius: 8px;
        }

        .meta-label {
            font-size: 10px;
            /* ~23% reduction */
        }

        .meta-value {
            font-size: 13px;
            /* ~19% reduction */
        }

        .section-title {
            font-size: 14px;
            /* ~30% reduction */
            margin-bottom: 12px;
            /* ~50% reduction */
        }

        .progress-stepper {
            padding: 16px 8px;
            /* ~50% & ~75% reduction */
            border-radius: 10px;
        }

        .step-circle {
            width: 38px;
            /* ~32% reduction */
            height: 38px;
            font-size: 16px;
            /* ~33% reduction */
            margin-bottom: 6px;
            /* ~50% reduction */
            border-width: 2px;
        }

        .step-label {
            font-size: 10px;
            /* ~29% reduction */
        }

        .step-time {
            font-size: 9px;
            /* ~25% reduction */
        }

        .progress-stepper::before {
            top: calc(16px + 19px);
            left: 14%;
            right: 14%;
            height: 2px;
        }

        .progress-line-fill {
            top: calc(16px + 19px);
            left: 14%;
            height: 2px;
        }

        .payment-stepper {
            padding: 14px 30px;
            /* ~42% & ~50% reduction */
        }

        .payment-stepper::before {
            left: calc(25% + 19px);
            right: calc(25% + 19px);
        }

        .payment-stepper .progress-line-fill {
            left: calc(25% + 19px);
        }

        .eta-card {
            padding: 14px;
            /* ~42% reduction */
            border-radius: 10px;
            margin: 14px 0;
            /* ~42% reduction */
        }

        .eta-icon {
            font-size: 32px;
            /* ~33% reduction */
            margin-bottom: 6px;
            /* ~50% reduction */
        }

        .eta-title {
            font-size: 14px;
            /* ~22% reduction */
            margin-bottom: 5px;
            /* ~38% reduction */
        }

        .eta-time {
            font-size: 22px;
            /* ~31% reduction */
        }

        .eta-subtitle {
            font-size: 11px;
            /* ~21% reduction */
            margin-top: 5px;
            /* ~38% reduction */
        }

        .order-items-section {
            margin: 16px 0;
            /* ~50% reduction */
        }

        .order-promotion-group,
        .item-discount-group {
            padding: 14px;
            /* ~42% reduction */
            border-radius: 10px;
            margin-bottom: 14px;
            /* ~42% reduction */
        }

        .promotion-group-header,
        .item-discount-header {
            margin-bottom: 10px;
            /* ~38% reduction */
            padding-bottom: 8px;
            /* ~33% reduction */
        }

        .promotion-group-title,
        .item-discount-title {
            font-size: 13px;
            /* ~28% reduction */
            gap: 6px;
            /* ~40% reduction */
            flex-wrap: wrap;
        }

        .promotion-group-title i,
        .item-discount-title i {
            font-size: 14px;
            /* ~30% reduction */
        }

        .promotion-type-badge,
        .item-discount-badge {
            font-size: 8px;
            /* ~27% reduction */
            padding: 2px 6px;
            /* ~50% & ~40% reduction */
            border-radius: 4px;
        }

        .promotion-savings,
        .item-discount-savings {
            font-size: 11px;
            /* ~21% reduction */
            gap: 4px;
            /* ~33% reduction */
        }

        .promotion-savings i,
        .item-discount-savings i {
            font-size: 12px;
            /* ~25% reduction */
        }

        .promotion-group-items,
        .item-discount-items {
            margin-bottom: 10px;
            /* ~38% reduction */
        }

        .promotion-item,
        .item-discount-item {
            margin-bottom: 8px;
            /* ~33% reduction */
        }

        .promotion-group-total {
            padding-top: 10px;
            /* ~38% reduction */
            font-size: 13px;
            /* ~28% reduction */
        }

        .order-item {
            gap: 10px;
            /* ~50% reduction */
            padding: 10px;
            /* ~50% reduction */
            border-radius: 10px;
            margin-bottom: 10px;
            /* ~38% reduction */
        }

        .item-image {
            width: 60px;
            /* ~40% reduction */
            height: 60px;
            border-radius: 8px;
        }

        .item-name {
            font-size: 13px;
            /* ~28% reduction */
            margin-bottom: 5px;
            /* ~38% reduction */
        }

        .item-quantity-price {
            gap: 8px;
            /* ~50% reduction */
            margin-bottom: 5px;
            /* ~38% reduction */
            flex-wrap: wrap;
        }

        .item-quantity {
            font-size: 10px;
            /* ~29% reduction */
            padding: 4px 8px;
            /* ~33% reduction */
            border-radius: 5px;
        }

        .item-unit-price {
            font-size: 10px;
            /* ~29% reduction */
        }

        .item-notes {
            font-size: 10px;
            /* ~23% reduction */
            padding: 5px 8px;
            /* ~38% & ~33% reduction */
            border-radius: 5px;
            margin-top: 5px;
            /* ~38% reduction */
        }

        .item-price {
            font-size: 14px;
            /* ~30% reduction */
        }

        .free-badge {
            font-size: 7px;
            /* ~30% reduction */
            padding: 2px 5px;
            /* ~33% & ~38% reduction */
            border-radius: 3px;
            margin-left: 5px;
            /* ~38% reduction */
        }

        .original-price {
            margin-left: 5px;
            /* ~38% reduction */
            font-size: 10px;
            /* ~23% reduction */
        }

        .order-summary-card {
            padding: 16px;
            /* ~50% reduction */
            border-radius: 14px;
        }

        .summary-row {
            padding: 8px 0;
            /* ~43% reduction */
            font-size: 12px;
            /* ~25% reduction */
        }

        .summary-row.total {
            margin-top: 8px;
            /* ~33% reduction */
            padding-top: 12px;
            /* ~40% reduction */
            font-size: 16px;
            /* ~33% reduction */
        }

        .review-section {
            margin-top: 16px;
            /* ~50% reduction */
            padding-top: 16px;
            /* ~50% reduction */
        }

        .review-items-list {
            gap: 12px;
            /* ~40% reduction */
            margin-top: 14px;
            /* ~42% reduction */
        }

        .review-item-card {
            padding: 14px;
            /* ~42% reduction */
            border-radius: 10px;
        }

        .review-item-header {
            margin-bottom: 10px;
            /* ~38% reduction */
            padding-bottom: 10px;
            /* ~38% reduction */
        }

        .review-item-name {
            font-size: 13px;
            /* ~28% reduction */
        }

        .review-item-quantity {
            padding: 4px 10px;
            /* ~33% & ~38% reduction */
            font-size: 11px;
            /* ~21% reduction */
        }

        .rating-section {
            margin-bottom: 10px;
            /* ~38% reduction */
        }

        .rating-label {
            margin-bottom: 5px;
            /* ~38% reduction */
            font-size: 11px;
            /* ~21% reduction */
        }

        .star-rating {
            gap: 5px;
            /* ~38% reduction */
            font-size: 24px;
            /* ~25% reduction */
        }

        .review-textarea {
            min-height: 70px;
            /* ~30% reduction */
            padding: 8px;
            /* ~33% reduction */
            border-radius: 8px;
            font-size: 11px;
            /* ~21% reduction */
        }

        .anonymous-checkbox {
            gap: 5px;
            /* ~38% reduction */
            margin-top: 8px;
            /* ~33% reduction */
        }

        .anonymous-checkbox input[type="checkbox"] {
            width: 14px;
            /* ~22% reduction */
            height: 14px;
        }

        .anonymous-checkbox label {
            font-size: 11px;
            /* ~21% reduction */
        }

        .submit-review-section {
            margin-top: 16px;
            /* ~50% reduction */
            gap: 8px;
            /* ~33% reduction */
        }

        .btn-submit-review {
            padding: 10px 20px;
            /* ~29% & ~38% reduction */
            font-size: 13px;
            /* ~19% reduction */
            border-radius: 8px;
        }

        .success-message,
        .error-message {
            padding: 10px;
            /* ~38% reduction */
            border-radius: 8px;
            margin-bottom: 12px;
            /* ~40% reduction */
            font-size: 13px;
        }

        .already-reviewed-badge {
            padding: 8px 14px;
            /* ~33% & ~42% reduction */
            border-radius: 8px;
            font-size: 13px;
            /* ~19% reduction */
        }
    }
</style>
@endsection

@section('content')
<div class="order-details-fullscreen">
    <div class="order-details-container">
        <a href="{{ route('customer.orders.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>

        <div class="order-details-card">
            <!-- Order Header -->
            <div class="order-header">
                <div>
                    <div class="order-id-large">Order #{{ $order->confirmation_code ?? 'ORD-' . $order->id }}</div>
                    <div class="order-date-time">
                        <i class="fas fa-calendar"></i> {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </div>

                    <div class="order-meta">
                        <div class="meta-item">
                            <div class="meta-label">Order Type</div>
                            <div class="meta-value">
                                @if($order->order_type === 'dine_in')
                                <i class="fas fa-utensils"></i> Dine In
                                @else
                                <i class="fas fa-shopping-bag"></i> Takeaway
                                @endif
                            </div>
                        </div>

                        @if($order->order_type === 'dine_in' && ($order->table || $order->table_number))
                        <div class="meta-item">
                            <div class="meta-label">Table</div>
                            <div class="meta-value" style="color: #6366f1;">
                                <i class="fas fa-chair"></i>
                                @if($order->table)
                                {{ $order->table->table_number }}
                                @if($order->table->location)
                                <div style="font-size: 11px; color: var(--text-2); margin-top: 2px;">({{ $order->table->location }})</div>
                                @endif
                                @else
                                {{ $order->table_number }}
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="meta-item">
                            <div class="meta-label">Payment Method</div>
                            <div class="meta-value">
                                @if($order->payment_method === 'online')
                                <i class="fas fa-credit-card"></i> Online Payment
                                @else
                                <i class="fas fa-money-bill"></i> Pay at Restaurant
                                @endif
                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Total Amount</div>
                            <div class="meta-value" style="color: #6366f1;">RM {{ number_format($order->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Progress -->
            <div class="progress-section">
                <div class="section-title">
                    <i class="fas fa-tasks"></i> Order Status
                </div>
                <div class="progress-stepper">
                    @php
                    $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
                    $statusLabels = [
                    'pending' => 'Order Placed',
                    'confirmed' => 'Confirmed',
                    'preparing' => 'Preparing',
                    'ready' => 'Ready',
                    'completed' => 'Completed'
                    ];
                    $statusIcons = [
                    'pending' => '📝',
                    'confirmed' => '✅',
                    'preparing' => '👨‍🍳',
                    'ready' => '🔔',
                    'completed' => '✨'
                    ];
                    $currentIndex = array_search($order->order_status, $statuses);
                    if ($currentIndex === false) $currentIndex = 0;
                    $progressPercent = ($currentIndex / (count($statuses) - 1)) * 80;
                    @endphp

                    <div class="progress-line-fill" style="width: {{ $progressPercent }}%;"></div>

                    @foreach($statuses as $index => $status)
                    <div class="step {{ $index < $currentIndex ? 'completed' : ($index === $currentIndex ? 'active' : '') }}">
                        <div class="step-circle">
                            @if($index < $currentIndex)
                                <i class="fas fa-check"></i>
                                @else
                                {{ $statusIcons[$status] }}
                                @endif
                        </div>
                        <div class="step-label">{{ $statusLabels[$status] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Status Progress -->
            <div class="progress-section">
                <div class="section-title">
                    <i class="fas fa-credit-card"></i> Payment Status
                </div>
                <div class="progress-stepper payment-stepper">
                    @php
                    $paymentStatuses = ['unpaid', 'paid'];
                    $paymentLabels = ['Unpaid', 'Paid'];
                    $paymentIcons = ['💳', '✅'];
                    $paymentIndex = $order->payment_status === 'paid' ? 1 : 0;
                    $paymentProgress = $paymentIndex * 100;
                    @endphp

                    <div class="progress-line-fill" style="width: {{ $paymentProgress === 100 ? 'calc(50% - 56px)' : '0%' }};"></div>

                    @foreach($paymentStatuses as $index => $status)
                    <div class="step {{ $index < $paymentIndex ? 'completed' : ($index === $paymentIndex ? 'active' : '') }}">
                        <div class="step-circle">
                            @if($index < $paymentIndex)
                                <i class="fas fa-check"></i>
                                @else
                                {{ $paymentIcons[$index] }}
                                @endif
                        </div>
                        <div class="step-label">{{ $paymentLabels[$index] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- ETA if exists -->
            @if($order->estimated_completion_time)
            <div class="eta-card">
                <div class="eta-icon">⏱️</div>
                <div class="eta-title">Estimated Completion Time</div>
                <div class="eta-time">{{ $order->estimated_completion_time->format('g:i A') }}</div>
                <div class="eta-subtitle">{{ $order->estimated_completion_time->diffForHumans() }}</div>
            </div>
            @endif

            <!-- Order Items -->
            <div class="order-items-section">
                <div class="section-title">
                    <i class="fas fa-shopping-cart"></i> Order Items
                </div>

                {{-- Promotion Groups --}}
                @foreach($promotionGroups as $group)
                <div class="order-promotion-group">
                    <div class="promotion-group-header">
                        <div class="promotion-group-title">
                            <i class="fas fa-gift"></i>
                            <span>{{ $group['promotion']->name ?? 'Promotion' }}</span>
                            <span class="promotion-type-badge">{{ $group['promotion']->type_label ?? 'Bundle' }}</span>
                        </div>
                        @if($group['savings'] > 0)
                        <div class="promotion-savings">
                            <i class="fas fa-tag"></i> Saved RM {{ number_format($group['savings'], 2) }}
                        </div>
                        @endif
                    </div>

                    <div class="promotion-group-items">
                        @foreach($group['items'] as $item)
                        <div class="order-item promotion-item">
                            @if($item->menuItem && $item->menuItem->image_url)
                            <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="item-image">
                            @else
                            <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                            </div>
                            @endif
                            <div class="item-details">
                                <div class="item-name">
                                    {{ $item->menuItem->name ?? 'Unknown Item' }}
                                    @if($item->is_free_item)
                                    <span class="free-badge">FREE</span>
                                    @endif
                                </div>
                                <div class="item-unit-price">
                                    RM {{ number_format($item->unit_price, 2) }} (x{{ $item->quantity }})
                                    @if($item->original_price && $item->original_price > $item->unit_price)
                                    <span class="original-price">RM {{ number_format($item->original_price, 2) }}</span>
                                    @endif
                                </div>
                                @php
                                $addons = $item->customizations()->where('customization_type', 'addon')->get();
                                @endphp
                                @if($addons->count() > 0)
                                <div style="margin-top: 6px; font-size: 12px; color: #3b82f6; font-style: italic;">
                                    <i class="fas fa-puzzle-piece"></i> {{ $addons->pluck('customization_value')->join(', ') }}
                                </div>
                                @endif
                            </div>
                            <div class="item-price">RM {{ number_format($item->total_price, 2) }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="promotion-group-total">
                        <span>Bundle Total:</span>
                        <span>RM {{ number_format($group['total_price'], 2) }}</span>
                    </div>
                </div>
                @endforeach

                {{-- Item Discounts --}}
                @if(count($itemDiscounts) > 0)
                @php
                $totalItemDiscountSavings = 0;
                foreach($itemDiscounts as $item) {
                $totalItemDiscountSavings += ($item->discount_amount * $item->quantity);
                }
                @endphp
                <div class="item-discount-group">
                    <div class="item-discount-header">
                        <div class="item-discount-title">
                            <i class="fas fa-percent"></i>
                            <span>Item Discounts</span>
                            <span class="item-discount-badge">Discount</span>
                        </div>
                        @if($totalItemDiscountSavings > 0)
                        <div class="item-discount-savings">
                            <i class="fas fa-tag"></i> Saved RM {{ number_format($totalItemDiscountSavings, 2) }}
                        </div>
                        @endif
                    </div>

                    <div class="item-discount-items">
                        @foreach($itemDiscounts as $item)
                        <div class="order-item item-discount-item">
                            @if($item->menuItem && $item->menuItem->image_url)
                            <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="item-image">
                            @else
                            <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                            </div>
                            @endif
                            <div class="item-details">
                                <div class="item-name">
                                    {{ $item->menuItem->name ?? 'Unknown Item' }}
                                </div>
                                <div class="item-unit-price">
                                    RM {{ number_format($item->unit_price, 2) }} (x{{ $item->quantity }})
                                    @if($item->original_price && $item->original_price > $item->unit_price)
                                    <span class="original-price">RM {{ number_format($item->original_price, 2) }}</span>
                                    @endif
                                </div>
                                @if($item->promotion)
                                <div class="item-notes">
                                    <i class="fas fa-tag"></i> {{ $item->promotion->name }}
                                </div>
                                @endif
                                @php
                                $addons = $item->customizations()->where('customization_type', 'addon')->get();
                                @endphp
                                @if($addons->count() > 0)
                                <div style="margin-top: 6px; font-size: 12px; color: #3b82f6; font-style: italic;">
                                    <i class="fas fa-puzzle-piece"></i> {{ $addons->pluck('customization_value')->join(', ') }}
                                </div>
                                @endif
                            </div>
                            <div class="item-price">RM {{ number_format($item->total_price, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Order-Level Promotions --}}
                @if($orderPromotions->count() > 0)
                @foreach($orderPromotions as $promotion)
                @if($promotion)
                <div class="item-discount-group">
                    <div class="item-discount-header">
                        <div class="item-discount-title">
                            <i class="fas fa-ticket-alt"></i>
                            <span>{{ $promotion->name }}</span>
                            <span class="item-discount-badge">{{ $promotion->type_label }}</span>
                        </div>
                        @php
                        $promoUsage = $order->promotionUsageLogs->where('promotion_id', $promotion->id)->first();
                        $discountAmount = $promoUsage ? $promoUsage->discount_amount : 0;
                        @endphp
                        @if($discountAmount > 0)
                        <div class="item-discount-savings">
                            <i class="fas fa-tag"></i> Saved RM {{ number_format($discountAmount, 2) }}
                        </div>
                        @endif
                    </div>

                    <div class="item-discount-items">
                        <div class="order-item item-discount-item">
                            <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-ticket-alt" style="font-size: 32px; color: #9ca3af;"></i>
                            </div>
                            <div class="item-details">
                                <div class="item-name">
                                    Promo Code Applied
                                </div>
                                <div class="item-unit-price">
                                    Code: <strong>{{ $promotion->promo_code ?? 'N/A' }}</strong>
                                </div>
                                @if($promotion->discount_type === 'percentage')
                                <div class="item-notes">
                                    <i class="fas fa-percent"></i> {{ number_format($promotion->discount_value, 0) }}% OFF
                                </div>
                                @else
                                <div class="item-notes">
                                    <i class="fas fa-money-bill"></i> RM {{ number_format($promotion->discount_value, 2) }} OFF
                                </div>
                                @endif
                            </div>
                            <div class="item-price">
                                @if($discountAmount > 0)
                                -RM {{ number_format($discountAmount, 2) }}
                                @else
                                RM 0.00
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endif

                {{-- Voucher Discount --}}
                @if($order->customer_voucher_id && $order->voucher_discount > 0)
                <div class="item-discount-group">
                    <div class="item-discount-header">
                        <div class="item-discount-title">
                            <i class="fas fa-gift"></i>
                            <span>{{ $order->customerVoucher && $order->customerVoucher->voucherTemplate ? $order->customerVoucher->voucherTemplate->name : 'Reward Voucher' }}</span>
                            <span class="item-discount-badge">VOUCHER</span>
                        </div>
                        <div class="item-discount-savings">
                            <i class="fas fa-tag"></i> Saved RM {{ number_format($order->voucher_discount, 2) }}
                        </div>
                    </div>

                    <div class="item-discount-items">
                        <div class="order-item item-discount-item">
                            <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-gift" style="font-size: 32px; color: #9ca3af;"></i>
                            </div>
                            <div class="item-details">
                                <div class="item-name">
                                    Voucher Discount Applied
                                </div>
                                <div class="item-unit-price">
                                    Code: <strong>{{ $order->voucher_code ?? ($order->customerVoucher ? $order->customerVoucher->voucher_code : 'N/A') }}</strong>
                                </div>
                                @if($order->customerVoucher && $order->customerVoucher->voucherTemplate)
                                @php
                                $template = $order->customerVoucher->voucherTemplate;
                                @endphp
                                @if($template->discount_type === 'percentage')
                                <div class="item-notes">
                                    <i class="fas fa-percent"></i> {{ number_format($template->discount_value, 0) }}% OFF
                                </div>
                                @else
                                <div class="item-notes">
                                    <i class="fas fa-money-bill"></i> RM {{ number_format($template->discount_value, 2) }} OFF
                                </div>
                                @endif
                                @endif
                            </div>
                            <div class="item-price">
                                -RM {{ number_format($order->voucher_discount, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Regular Items --}}
                @foreach($regularItems as $item)
                <div class="order-item">
                    @if($item->menuItem && $item->menuItem->image_url)
                    <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="item-image">
                    @else
                    <div class="item-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                    </div>
                    @endif
                    <div class="item-details">
                        <div class="item-name">{{ $item->menuItem->name ?? 'Unknown Item' }}</div>
                        <div class="item-unit-price">
                            RM {{ number_format($item->unit_price, 2) }} (x{{ $item->quantity }})
                        </div>
                        @if($item->special_note)
                        <div class="item-notes">
                            <i class="fas fa-comment"></i> {{ $item->special_note }}
                        </div>
                        @endif
                        @php
                        $addons = $item->customizations()->where('customization_type', 'addon')->get();
                        @endphp
                        @if($addons->count() > 0)
                        <div style="margin-top: 6px; font-size: 12px; color: #3b82f6; font-style: italic;">
                            <i class="fas fa-puzzle-piece"></i> {{ $addons->pluck('customization_value')->join(', ') }}
                        </div>
                        @endif
                    </div>
                    <div class="item-price">RM {{ number_format($item->total_price, 2) }}</div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="order-summary-card">
                <div class="section-title">
                    <i class="fas fa-receipt"></i> Order Summary
                </div>
                <div class="summary-row">
                    <span>Subtotal ({{ $order->items->count() }} items)</span>
                    <span>RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Tax & Service</span>
                    <span>RM 0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Review Section (Only for completed/served orders and logged in users) -->
            @auth
            @if(false && in_array($order->order_status, ['completed', 'served']))
            <div class="review-section" id="review-section" style="display: none;">
                <div class="section-title">
                    <i class="fas fa-star"></i> Rate Your Order
                </div>

                @if(session('review_success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> {{ session('review_success') }}
                </div>
                @endif

                @if(session('review_error'))
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> {{ session('review_error') }}
                </div>
                @endif

                @php
                $hasReviews = $order->reviews()->exists();
                $reviewableItems = [];

                if (!$hasReviews) {
                foreach ($order->items as $orderItem) {
                if (!$orderItem->menuItem) continue;

                $existingReview = $order->reviews()
                ->where('menu_item_id', $orderItem->menu_item_id)
                ->first();

                if (!$existingReview) {
                $reviewableItems[] = [
                'order_item_id' => $orderItem->id,
                'menu_item_id' => $orderItem->menu_item_id,
                'menu_item' => $orderItem->menuItem,
                'quantity' => $orderItem->quantity
                ];
                }
                }
                }
                @endphp

                @if($hasReviews)
                <div class="already-reviewed-badge">
                    <i class="fas fa-check-circle"></i> Thank you! You've already reviewed this order.
                </div>
                @elseif(!empty($reviewableItems))
                <form id="reviewForm" method="POST" action="#" data-disabled-route="customer.reviews.store-batch">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="review-items-list">
                        @foreach($reviewableItems as $index => $item)
                        <div class="review-item-card">
                            <div class="review-item-header">
                                <span class="review-item-name">{{ $item['menu_item']->name }}</span>
                                <span class="review-item-quantity">x{{ $item['quantity'] }}</span>
                            </div>

                            <input type="hidden" name="reviews[{{ $index }}][menu_item_id]" value="{{ $item['menu_item_id'] }}">

                            <div class="rating-section">
                                <label class="rating-label">Rating *</label>
                                <div class="star-rating" data-item-index="{{ $index }}">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-rating="{{ $i }}">★</span>
                                        @endfor
                                </div>
                                <input type="hidden" name="reviews[{{ $index }}][rating]" class="rating-input" required>
                            </div>

                            <div>
                                <label for="review_text_{{ $index }}" class="rating-label">Your Review (Optional)</label>
                                <textarea
                                    name="reviews[{{ $index }}][review_text]"
                                    id="review_text_{{ $index }}"
                                    class="review-textarea"
                                    placeholder="Tell us about your experience with this dish..."></textarea>
                            </div>

                            <div class="anonymous-checkbox">
                                <input
                                    type="checkbox"
                                    name="reviews[{{ $index }}][is_anonymous]"
                                    id="anonymous_{{ $index }}"
                                    value="1">
                                <label for="anonymous_{{ $index }}">Post anonymously</label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="submit-review-section">
                        <button type="submit" class="btn-submit-review" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Submit Reviews
                        </button>
                    </div>
                </form>
                @else
                <p style="text-align: center; color: #6b7280; padding: 20px;">
                    No items available to review.
                </p>
                @endif
            </div>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Pusher for real-time order status updates -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time order status updates via Pusher
        const currentOrderId = {
            {
                $order - > id
            }
        };
        const orderStatus = '{{ $order->order_status }}';

        // Only listen for updates if order is not completed/cancelled
        if (['pending', 'confirmed', 'preparing', 'ready'].includes(orderStatus)) {
            // Initialize Pusher
            const pusher = new Pusher('{{ config('
                broadcasting.connections.pusher.key ') }}', {
                    cluster: '{{ config('
                    broadcasting.connections.pusher.options.cluster ') }}',
                    encrypted: true
                });

            // Subscribe to kitchen-display channel
            const channel = pusher.subscribe('kitchen-display');

            // Listen for order status updates
            channel.bind('order.status.updated', function(data) {
                console.log('Pusher event received:', data);

                // Filter: Only process if this is OUR order
                if (data.order_id === currentOrderId) {
                    console.log('Order status changed:', data.old_status, '→', data.new_status);

                    // Show toast notification
                    if (typeof Toastify !== 'undefined') {
                        Toastify({
                            text: `Order status updated: ${data.new_status}`,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#4CAF50",
                        }).showToast();
                    }

                    // Reload page after 2 seconds to show updated status
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });

            console.log('Pusher listening for order', currentOrderId);
        }

        // Smooth scroll to review section if hash present
        if (window.location.hash === '#review-section') {
            setTimeout(() => {
                const reviewSection = document.getElementById('review-section');
                if (reviewSection) {
                    reviewSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 100);
        }

        // Review form star rating functionality
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            const starRatings = document.querySelectorAll('.star-rating');
            const submitBtn = document.getElementById('submitBtn');

            // Handle star rating clicks
            starRatings.forEach(ratingContainer => {
                const stars = ratingContainer.querySelectorAll('.star');
                const itemIndex = ratingContainer.dataset.itemIndex;
                const ratingInput = document.querySelector(`input[name="reviews[${itemIndex}][rating]"]`);

                stars.forEach((star, index) => {
                    star.addEventListener('click', function() {
                        const rating = this.dataset.rating;
                        ratingInput.value = rating;

                        // Update star display
                        stars.forEach((s, i) => {
                            if (i < rating) {
                                s.classList.add('active');
                            } else {
                                s.classList.remove('active');
                            }
                        });
                    });

                    // Hover effect
                    star.addEventListener('mouseenter', function() {
                        const rating = this.dataset.rating;
                        stars.forEach((s, i) => {
                            if (i < rating) {
                                s.style.color = '#fbbf24';
                            } else {
                                s.style.color = '#d1d5db';
                            }
                        });
                    });
                });

                // Reset hover effect
                ratingContainer.addEventListener('mouseleave', function() {
                    const currentRating = ratingInput.value;
                    stars.forEach((s, i) => {
                        if (i < currentRating) {
                            s.style.color = '#fbbf24';
                        } else {
                            s.style.color = '#d1d5db';
                        }
                    });
                });
            });

            // Form submission
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate that all items have ratings
                const ratingInputs = document.querySelectorAll('.rating-input');
                let allRated = true;

                ratingInputs.forEach(input => {
                    if (!input.value) {
                        allRated = false;
                    }
                });

                if (!allRated) {
                    if (typeof Toast !== 'undefined') {
                        Toast.warning('Incomplete', 'Please rate all items before submitting.');
                    } else {
                        alert('Please rate all items before submitting.');
                    }
                    return;
                }

                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

                // Submit via AJAX
                const formData = new FormData(reviewForm);

                // Convert to JSON structure
                const reviews = [];
                ratingInputs.forEach((input, index) => {
                    const reviewTextArea = document.querySelector(`textarea[name="reviews[${index}][review_text]"]`);
                    const isAnonymousCheckbox = document.querySelector(`input[name="reviews[${index}][is_anonymous]"]`);
                    const menuItemIdInput = document.querySelector(`input[name="reviews[${index}][menu_item_id]"]`);

                    reviews.push({
                        menu_item_id: menuItemIdInput.value,
                        rating: input.value,
                        review_text: reviewTextArea.value,
                        is_anonymous: isAnonymousCheckbox.checked ? 1 : 0
                    });
                });

                const data = {
                    order_id: document.querySelector('input[name="order_id"]').value,
                    reviews: reviews
                };

                // Rating feature disabled - route removed
                console.warn('Rating feature is currently disabled');
                if (typeof Toast !== 'undefined') {
                    Toast.info('Unavailable', 'Rating feature is currently unavailable.');
                } else {
                    alert('Rating feature is currently unavailable.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Reviews';
                return;

                /* DISABLED - Rating feature hidden
                fetch('#', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Toast !== 'undefined') {
                            Toast.success('Success', data.message);
                        } else {
                            alert(data.message);
                        }
                        // Reload page to show "already reviewed" badge
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        if (typeof Toast !== 'undefined') {
                            Toast.error('Failed', data.message || 'Failed to submit reviews. Please try again.');
                        } else {
                            alert(data.message || 'Failed to submit reviews. Please try again.');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Reviews';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Toast !== 'undefined') {
                        Toast.error('Error', 'An error occurred. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Reviews';
                });
                */
            });
        }
    });
</script>
@endsection