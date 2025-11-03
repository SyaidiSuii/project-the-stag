/**
 * Cart Voucher Management System
 * Handles voucher selection, application, and discount calculation
 */

// Global voucher state
let appliedVoucher = null;
let availableVouchers = [];

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeVoucherSystem();
});

function initializeVoucherSystem() {
    // Button event listeners
    const selectVoucherBtn = document.getElementById('select-voucher-btn');
    const closeVoucherModal = document.getElementById('closeVoucherModal');
    const removeVoucherBtn = document.getElementById('remove-voucher-btn');

    if (selectVoucherBtn) {
        selectVoucherBtn.addEventListener('click', openVoucherModal);
    }

    if (closeVoucherModal) {
        closeVoucherModal.addEventListener('click', () => {
            document.getElementById('voucherSelectionModal').style.display = 'none';
        });
    }

    if (removeVoucherBtn) {
        removeVoucherBtn.addEventListener('click', removeVoucher);
    }

    // Load applied voucher from session if exists
    loadAppliedVoucher();
}

function openVoucherModal() {
    const modal = document.getElementById('voucherSelectionModal');
    modal.style.display = 'flex';

    // Load available vouchers
    fetchAvailableVouchers();
}

function fetchAvailableVouchers() {
    const container = document.getElementById('voucherListContainer');
    container.innerHTML = '<div style="text-align: center; padding: 40px 20px; color: #9ca3af;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i><p>Loading vouchers...</p></div>';

    fetch('/customer/cart/available-vouchers')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.vouchers && data.vouchers.length > 0) {
                availableVouchers = data.vouchers;
                renderVoucherList(data.vouchers);
            } else {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                        <i class="fas fa-ticket-alt" style="font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.3;"></i>
                        <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px;">No Vouchers Available</p>
                        <p style="font-size: 0.9rem;">Collect vouchers from the Rewards page!</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching vouchers:', error);
            container.innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #ef4444;">
                    <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                    <p>Failed to load vouchers</p>
                </div>
            `;
        });
}

function renderVoucherList(vouchers) {
    const container = document.getElementById('voucherListContainer');
    container.innerHTML = '';

    vouchers.forEach(voucher => {
        const voucherCard = document.createElement('div');

        // Different colors for vouchers from collections vs rewards
        const isReward = voucher.source === 'reward';
        const bgGradient = isReward
            ? 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)'
            : 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)';
        const borderColor = isReward ? '#3b82f6' : '#f59e0b';
        const textColor = isReward ? '#1e40af' : '#92400e';
        const descColor = isReward ? '#2563eb' : '#d97706';
        const shadowColor = isReward ? 'rgba(59, 130, 246, 0.3)' : 'rgba(245, 158, 11, 0.3)';

        voucherCard.style.cssText = `background: ${bgGradient}; border: 2px solid ${borderColor}; border-radius: 12px; padding: 16px; margin-bottom: 12px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;`;

        voucherCard.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <div style="font-size: 1rem; font-weight: 700; color: ${textColor};">
                            ${voucher.name}
                        </div>
                        ${isReward ? `<span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 600;">REWARD</span>` : ''}
                    </div>
                    <div style="font-size: 0.85rem; color: ${descColor}; margin-bottom: 8px;">
                        ${voucher.description}
                    </div>
                    ${voucher.minimum_spend > 0 ? `
                        <div style="font-size: 0.75rem; color: ${textColor};">
                            <i class="fas fa-info-circle"></i> Min. spend: RM${parseFloat(voucher.minimum_spend).toFixed(2)}
                        </div>
                    ` : ''}
                    ${voucher.expiry_date ? `
                        <div style="font-size: 0.75rem; color: ${textColor};">
                            <i class="fas fa-clock"></i> Valid until: ${voucher.expiry_date}
                        </div>
                    ` : ''}
                </div>
                <button class="apply-voucher-btn" data-voucher-id="${voucher.id}" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                    Apply
                </button>
            </div>
        `;

        // Add hover effect with source-specific shadow
        voucherCard.addEventListener('mouseenter', () => {
            voucherCard.style.transform = 'translateY(-2px)';
            voucherCard.style.boxShadow = `0 4px 12px ${shadowColor}`;
        });
        voucherCard.addEventListener('mouseleave', () => {
            voucherCard.style.transform = 'translateY(0)';
            voucherCard.style.boxShadow = 'none';
        });

        // Apply button click
        const applyBtn = voucherCard.querySelector('.apply-voucher-btn');
        applyBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            applyVoucherToCart(voucher.id);
        });

        container.appendChild(voucherCard);
    });
}

function applyVoucherToCart(voucherId) {
    // Show loading state
    const modal = document.getElementById('voucherSelectionModal');
    const originalContent = document.getElementById('voucherListContainer').innerHTML;
    document.getElementById('voucherListContainer').innerHTML = '<div style="text-align: center; padding: 40px 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #f59e0b;"></i><p style="margin-top: 12px;">Applying voucher...</p></div>';

    fetch('/customer/cart/apply-voucher', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ voucher_id: voucherId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store applied voucher
            appliedVoucher = data.voucher;

            // Update UI
            updateVoucherUI(data.voucher);
            updateCartTotal(data.new_total, data.voucher.discount);

            // Close modal
            modal.style.display = 'none';

            // Show success message
            if (typeof showMessage === 'function') {
                showMessage(data.message, 'success');
            } else if (typeof Toast !== 'undefined') {
                Toast.success('Voucher Applied', data.message);
            } else {
                alert(data.message);
            }
        } else {
            // Show error
            if (typeof showMessage === 'function') {
                showMessage(data.message, 'error');
            } else if (typeof Toast !== 'undefined') {
                Toast.error('Voucher Error', data.message);
            } else {
                alert(data.message);
            }
            // Restore original content
            document.getElementById('voucherListContainer').innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error applying voucher:', error);
        if (typeof showMessage === 'function') {
            showMessage('Failed to apply voucher. Please try again.', 'error');
        } else if (typeof Toast !== 'undefined') {
            Toast.error('Voucher Error', 'Failed to apply voucher. Please try again.');
        } else {
            alert('Failed to apply voucher');
        }
        document.getElementById('voucherListContainer').innerHTML = originalContent;
    });
}

function removeVoucher() {
    fetch('/customer/cart/remove-voucher', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            appliedVoucher = null;
            updateVoucherUI(null);

            // Recalculate cart total without voucher
            if (typeof updateCartDisplay === 'function') {
                updateCartDisplay();
            }

            if (typeof showMessage === 'function') {
                showMessage(data.message, 'success');
            }
        }
    })
    .catch(error => {
        console.error('Error removing voucher:', error);
    });
}

function updateVoucherUI(voucher) {
    const appliedContainer = document.getElementById('voucher-applied-container');
    const noVouchersMessage = document.getElementById('no-vouchers-message');
    const voucherDiscountRow = document.getElementById('voucher-discount-row');
    const voucherDiscountAmount = document.getElementById('voucher-discount-amount');

    if (voucher && voucher.discount !== undefined) {
        // Show applied voucher
        document.getElementById('voucher-name').textContent = voucher.name || 'Voucher';
        document.getElementById('voucher-desc').textContent = voucher.description || '';
        appliedContainer.style.display = 'block';
        noVouchersMessage.style.display = 'none';

        // Show discount in cart total
        const discount = parseFloat(voucher.discount) || 0;
        voucherDiscountRow.style.display = 'flex';
        voucherDiscountAmount.textContent = `-RM ${discount.toFixed(2)}`;
    } else {
        // Hide applied voucher
        appliedContainer.style.display = 'none';
        noVouchersMessage.style.display = 'block';

        // Hide discount row
        voucherDiscountRow.style.display = 'none';
    }
}

function updateCartTotal(newTotal, discount) {
    const totalElement = document.getElementById('total-amount');
    if (totalElement && newTotal !== undefined) {
        const total = parseFloat(newTotal) || 0;
        totalElement.textContent = `RM ${total.toFixed(2)}`;
    }

    // Update voucher discount display
    const voucherDiscountRow = document.getElementById('voucher-discount-row');
    const voucherDiscountAmount = document.getElementById('voucher-discount-amount');

    if (voucherDiscountRow) voucherDiscountRow.style.display = 'flex';
    if (voucherDiscountAmount && discount !== undefined) {
        const discountAmount = parseFloat(discount) || 0;
        voucherDiscountAmount.textContent = `-RM ${discountAmount.toFixed(2)}`;
    }

    // Trigger cart display update to recalculate with voucher
    if (typeof updateCartDisplay === 'function') {
        updateCartDisplay();
    }
}

function loadAppliedVoucher() {
    // Fetch applied voucher from backend session
    fetch('/customer/cart/get-applied-voucher', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.voucher) {
            appliedVoucher = data.voucher;
            updateVoucherUI(data.voucher);

            // Trigger cart recalculation
            if (typeof updateCartDisplay === 'function') {
                updateCartDisplay();
            }
        }
    })
    .catch(error => {
        console.error('Error loading applied voucher:', error);
    });
}

// Export functions for use in other scripts
window.applyVoucherToCart = applyVoucherToCart;
window.removeVoucher = removeVoucher;
window.getAppliedVoucher = () => appliedVoucher;
