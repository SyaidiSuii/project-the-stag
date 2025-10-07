// Customer Orders JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Cancel Booking Functionality
    const cancelBookingButtons = document.querySelectorAll('.cancel-booking-btn[data-reservation-id]');
    
    cancelBookingButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const reservationId = this.dataset.reservationId;
            const confirmationCode = this.closest('.booking-card').querySelector('h3').textContent.trim();

            // Show confirmation dialog
            const confirmed = await showConfirm(
                'Cancel Booking?',
                `Are you sure you want to cancel this booking?\n\n${confirmationCode}\n\nThis action cannot be undone.`,
                'danger',
                'Cancel Booking',
                'Keep Booking'
            );

            if (confirmed) {
                cancelBooking(reservationId, this);
            }
        });
    });

    // Cancel Order Functionality (existing)
    const cancelOrderButtons = document.querySelectorAll('.cancel-booking-btn[data-order-id]');
    
    cancelOrderButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const orderId = this.dataset.orderId;
            const confirmationCode = this.closest('.order-card').querySelector('h3').textContent.trim();

            // Show confirmation dialog
            const confirmed = await showConfirm(
                'Cancel Order?',
                `Are you sure you want to cancel this order?\n\n${confirmationCode}\n\nThis action cannot be undone.`,
                'danger',
                'Cancel Order',
                'Keep Order'
            );

            if (confirmed) {
                cancelOrder(orderId, this);
            }
        });
    });

    /**
     * Cancel a booking reservation
     */
    async function cancelBooking(reservationId, buttonElement) {
        const originalText = buttonElement.innerHTML;
        
        try {
            // Show loading state
            buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
            buttonElement.disabled = true;
            
            const response = await fetch(`/customer/orders/booking/${reservationId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success message
                Toast.success('Booking Cancelled', data.message);

                // Update the booking card status and remove cancel button
                const bookingCard = buttonElement.closest('.booking-card');
                const statusElement = bookingCard.querySelector('.order-status');
                statusElement.textContent = 'Cancelled';
                statusElement.className = 'order-status status-cancelled';
                
                // Remove action buttons
                const actionsContainer = bookingCard.querySelector('.order-actions');
                actionsContainer.innerHTML = '<span class="text-muted">Booking cancelled</span>';
                
                // Optionally reload page after delay
                setTimeout(() => {
                    location.reload();
                }, 2000);
                
            } else {
                throw new Error(data.error || 'Failed to cancel booking');
            }
        } catch (error) {
            console.error('Cancel booking error:', error);
            Toast.error('Cancellation Failed', error.message || 'Failed to cancel booking. Please try again.');
            
            // Restore button state
            buttonElement.innerHTML = originalText;
            buttonElement.disabled = false;
        }
    }

    /**
     * Cancel an order
     */
    async function cancelOrder(orderId, buttonElement) {
        const originalText = buttonElement.innerHTML;
        
        try {
            // Show loading state
            buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
            buttonElement.disabled = true;
            
            const response = await fetch(`/customer/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success message
                Toast.success('Order Cancelled', data.message);

                // Update the order card status and remove cancel button
                const orderCard = buttonElement.closest('.order-card');
                const statusElement = orderCard.querySelector('.order-status');
                statusElement.textContent = 'Cancelled';
                statusElement.className = 'order-status status-cancelled';
                
                // Remove cancel button and add reorder button
                buttonElement.remove();
                const actionsContainer = orderCard.querySelector('.order-actions');
                const reorderButton = document.createElement('button');
                reorderButton.className = 'btn btn-primary reorder-btn';
                reorderButton.dataset.orderId = orderId;
                reorderButton.innerHTML = 'Reorder';
                actionsContainer.appendChild(reorderButton);
                
                // Optionally reload page after delay
                setTimeout(() => {
                    location.reload();
                }, 2000);
                
            } else {
                throw new Error(data.error || 'Failed to cancel order');
            }
        } catch (error) {
            console.error('Cancel order error:', error);
            Toast.error('Cancellation Failed', error.message || 'Failed to cancel order. Please try again.');

            // Restore button state
            buttonElement.innerHTML = originalText;
            buttonElement.disabled = false;
        }
    }

    /**
     * Show toast notification (deprecated - now using global Toast system)
     */
    function showToast(type, message) {
        // Create toast element if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            `;
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
            color: ${type === 'success' ? '#155724' : '#721c24'};
            border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 400px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        
        const icon = type === 'success' ? '' : 'L';
        toast.innerHTML = `${icon} ${message}`;
        
        toastContainer.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
});