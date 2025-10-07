/**
 * Modern Confirmation Modal System
 * Usage:
 *   showConfirm('Delete Item?', 'This action cannot be undone', 'danger')
 *     .then(confirmed => {
 *       if (confirmed) {
 *         // User clicked confirm
 *       }
 *     });
 */

function showConfirm(title, message = '', type = 'danger', confirmText = 'Confirm', cancelText = 'Cancel') {
    return new Promise((resolve) => {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'confirm-modal-overlay';

        // Icon based on type
        const icons = {
            danger: '⚠',
            warning: '⚠',
            info: 'ℹ'
        };

        const icon = icons[type] || icons.danger;

        // Build modal HTML
        overlay.innerHTML = `
            <div class="confirm-modal">
                <div class="confirm-modal-header">
                    <div class="confirm-modal-icon ${type}">
                        ${icon}
                    </div>
                    <div class="confirm-modal-text">
                        <h3 class="confirm-modal-title">${title}</h3>
                        <p class="confirm-modal-message">${message}</p>
                    </div>
                </div>
                <div class="confirm-modal-footer">
                    <button class="confirm-modal-btn confirm-modal-btn-cancel">${cancelText}</button>
                    <button class="confirm-modal-btn confirm-modal-btn-confirm ${type}">${confirmText}</button>
                </div>
            </div>
        `;

        // Add to body
        document.body.appendChild(overlay);

        const modal = overlay.querySelector('.confirm-modal');
        const cancelBtn = overlay.querySelector('.confirm-modal-btn-cancel');
        const confirmBtn = overlay.querySelector('.confirm-modal-btn-confirm');

        // Close function
        const closeModal = (confirmed) => {
            overlay.classList.add('hiding');
            modal.classList.add('hiding');
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
                resolve(confirmed);
            }, 200);
        };

        // Event listeners
        cancelBtn.addEventListener('click', () => closeModal(false));
        confirmBtn.addEventListener('click', () => closeModal(true));

        // Click outside to cancel
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal(false);
            }
        });

        // ESC key to cancel
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
    });
}

// Expose globally
window.showConfirm = showConfirm;

// Convenience methods
window.Confirm = {
    delete: (title = 'Delete Item?', message = 'This action cannot be undone') =>
        showConfirm(title, message, 'danger', 'Delete', 'Cancel'),

    clear: (title = 'Clear All?', message = 'This will remove all items') =>
        showConfirm(title, message, 'warning', 'Clear', 'Cancel'),

    proceed: (title = 'Continue?', message = '') =>
        showConfirm(title, message, 'info', 'Continue', 'Cancel')
};
