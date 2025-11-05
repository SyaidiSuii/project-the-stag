/**
 * Cart Voucher Management System
 * Handles voucher selection, application, and discount calculation
 */

// Global voucher state
let appliedVoucher = null;
let availableVouchers = [];

function getCsrfToken() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken || !csrfToken.content) {
        console.error("CSRF token not found in meta tag");
        return null;
    }
    return csrfToken.content;
}

// Show success overlay notification
function showSuccessOverlay(message, duration = 2000) {
    // Create overlay container
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease;
    `;

    // Create success card
    const card = document.createElement('div');
    card.style.cssText = `
        background: white;
        border-radius: 20px;
        padding: 40px;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease;
    `;

    card.innerHTML = `
        <div style="
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease;
        ">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h3 style="
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        ">Success!</h3>
        <p style="
            font-size: 16px;
            color: #6b7280;
            margin: 0;
        ">${message}</p>
    `;

    overlay.appendChild(card);
    document.body.appendChild(overlay);

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Auto close after duration
    setTimeout(() => {
        overlay.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            overlay.remove();
            style.remove();
        }, 300);
    }, duration);
}

// Show error overlay notification
function showErrorOverlay(message, duration = 3000) {
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.3s ease;
    `;

    const card = document.createElement('div');
    card.style.cssText = `
        background: white;
        border-radius: 20px;
        padding: 40px;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease;
    `;

    card.innerHTML = `
        <div style="
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease;
        ">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>
        <h3 style="
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        ">Oops!</h3>
        <p style="
            font-size: 16px;
            color: #6b7280;
            margin: 0;
        ">${message}</p>
    `;

    overlay.appendChild(card);
    document.body.appendChild(overlay);

    // Auto close
    setTimeout(() => {
        overlay.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => overlay.remove(), 300);
    }, duration);
}

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
            if (data.success && data.rewards && data.rewards.length > 0) {
                availableVouchers = data.rewards;
                renderVoucherList(data.rewards);
            } else {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                        <i class="fas fa-ticket-alt" style="font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.3;"></i>
                        <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px;">No Rewards Available</p>
                        <p style="font-size: 0.9rem;">Redeem rewards from the Rewards page!</p>
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
            if (isReward) {
                applyRewardToCartFromModal(voucher);
            } else {
                applyVoucherToCart(voucher.id);
            }
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
            } else {
                showSuccessOverlay(data.message);
            }
        } else {
            // Show error
            if (typeof showMessage === 'function') {
                showMessage(data.message, 'error');
            } else {
                showErrorOverlay(data.message);
            }
            // Restore original content
            document.getElementById('voucherListContainer').innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error applying voucher:', error);
        if (typeof showMessage === 'function') {
            showMessage('Failed to apply voucher. Please try again.', 'error');
        } else {
            showErrorOverlay('Failed to apply voucher. Please try again.');
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

window.applyRewardToCartFromModal = function(rewardData) {
    const redemptionId = rewardData.id.replace("reward_", "");
    if (rewardData.menu_item_id) {
        processProductTypeReward(rewardData, redemptionId);
    } else if (rewardData.discount_type && rewardData.discount_type !== "free_item") {
        processVoucherTypeReward(rewardData, redemptionId);
    }
};

async function processVoucherTypeReward(rewardData, redemptionId) {
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch("/customer/rewards/apply-voucher", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
            body: JSON.stringify({ customer_reward_id: redemptionId })
        });
        const result = await response.json();
        if (!result.success) throw new Error(result.message);
        
        const cartResponse = await fetch("/customer/cart/apply-voucher", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
            body: JSON.stringify({ voucher_id: result.voucher_id })
        });
        const cartResult = await cartResponse.json();
        if (!cartResult.success) throw new Error(cartResult.message);

        // Show success overlay
        showSuccessOverlay("Voucher applied successfully!");

        // Close modals and reload after animation
        setTimeout(() => {
            location.reload();
        }, 2000);
    } catch (error) {
        console.error("Error:", error);
        showErrorOverlay(error.message || "Failed to apply voucher");
    }
}

function processProductTypeReward(rewardData, redemptionId) {
    fetch("/customer/rewards/mark-pending", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrfToken() },
        body: JSON.stringify({ redemption_id: redemptionId })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            fetch("/customer/cart/add", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrfToken() },
                body: JSON.stringify({
                    menu_item_id: rewardData.menu_item_id,
                    quantity: 1,
                    is_free_item: true,
                    redemption_id: redemptionId,
                    free_item_title: rewardData.name
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccessOverlay(rewardData.name + " added to cart!");
                    setTimeout(() => location.reload(), 2000);
                }
            });
        }
    });
}

window.loadAvailableVouchers = () => location.reload();
console.log("Functions restored");