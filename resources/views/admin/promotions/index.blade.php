@extends('layouts.admin')

@section('title', 'Promotions Management')
@section('page-title', 'Promotions Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/user-account.css') }}">
<style>
/* Additional styles for toggle button */
.toggle-btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.toggle-btn.active {
    background: #dcfce7;
    color: #16a34a;
}

.toggle-btn.inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.toggle-btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.promo-code-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: #f3f4f6;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.promo-code-badge:hover {
    background: #e5e7eb;
}

.promo-code-badge.hidden .code-text {
    filter: blur(6px);
    user-select: none;
}

.promo-code-badge .reveal-icon {
    font-size: 0.85rem;
    color: #6b7280;
}

.promo-code-badge.hidden .reveal-icon::before {
    content: '\f070'; /* eye-slash */
}

.promo-code-badge:not(.hidden) .reveal-icon::before {
    content: '\f06e'; /* eye */
}

.discount-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.discount-badge.percentage {
    background: #dbeafe;
    color: #2563eb;
}

.discount-badge.fixed {
    background: #fef3c7;
    color: #d97706;
}

.admin-tabs {
    display: flex;
    gap: 8px;
    background: white;
    padding: 12px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    border: 1px solid var(--muted);
}

.admin-tab {
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    background: transparent;
    color: var(--text-2);
    border: none;
    font-size: 14px;
}

.admin-tab.active {
    background: var(--brand);
    color: white;
}

