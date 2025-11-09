@extends('layouts.customer')

@section('title', $promotion->name)

@section('content')
<style>
    /* Override parent flex layout */
    body {
        display: block !important;
    }

    .bundle-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        text-decoration: none;
        margin-bottom: 24px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        color: #ef4444;
        transform: translateX(-4px);
    }

    .hero-banner {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 24px;
        padding: 48px;
        color: white;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(239, 68, 68, 0.3);
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-header {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .hero-icon {
        font-size: 48px;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .hero-title {
        margin: 0;
        font-size: 42px;
        font-weight: 800;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .hero-description {
        margin: 12px 0 0 0;
        font-size: 18px;
        opacity: 0.95;
        line-height: 1.6;
    }

    .hero-image {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 16px;
        margin-top: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .card-title {
        margin: 0 0 28px 0;
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-title i {
        color: #ef4444;
        font-size: 32px;
    }

    .price-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
    }

    .price-main {
        font-size: 64px;
        font-weight: 800;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .price-regular {
        font-size: 20px;
        color: #9ca3af;
        text-decoration: line-through;
        margin-top: 8px;
        font-weight: 500;
    }

    .savings-badge {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
        padding: 24px 32px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
        animation: savesPulse 2s ease-in-out infinite;
    }

    @keyframes savesPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .savings-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }

    .savings-amount {
        font-size: 36px;
        font-weight: 800;
        margin-top: 8px;
    }

    .savings-percent {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 4px;
        font-weight: 600;
    }

    .bundle-items-grid {
        display: grid;
        gap: 20px;
    }

    .bundle-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border: 2px solid #f3f4f6;
        border-radius: 16px;
        transition: all 0.3s ease;
        background: linear-gradient(to right, #ffffff 0%, #fafafa 100%);
    }

    .bundle-item:hover {
        border-color: #ef4444;
        transform: translateX(8px);
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.1);
    }

    .item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .item-placeholder {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .item-description {
        margin: 0;
        font-size: 14px;
        color: #6b7280;
        line-height: 1.5;
    }

    .item-tags {
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .tag-price {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .tag-quantity {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #2563eb;
    }

    .item-total {
        text-align: right;
        min-width: 120px;
    }

    .item-total-price {
        font-size: 24px;
        font-weight: 800;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .item-total-label {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 3px solid #f3f4f6;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        margin-bottom: 12px;
    }

    .summary-row.discount {
        color: #16a34a;
        font-weight: 600;
    }

    .summary-row.total {
        font-size: 28px;
        font-weight: 800;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 2px solid #f3f4f6;
    }

    .validity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .validity-card {
        padding: 28px;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 16px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .validity-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .validity-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .validity-value {
        font-size: 22px;
        font-weight: 800;
        color: #1f2937;
    }

    .cta-button {
        width: 100%;
        padding: 24px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(239, 68, 68, 0.4);
    }

    .cta-button:active {
        transform: translateY(0);
    }

    .cta-info {
        text-align: center;
        margin: 16px 0 0 0;
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        color: #9ca3af;
        padding: 60px 20px;
        font-size: 16px;
    }

    /* Success/Error Modal Styles */
    .message-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .message-modal {
        background: white;
        border-radius: 24px;
        padding: 40px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        text-align: center;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-icon {
        font-size: 64px;
        margin-bottom: 24px;
        animation: bounceIn 0.5s ease;
    }

    @keyframes bounceIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .message-icon.success {
        color: #16a34a;
    }

    .message-icon.error {
        color: #dc2626;
    }

    .message-title {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 16px;
        color: #1f2937;
    }

    .message-content {
        font-size: 16px;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 28px;
    }

    .message-items {
        background: #f9fafb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 28px;
        text-align: left;
    }

    .message-items-title {
        font-size: 14px;
        font-weight: 700;
        color: #4b5563;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .message-items-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .message-items-list li {
        padding: 8px 0;
        color: #1f2937;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .message-items-list li:before {
        content: '✓';
        color: #16a34a;
        font-weight: bold;
        font-size: 16px;
    }

    .message-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .message-btn {
        padding: 16px 32px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    .message-btn.primary {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .message-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }

    .message-btn.secondary {
        background: #f3f4f6;
        color: #4b5563;
    }

    .message-btn.secondary:hover {
        background: #e5e7eb;
    }

    @media (max-width: 768px) {
        .hero-banner {
            padding: 32px 24px;
        }

        .hero-title {
            font-size: 32px;
        }

        .price-main {
            font-size: 48px;
        }

        .card {
            padding: 24px;
        }

        .bundle-item {
            flex-direction: column;
            text-align: center;
        }

        .item-total {
            text-align: center;
        }

        .message-modal {
            padding: 32px 24px;
        }

        .message-icon {
            font-size: 48px;
        }

        .message-title {
            font-size: 24px;
        }

        .message-buttons {
            flex-direction: column;
        }

        .message-btn {
            width: 100%;
        }
    }

    /* ===== Large Desktop (1600px+) - 30-40% increase ===== */
    @media (min-width: 1600px) {
        .bundle-container {
            max-width: 1500px;
            padding: 28px;
        }

        .back-link {
            font-size: 1.05rem;
            margin-bottom: 30px;
        }

        .hero-banner {
            border-radius: 30px;
            padding: 60px;
            margin-bottom: 40px;
        }

        .hero-icon {
            font-size: 60px;
        }

        .hero-title {
            font-size: 52px;
        }

        .hero-description {
            font-size: 22px;
        }

        .hero-image {
            max-height: 500px;
            border-radius: 20px;
        }

        .card {
            border-radius: 30px;
            padding: 50px;
            margin-bottom: 40px;
        }

        .card-title {
            font-size: 35px;
            margin-bottom: 35px;
        }

        .card-title i {
            font-size: 40px;
        }

        .price-main {
            font-size: 80px;
        }

        .price-regular {
            font-size: 25px;
        }

        .savings-badge {
            padding: 30px 40px;
        }

        .savings-amount {
            font-size: 45px;
        }

        .item-image, .item-placeholder {
            width: 130px;
            height: 130px;
        }

        .item-name {
            font-size: 25px;
        }

        .cta-button {
            padding: 30px;
            font-size: 25px;
        }
    }

    /* ===== Tablet (769px - 1199px) - 20-25% reduction ===== */
    @media (max-width: 1199px) and (min-width: 769px) {
        .bundle-container {
            padding: 18px;
        }

        .back-link {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .hero-banner {
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 28px;
        }

        .hero-header {
            gap: 18px;
            margin-bottom: 20px;
        }

        .hero-icon {
            font-size: 42px;
        }

        .hero-title {
            font-size: 36px;
        }

        .hero-description {
            font-size: 16px;
        }

        .hero-image {
            max-height: 350px;
            border-radius: 14px;
            margin-top: 20px;
        }

        .card {
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 28px;
        }

        .card-title {
            font-size: 24px;
            margin-bottom: 24px;
            gap: 10px;
        }

        .card-title i {
            font-size: 28px;
        }

        .price-container {
            gap: 20px;
        }

        .price-main {
            font-size: 54px;
        }

        .price-regular {
            font-size: 18px;
            margin-top: 6px;
        }

        .savings-badge {
            padding: 20px 28px;
            border-radius: 14px;
        }

        .savings-label {
            font-size: 11px;
        }

        .savings-amount {
            font-size: 30px;
            margin-top: 6px;
        }

        .savings-percent {
            font-size: 13px;
        }

        .bundle-items-grid {
            gap: 18px;
        }

        .bundle-item {
            gap: 18px;
            padding: 18px;
            border-radius: 14px;
        }

        .item-image, .item-placeholder {
            width: 85px;
            height: 85px;
            border-radius: 10px;
        }

        .item-name {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .item-description {
            font-size: 13px;
        }

        .item-tags {
            margin-top: 10px;
            gap: 10px;
        }

        .tag {
            padding: 5px 12px;
            font-size: 12px;
        }

        .item-total {
            min-width: 105px;
        }

        .item-total-price {
            font-size: 21px;
        }

        .item-total-label {
            font-size: 11px;
        }

        .summary-section {
            margin-top: 28px;
            padding-top: 28px;
        }

        .summary-row {
            font-size: 14.5px;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-size: 24px;
            margin-top: 14px;
            padding-top: 14px;
        }

        .validity-grid {
            gap: 18px;
        }

        .validity-card {
            padding: 24px;
            border-radius: 14px;
        }

        .validity-label {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .validity-value {
            font-size: 19px;
        }

        .cta-button {
            padding: 20px;
            font-size: 18px;
            border-radius: 14px;
        }

        .cta-info {
            font-size: 13px;
            margin-top: 14px;
        }

        .empty-state {
            padding: 50px 18px;
            font-size: 14.5px;
        }

        .message-modal {
            padding: 35px;
            max-width: 450px;
        }

        .message-icon {
            font-size: 56px;
            margin-bottom: 20px;
        }

        .message-title {
            font-size: 24px;
            margin-bottom: 14px;
        }

        .message-content {
            font-size: 14.5px;
            margin-bottom: 24px;
        }

        .message-items {
            padding: 18px;
            margin-bottom: 24px;
        }

        .message-btn {
            padding: 14px 28px;
            font-size: 14.5px;
            min-width: 110px;
        }
    }

    /* ===== Mobile (max-width: 768px) - Enhanced ===== */
    @media (max-width: 768px) {
        .bundle-container {
            padding: 15px;
        }

        .back-link {
            font-size: 0.85rem;
            margin-bottom: 18px;
        }

        .hero-banner {
            padding: 28px 20px;
            border-radius: 18px;
            margin-bottom: 24px;
        }

        .hero-header {
            gap: 14px;
            margin-bottom: 18px;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .hero-icon {
            font-size: 36px;
        }

        .hero-title {
            font-size: 28px;
        }

        .hero-description {
            font-size: 15px;
        }

        .hero-image {
            max-height: 280px;
            border-radius: 12px;
            margin-top: 18px;
        }

        .card {
            border-radius: 18px;
            padding: 24px 18px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 21px;
            margin-bottom: 22px;
            gap: 8px;
        }

        .card-title i {
            font-size: 24px;
        }

        .price-container {
            gap: 18px;
            flex-direction: column;
            align-items: flex-start;
        }

        .price-main {
            font-size: 42px;
        }

        .price-regular {
            font-size: 16px;
            margin-top: 6px;
        }

        .savings-badge {
            padding: 18px 24px;
            border-radius: 12px;
            width: 100%;
        }

        .savings-label {
            font-size: 10px;
        }

        .savings-amount {
            font-size: 28px;
            margin-top: 6px;
        }

        .savings-percent {
            font-size: 12px;
        }

        .bundle-items-grid {
            gap: 14px;
        }

        .bundle-item {
            flex-direction: column;
            text-align: center;
            gap: 14px;
            padding: 16px;
            border-radius: 12px;
        }

        .item-image, .item-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 10px;
        }

        .item-name {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .item-description {
            font-size: 12px;
        }

        .item-tags {
            margin-top: 8px;
            gap: 8px;
            justify-content: center;
        }

        .tag {
            padding: 4px 10px;
            font-size: 11px;
        }

        .item-total {
            text-align: center;
            min-width: auto;
            width: 100%;
        }

        .item-total-price {
            font-size: 20px;
        }

        .item-total-label {
            font-size: 10px;
        }

        .summary-section {
            margin-top: 24px;
            padding-top: 24px;
        }

        .summary-row {
            font-size: 13.5px;
            margin-bottom: 9px;
        }

        .summary-row.total {
            font-size: 22px;
            margin-top: 12px;
            padding-top: 12px;
        }

        .validity-grid {
            gap: 14px;
            grid-template-columns: 1fr;
        }

        .validity-card {
            padding: 20px;
            border-radius: 12px;
        }

        .validity-label {
            font-size: 10px;
            margin-bottom: 9px;
        }

        .validity-value {
            font-size: 18px;
        }

        .cta-button {
            padding: 18px;
            font-size: 16px;
            border-radius: 12px;
        }

        .cta-info {
            font-size: 12px;
            margin-top: 12px;
        }

        .empty-state {
            padding: 45px 15px;
            font-size: 13.5px;
        }

        .message-overlay {
            padding: 12px;
        }

        .message-modal {
            padding: 28px 20px;
            max-width: none;
            width: 100%;
            border-radius: 18px;
        }

        .message-icon {
            font-size: 42px;
            margin-bottom: 18px;
        }

        .message-title {
            font-size: 21px;
            margin-bottom: 12px;
        }

        .message-content {
            font-size: 13.5px;
            margin-bottom: 22px;
        }

        .message-items {
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 22px;
        }

        .message-items-title {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .message-items-list li {
            padding: 6px 0;
            font-size: 13px;
        }

        .message-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .message-btn {
            padding: 12px 24px;
            font-size: 13.5px;
            min-width: auto;
            width: 100%;
            border-radius: 10px;
        }
    }
</style>

<div class="bundle-container">
    {{-- Back Button --}}
    <a href="{{ route('customer.promotions.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Promotions
    </a>

    {{-- Hero Banner --}}
    <div class="hero-banner">
        <div class="hero-content">
            <div class="hero-header">
                <i class="fas fa-box-open hero-icon"></i>
                <div>
                    <h1 class="hero-title">{{ $promotion->name }}</h1>
                    <p class="hero-description">
                        {{ $promotion->description ?? 'Special bundle deal with amazing value' }}
                    </p>
                </div>
            </div>

            @if($promotion->banner_image)
                <img src="{{ asset('storage/' . $promotion->banner_image) }}"
                     alt="{{ $promotion->name }}"
                     class="hero-image">
            @endif
        </div>
    </div>

    {{-- Bundle Price --}}
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-tag"></i>
            Bundle Package Price
        </h2>

        @php
            $bundlePrice = $promotion->getBundlePrice();
            $bundleItems = $promotion->getBundleItems() ?? [];
            $totalRegularPrice = 0;

            foreach($bundleItems as $item) {
                $menuItem = \App\Models\MenuItem::find($item['item_id']);
                if($menuItem) {
                    $totalRegularPrice += $menuItem->price * ($item['quantity'] ?? 1);
                }
            }
            $savings = $totalRegularPrice - $bundlePrice;
            $savingsPercentage = $totalRegularPrice > 0 ? ($savings / $totalRegularPrice) * 100 : 0;
        @endphp

        <div class="price-container">
            <div>
                <div class="price-main">
                    RM {{ number_format($bundlePrice, 2) }}
                </div>
                @if($totalRegularPrice > $bundlePrice)
                    <div class="price-regular">
                        Regular Price: RM {{ number_format($totalRegularPrice, 2) }}
                    </div>
                @endif
            </div>

            @if($savings > 0)
                <div class="savings-badge">
                    <div class="savings-label">You Save</div>
                    <div class="savings-amount">RM {{ number_format($savings, 2) }}</div>
                    <div class="savings-percent">({{ number_format($savingsPercentage, 0) }}% off)</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bundle Items --}}
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-box"></i>
            What's Included in This Bundle
        </h2>

        <div class="bundle-items-grid">
            @forelse($bundleItems as $item)
                @php
                    $menuItem = \App\Models\MenuItem::find($item['item_id']);
                @endphp
                @if($menuItem)
                    <div class="bundle-item">
                        @if($menuItem->image)
                            <img src="{{ asset('storage/' . $menuItem->image) }}"
                                 alt="{{ $menuItem->name }}"
                                 class="item-image">
                        @else
                            <div class="item-placeholder">
                                <i class="fas fa-utensils" style="font-size: 32px; color: #9ca3af;"></i>
                            </div>
                        @endif

                        <div class="item-details">
                            <h3 class="item-name">{{ $menuItem->name }}</h3>
                            @if($menuItem->description)
                                <p class="item-description">
                                    {{ Str::limit($menuItem->description, 120) }}
                                </p>
                            @endif
                            <div class="item-tags">
                                <span class="tag tag-price">
                                    <i class="fas fa-tag"></i>
                                    RM {{ number_format($menuItem->price, 2) }}
                                </span>
                                @if(isset($item['quantity']) && $item['quantity'] > 1)
                                    <span class="tag tag-quantity">
                                        <i class="fas fa-times"></i>
                                        {{ $item['quantity'] }}x
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="item-total">
                            <div class="item-total-price">
                                RM {{ number_format($menuItem->price * ($item['quantity'] ?? 1), 2) }}
                            </div>
                            <div class="item-total-label">Subtotal</div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="empty-state">
                    <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No items configured for this bundle.</p>
                </div>
            @endforelse
        </div>

        {{-- Total Summary --}}
        @if(count($bundleItems) > 0)
            <div class="summary-section">
                <div class="summary-row">
                    <span style="color: #6b7280;">Total Regular Price:</span>
                    <span style="font-weight: 600; color: #1f2937;">RM {{ number_format($totalRegularPrice, 2) }}</span>
                </div>
                <div class="summary-row discount">
                    <span>Discount ({{ number_format($savingsPercentage, 0) }}%):</span>
                    <span style="font-weight: 700;">- RM {{ number_format($savings, 2) }}</span>
                </div>
                <div class="summary-row total">
                    <span style="color: #1f2937;">Bundle Price:</span>
                    <span style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        RM {{ number_format($bundlePrice, 2) }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Validity Period --}}
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-calendar-check"></i>
            Valid Period
        </h2>

        <div class="validity-grid">
            <div class="validity-card">
                <div class="validity-label">Start Date</div>
                <div class="validity-value">
                    {{ $promotion->start_date->format('d M Y') }}
                </div>
            </div>
            <div class="validity-card">
                <div class="validity-label">End Date</div>
                <div class="validity-value">
                    {{ $promotion->end_date->format('d M Y') }}
                </div>
            </div>
            <div class="validity-card">
                <div class="validity-label">Status</div>
                <div class="validity-value" style="color: {{ $promotion->is_active ? '#16a34a' : '#dc2626' }};">
                    {{ $promotion->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add to Cart --}}
    <div class="card">
        <button onclick="addBundleToCart()" class="cta-button">
            <i class="fas fa-shopping-cart"></i>
            Add Bundle to Cart - RM {{ number_format($bundlePrice, 2) }}
        </button>
        <p class="cta-info">
            <i class="fas fa-info-circle"></i> All items in the bundle will be added to your cart
        </p>
    </div>
</div>

<script>
// Show styled message modal
function showMessageModal(type, title, message, items = null, onClose = null) {
    const overlay = document.createElement('div');
    overlay.className = 'message-overlay';

    let itemsHTML = '';
    if (items && items.length > 0) {
        itemsHTML = `
            <div class="message-items">
                <div class="message-items-title">Items Added to Cart</div>
                <ul class="message-items-list">
                    ${items.map(item => `<li>${item}</li>`).join('')}
                </ul>
            </div>
        `;
    }

    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    const iconType = type === 'success' ? 'success' : 'error';

    overlay.innerHTML = `
        <div class="message-modal">
            <div class="message-icon ${iconType}">
                <i class="fas ${iconClass}"></i>
            </div>
            <div class="message-title">${title}</div>
            <div class="message-content">${message}</div>
            ${itemsHTML}
            <div class="message-buttons">
                ${type === 'success' 
                    ? '<button class="message-btn secondary" onclick="closeMessageModal()">Stay Here</button><button class="message-btn primary" onclick="goToMenu()">Go to Menu</button>' 
                    : '<button class="message-btn primary" onclick="closeMessageModal()">OK</button>'
                }
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    // Close on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeMessageModal();
            if (onClose) onClose();
        }
    });
}

function closeMessageModal() {
    const overlay = document.querySelector('.message-overlay');
    if (overlay) {
        overlay.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => overlay.remove(), 300);
    }
}

function goToMenu() {
    window.location.href = '{{ route("customer.menu.index") }}';
}

async function addBundleToCart() {
    const promotionId = {{ $promotion->id }};
    const button = event.target;

    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding to cart...';

    try {
        const response = await fetch('{{ route("customer.cart.add-promotion") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                promotion_id: promotionId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Show success modal
            showMessageModal(
                'success',
                'Bundle Added Successfully! 🎉',
                data.message || 'The bundle has been added to your cart.',
                data.items_added || []
            );
        } else {
            // Show error modal
            showMessageModal(
                'error',
                'Unable to Add Bundle',
                data.message || 'Failed to add bundle to cart. Please try again.'
            );
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add Bundle to Cart - RM {{ number_format($bundlePrice, 2) }}';
        }
    } catch (error) {
        console.error('Error adding bundle to cart:', error);
        showMessageModal(
            'error',
            'Error Occurred',
            'An unexpected error occurred. Please try again later.'
        );
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add Bundle to Cart - RM {{ number_format($bundlePrice, 2) }}';
    }
}
</script>
@endsection