/**
 * Modern Toast Notification System
 * Usage:
 *   showToast('Success!', 'Your changes have been saved', 'success')
 *   showToast('Error!', 'Something went wrong', 'error')
 *   showToast('Warning!', 'Please check your input', 'warning')
 *   showToast('Info', 'Here is some information', 'info')
 */

// Create toast container if it doesn't exist
function getToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

// Show toast notification
function showToast(title, message = '', type = 'info', duration = 4000) {
    const container = getToastContainer();

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    // Icon based on type
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    const icon = icons[type] || icons.info;

    // Build toast HTML
    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            ${message ? `<div class="toast-message">${message}</div>` : ''}
        </div>
        <button class="toast-close" aria-label="Close">×</button>
        ${duration > 0 ? `<div class="toast-progress" style="animation-duration: ${duration}ms;"></div>` : ''}
    `;

    // Add to container
    container.appendChild(toast);

    // Close button handler
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
        removeToast(toast);
    });

    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            removeToast(toast);
        }, duration);
    }

    return toast;
}

// Remove toast with animation
function removeToast(toast) {
    toast.classList.add('hiding');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Convenience methods
window.Toast = {
    success: (title, message = '', duration = 4000) => showToast(title, message, 'success', duration),
    error: (title, message = '', duration = 5000) => showToast(title, message, 'error', duration),
    warning: (title, message = '', duration = 4000) => showToast(title, message, 'warning', duration),
    info: (title, message = '', duration = 4000) => showToast(title, message, 'info', duration)
};

// Also expose showToast globally
window.showToast = showToast;