.admin-tab:hover:not(.active) {
    background: var(--bg);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* ===== Empty State ===== */
   .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px 20px;
      text-align: center;
    }
    
    .empty-state-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }
    
    .empty-state-title {
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .empty-state-text {
      color: var(--text-3);
      font-size: 14px;
    }
</style>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Total Promotions</div>
            <div class="admin-card-icon icon-blue"><i class="fas fa-tags"></i></div>
        </div>
        <div class="admin-card-value">{{ $promotions->total() }}</div>
        <div class="admin-card-desc">All promotions</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Active Promotions</div>
            <div class="admin-card-icon icon-green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="admin-card-value">{{ $promotions->where('is_active', true)->count() }}</div>
        <div class="admin-card-desc">Currently active</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Promo Codes</div>
            <div class="admin-card-icon icon-orange"><i class="fas fa-ticket-alt"></i></div>
        </div>
        <div class="admin-card-value">{{ $promotions->where('type', 'promo_code')->count() }}</div>
        <div class="admin-card-desc">Voucher codes</div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Combo Deals</div>
            <div class="admin-card-icon icon-red"><i class="fas fa-layer-group"></i></div>
        </div>
        <div class="admin-card-value">{{ $promotions->where('type', 'combo_deal')->count() }}</div>
        <div class="admin-card-desc">Set meals</div>
    </div>
</div>

{{-- Notifications will be shown via JavaScript --}}

<!-- Type Filter Tabs -->
<div class="admin-tabs">
    <button class="admin-tab {{ !request('type') ? 'active' : '' }}" onclick="filterByType('')">
        <i class="fas fa-th"></i> All Types
    </button>
    <button class="admin-tab {{ request('type') == 'promo_code' ? 'active' : '' }}" onclick="filterByType('promo_code')">
        <i class="fas fa-ticket-alt"></i> Promo Codes
    </button>
    <button class="admin-tab {{ request('type') == 'combo_deal' ? 'active' : '' }}" onclick="filterByType('combo_deal')">
        <i class="fas fa-layer-group"></i> Combo Deals
    </button>
    <button class="admin-tab {{ request('type') == 'item_discount' ? 'active' : '' }}" onclick="filterByType('item_discount')">
        <i class="fas fa-percent"></i> Item Discounts
    </button>
    <button class="admin-tab {{ request('type') == 'buy_x_free_y' ? 'active' : '' }}" onclick="filterByType('buy_x_free_y')">
        <i class="fas fa-gift"></i> Buy X Free Y
    </button>
    <button class="admin-tab {{ request('type') == 'bundle' ? 'active' : '' }}" onclick="filterByType('bundle')">
        <i class="fas fa-box-open"></i> Bundles
    </button>
    <button class="admin-tab {{ request('type') == 'seasonal' ? 'active' : '' }}" onclick="filterByType('seasonal')">
        <i class="fas fa-calendar-alt"></i> Seasonal
    </button>
</div>

<!-- Promotions Section -->
<div id="promotions-tab" class="tab-content active">
    <div class="admin-section">
        <div class="section-header">
            <h2 class="section-title">
                @if(request('type'))
                    {{ ucwords(str_replace('_', ' ', request('type'))) }} Promotions
                @else
                    All Promotions
                @endif
            </h2>
        </div>

        <div class="search-filter">
            <a href="{{ route('admin.promotions.create') }}" class="admin-btn btn-primary">
                <div class="admin-nav-icon"><i class="fas fa-plus"></i></div>
                Create Promotion
            </a>
        </div>

        @if($promotions->count() > 0)
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Promotion Name</th>
                            <th class="cell-center">Type</th>
                            <th>Promo Code</th>
                            <th class="cell-center">Discount</th>
                            <th class="cell-center">Min. Order</th>
                            <th>Valid Period</th>
                            <th class="cell-center">Status</th>
                            <th class="cell-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promo)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: var(--text);">{{ $promo->name }}</div>
                                @if($promo->description)
                                    <div style="font-size: 12px; color: var(--text-3); margin-top: 2px;">{{ Str::limit($promo->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="cell-center">
                                @php
                                    $typeConfig = [
                                        'promo_code' => ['icon' => 'ticket-alt', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'label' => 'Promo Code'],
                                        'combo_deal' => ['icon' => 'layer-group', 'color' => '#8b5cf6', 'bg' => '#ede9fe', 'label' => 'Combo'],
                                        'item_discount' => ['icon' => 'percent', 'color' => '#10b981', 'bg' => '#d1fae5', 'label' => 'Discount'],
                                        'buy_x_free_y' => ['icon' => 'gift', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => 'BOGO'],
                                        'bundle' => ['icon' => 'box-open', 'color' => '#ef4444', 'bg' => '#fee2e2', 'label' => 'Bundle'],
                                        'seasonal' => ['icon' => 'calendar-alt', 'color' => '#ec4899', 'bg' => '#fce7f3', 'label' => 'Seasonal'],
                                    ];
                                    $config = $typeConfig[$promo->type] ?? ['icon' => 'tag', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'label' => 'Other'];
                                @endphp
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                                    <i class="fas fa-{{ $config['icon'] }}"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($promo->promo_code)
                                    <span class="promo-code-badge hidden"
                                          data-code="{{ $promo->promo_code }}"
                                          onclick="togglePromoCode(this)"
                                          title="Click to reveal">
                                        <span class="code-text">*****</span>
                                        <i class="fas reveal-icon"></i>
                                    </span>
                                @else
                                    <span style="color: var(--text-3); font-style: italic;">—</span>
                                @endif
                            </td>
                            <td class="cell-center">
                                <span class="discount-badge {{ $promo->discount_type === 'percentage' ? 'percentage' : 'fixed' }}">
                                    @if($promo->discount_type === 'percentage')
                                        {{ number_format($promo->discount_value, 0) }}%
                                    @else
                                        RM {{ number_format($promo->discount_value, 2) }}
                                    @endif
                                </span>
                            </td>
                            <td class="cell-center">
                                @if($promo->minimum_order_value)
                                    RM {{ number_format($promo->minimum_order_value, 2) }}
                                @else
                                    <span style="color: var(--text-3); font-style: italic;">No min</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 13px;">{{ $promo->start_date->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: var(--text-3);">to {{ $promo->end_date->format('M d, Y') }}</div>
                            </td>
                            <td class="cell-center">
                                <span class="status {{ $promo->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $promo->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="cell-center">
                                <div class="table-actions">
                                    <a href="{{ route('admin.promotions.stats', $promo->id) }}"
                                       class="action-btn"
                                       style="background: #dbeafe; color: #2563eb;"
                                       title="View Statistics">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('admin.promotions.show', $promo->id) }}"
                                       class="action-btn view-btn"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="toggle-btn {{ $promo->is_active ? 'active' : 'inactive' }}"
                                            onclick="toggleStatus({{ $promo->id }}, 'promotion')"
                                            title="Toggle status">
                                        <i class="fas fa-toggle-{{ $promo->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                    <a href="{{ route('admin.promotions.edit', $promo->id) }}"
                                       class="action-btn edit-btn"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.promotions.destroy', $promo->id) }}"
                                          method="POST"
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this promotion?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $promotions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-tags"></i></div>
                <div class="empty-state-title">No Promotions Yet</div>
                <div class="empty-state-text">Start creating promotions to attract more customers!</div>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background-color: #10b981;' : 'background-color: #ef4444;'}
    `;

    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Toggle promo code visibility
function togglePromoCode(element) {
    const codeText = element.querySelector('.code-text');
    const fullCode = element.dataset.code;
    const isHidden = element.classList.contains('hidden');

    if (isHidden) {
        // Reveal the full code
        codeText.textContent = fullCode;
    } else {
        // Hide the code and show placeholder
        codeText.textContent = '*****';
    }
    element.classList.toggle('hidden');
}

// Filter promotions by type
function filterByType(type) {
    const url = new URL(window.location.href);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location.href = url.toString();
}

// Toggle status
function toggleStatus(id, type) {
    const url = `/admin/promotions/${id}/toggle-status`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Show notifications from session
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    @if(session('message'))
        showNotification('{{ session('message') }}', 'success');
    @endif
});
</script>
@endsection
