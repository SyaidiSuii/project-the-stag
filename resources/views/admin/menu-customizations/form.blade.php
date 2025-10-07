@extends('layouts.admin')

@section('title', $menuCustomization->id ? 'Edit Menu Customization' : 'Create Menu Customization')
@section('page-title', $menuCustomization->id ? 'Edit Menu Customization' : 'Create Menu Customization')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu-customizations.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/menu-managements.css') }}">
@endsection

@section('content')
<div class="admin-section">
    <div class="section-header">
        <h2 class="section-title">
            @if($menuCustomization->id)
            Edit Menu Customization - #{{ $menuCustomization->id }}
            @else
            Create New Menu Customization
            @endif
        </h2>
        <a href="{{ route('admin.menu-customizations.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Back to Customizations
        </a>
    </div>

    @if($menuCustomization->id)
    <form method="POST" action="{{ route('admin.menu-customizations.update', $menuCustomization->id) }}" class="menu-item-form">
        @method('PUT')
        @else
        <form method="POST" action="{{ route('admin.menu-customizations.store') }}" class="menu-item-form">
            @endif
            @csrf

            <!-- Basic Customization Details -->
            <div class="form-section">
                <h3 class="section-subtitle">Customization Details</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="order_item_id" class="form-label">Order Item *</label>
                        <select
                            id="order_item_id"
                            name="order_item_id"
                            class="form-control @error('order_item_id') is-invalid @enderror"
                            required>
                            <option value="">Select Order Item</option>
                            @foreach($orderItems as $orderItem)
                            <option value="{{ $orderItem->id }}"
                                {{ old('order_item_id', $menuCustomization->order_item_id) == $orderItem->id ? 'selected' : '' }}>
                                Order #{{ $orderItem->order->id }} - {{ $orderItem->menuItem->name }}
                                (Qty: {{ $orderItem->quantity }}, Price: RM {{ number_format($orderItem->unit_price, 2) }})
                            </option>
                            @endforeach
                        </select>
                        @if($errors->get('order_item_id'))
                        <div class="form-error">{{ implode(', ', $errors->get('order_item_id')) }}</div>
                        @endif
                        <div class="form-hint">Select the order item that this customization applies to</div>
                    </div>

                    <div class="form-group">
                        <label for="customization_type" class="form-label">Customization Type *</label>
                        <input
                            type="text"
                            id="customization_type"
                            name="customization_type"
                            class="form-control @error('customization_type') is-invalid @enderror"
                            value="{{ old('customization_type', $menuCustomization->customization_type) }}"
                            placeholder="e.g. Extra Sauce, No Onions, Extra Cheese"
                            maxlength="100"
                            required>
                        @if($errors->get('customization_type'))
                        <div class="form-error">{{ implode(', ', $errors->get('customization_type')) }}</div>
                        @endif
                        <div class="form-hint">Type of customization (max 100 characters)</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="customization_value" class="form-label">Customization Value *</label>
                        <textarea
                            id="customization_value"
                            name="customization_value"
                            class="form-control @error('customization_value') is-invalid @enderror"
                            rows="3"
                            placeholder="Describe the specific customization details..."
                            maxlength="255"
                            required>{{ old('customization_value', $menuCustomization->customization_value) }}</textarea>
                        @if($errors->get('customization_value'))
                        <div class="form-error">{{ implode(', ', $errors->get('customization_value')) }}</div>
                        @endif
                        <div class="form-hint">Detailed description of the customization (max 255 characters)</div>
                    </div>

                    <div class="form-group">
                        <label for="additional_price" class="form-label">Additional Price (RM) *</label>
                        <input
                            type="number"
                            id="additional_price"
                            name="additional_price"
                            class="form-control @error('additional_price') is-invalid @enderror"
                            value="{{ old('additional_price', $menuCustomization->additional_price) }}"
                            step="0.01"
                            min="0"
                            max="999999.99"
                            placeholder="0.00"
                            required>
                        @if($errors->get('additional_price'))
                        <div class="form-error">{{ implode(', ', $errors->get('additional_price')) }}</div>
                        @endif
                        <div class="form-hint">Extra cost for this customization (enter 0 if no additional cost)</div>
                    </div>
                </div>
            </div>

            <!-- Current Customization Information (for edit) -->
            @if($menuCustomization->id)
            <div class="form-section">
                <h3 class="section-subtitle">Current Customization Information</h3>
                <div class="order-info-grid">
                    <div class="info-item">
                        <span class="info-label">Customization ID:</span>
                        <span class="info-value">#{{ $menuCustomization->id }}</span>
                    </div>
                    @if($menuCustomization->orderItem)
                    <div class="info-item">
                        <span class="info-label">Order Item:</span>
                        <span class="info-value">{{ $menuCustomization->orderItem->menuItem->name ?? 'N/A' }}</span>
                    </div>
                    @if($menuCustomization->orderItem->order)
                    <div class="info-item">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#{{ $menuCustomization->orderItem->order->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Customer:</span>
                        <span class="info-value">{{ $menuCustomization->orderItem->order->user->name ?? 'Unknown' }}</span>
                    </div>
                    @endif
                    @endif
                    <div class="info-item">
                        <span class="info-label">Created:</span>
                        <span class="info-value">{{ $menuCustomization->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($menuCustomization->updated_at && $menuCustomization->updated_at != $menuCustomization->created_at)
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span>
                        <span class="info-value">{{ $menuCustomization->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    {{ $menuCustomization->id ? 'Update Customization' : 'Save Customization' }}
                </button>
                <a href="{{ route('admin.menu-customizations.index') }}" class="btn-cancel">
                    Cancel
                </a>
                @if($menuCustomization->id)
                <a href="{{ route('admin.menu-customizations.show', $menuCustomization->id) }}" class="btn-view">
                    <i class="fas fa-eye"></i>
                    View Details
                </a>
                @endif
            </div>
        </form>
</div>
@endsection

@section('scripts')
<script>
    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.textContent = message;
        notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Character counter for inputs with maxlength
    function setupCharacterCounters() {
        const customizationType = document.getElementById('customization_type');
        const customizationValue = document.getElementById('customization_value');

        function addCharacterCounter(element, maxLength) {
            const counter = document.createElement('div');
            counter.className = 'character-counter';
            counter.style.cssText = 'font-size: 12px; color: #6b7280; text-align: right; margin-top: 4px;';

            function updateCounter() {
                const currentLength = element.value.length;
                counter.textContent = `${currentLength}/${maxLength}`;
                counter.style.color = currentLength > maxLength * 0.9 ? '#ef4444' : '#6b7280';
            }

            element.addEventListener('input', updateCounter);
            element.parentNode.appendChild(counter);
            updateCounter();
        }

        if (customizationType) addCharacterCounter(customizationType, 100);
        if (customizationValue) addCharacterCounter(customizationValue, 255);
    }

    // Real-time price validation
    function setupPriceValidation() {
        const priceInput = document.getElementById('additional_price');

        if (priceInput) {
            priceInput.addEventListener('input', function() {
                const value = parseFloat(this.value);

                if (isNaN(value) || value < 0) {
                    this.setCustomValidity('Price must be a positive number');
                } else if (value > 999999.99) {
                    this.setCustomValidity('Price cannot exceed RM 999,999.99');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Format price display
            priceInput.addEventListener('blur', function() {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            });
        }
    }

    // Order item selection enhancement
    function setupOrderItemSelection() {
        const orderItemSelect = document.getElementById('order_item_id');

        if (orderItemSelect) {
            orderItemSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (selectedOption.value) {
                    // Could add additional logic here if needed
                    // e.g., auto-populate some fields based on selected order item
                    console.log('Selected order item:', selectedOption.text);
                }
            });
        }
    }

    // Form submission handling
    function setupFormSubmission() {
        const form = document.querySelector('.menu-item-form');

        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-save');

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                submitBtn.disabled = true;

                // Validate required fields
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> {{ $menuCustomization->id ? "Update Customization" : "Save Customization" }}';
                    submitBtn.disabled = false;
                    showNotification('Please fill in all required fields', 'error');
                }
            });
        }
    }

    // Initialize all functionality
    document.addEventListener('DOMContentLoaded', function() {
        setupCharacterCounters();
        setupPriceValidation();
        setupOrderItemSelection();
        setupFormSubmission();

        // Show session messages
        @if(session('message'))
        showNotification('{{ session('
            message ') }}', 'success');
        @endif

        @if(session('success'))
        showNotification('{{ session('
            success ') }}', 'success');
        @endif

        @if(session('error'))
        showNotification('{{ session('
            error ') }}', 'error');
        @endif

        // Clear validation errors on input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.form-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection