/**
 * Real-time Analytics Dashboard
 * WebSocket-only real-time updates using Laravel Reverb
 */

class RealtimeAnalytics {
    constructor() {
        this.isConnected = false;
        this.charts = {};

        this.init();
    }

    /**
     * Initialize the real-time analytics system
     */
    init() {
        console.log('🚀 Initializing Real-time Analytics (WebSocket Only)...');

        // Setup WebSocket connection
        this.setupWebSocket();

        // Setup manual refresh button
        this.setupRefreshButton();

        // Setup chart update handlers
        this.setupCharts();

        console.log('✅ Real-time Analytics initialized');
    }

    /**
     * Setup WebSocket connection with Laravel Echo
     */
    setupWebSocket() {
        if (typeof window.Echo === 'undefined') {
            console.error('❌ Laravel Echo not loaded! WebSocket unavailable.');
            this.showConnectionStatus('disconnected');
            return;
        }

        try {
            // Listen to the analytics-updates channel
            window.Echo.channel('analytics-updates')
                .listen('.order.paid', (data) => {
                    console.log('📦 Order Paid Event:', data);
                    this.handleOrderPaid(data);
                })
                .listen('.promotion.used', (data) => {
                    console.log('🎁 Promotion Used Event:', data);
                    this.handlePromotionUsed(data);
                })
                .listen('.reward.redeemed', (data) => {
                    console.log('⭐ Reward Redeemed Event:', data);
                    this.handleRewardRedeemed(data);
                })
                .listen('.booking.created', (data) => {
                    console.log('📅 Booking Created Event:', data);
                    this.handleBookingCreated(data);
                });

            this.isConnected = true;
            console.log('✅ WebSocket connected to analytics-updates channel');
            this.showConnectionStatus('connected');
        } catch (error) {
            console.error('❌ WebSocket connection failed:', error);
            this.showConnectionStatus('disconnected');
        }
    }

    /**
     * Handle order paid event
     */
    handleOrderPaid(data) {
        // Show notification
        this.showNotification('New Order', `Order #${data.order_number} paid: RM ${data.total_amount}`, 'success');

        // Update stats
        this.updateStatCard('revenue', data.analytics.total_revenue);
        this.updateStatCard('orders', data.analytics.total_orders);
        this.updateStatCard('avg-order', data.analytics.avg_order_value);

        if (data.analytics.qr_orders !== undefined) {
            this.updateStatCard('qr-orders', data.analytics.qr_orders);
        }

        // Refresh charts after a short delay
        setTimeout(() => {
            this.refreshCharts();
        }, 1000);

        // Add visual feedback
        this.flashElement('#revenue-card');
    }

    /**
     * Handle promotion used event
     */
    handlePromotionUsed(data) {
        this.showNotification('Promotion Applied', `Discount: RM ${data.discount_amount}`, 'info');

        if (data.analytics.promotions_used !== undefined) {
            this.updateStatCard('promotions', data.analytics.promotions_used);
        }
        if (data.analytics.total_discounts !== undefined) {
            this.updateStatCard('discounts', data.analytics.total_discounts);
        }

        this.flashElement('#promotions-card');
    }

    /**
     * Handle reward redeemed event
     */
    handleRewardRedeemed(data) {
        this.showNotification('Reward Redeemed', `Points used: ${data.points_used}`, 'success');

        if (data.analytics.rewards_redeemed !== undefined) {
            this.updateStatCard('rewards', data.analytics.rewards_redeemed);
        }

        this.flashElement('#rewards-card');
    }

    /**
     * Handle booking created event
     */
    handleBookingCreated(data) {
        this.showNotification('New Booking', `Table ${data.table_id} - ${data.guest_count} guests`, 'info');

        if (data.analytics.table_bookings !== undefined) {
            this.updateStatCard('bookings', data.analytics.table_bookings);
        }

        this.flashElement('#bookings-card');
    }

    /**
     * Update a stat card value
     */
    updateStatCard(cardId, newValue) {
        const selectors = {
            'revenue': '#current-month-revenue',
            'orders': '#current-month-orders',
            'avg-order': '#avg-order-value',
            'qr-orders': '#qr-orders-count',
            'promotions': '#promotions-used-count',
            'discounts': '#total-discounts',
            'rewards': '#rewards-redeemed-count',
            'bookings': '#table-bookings-count'
        };

        const element = document.querySelector(selectors[cardId]);
        if (element) {
            // Animate the change
            element.style.transition = 'all 0.3s ease';
            element.style.transform = 'scale(1.1)';

            // Format the value
            if (cardId === 'revenue' || cardId === 'discounts' || cardId === 'avg-order') {
                element.textContent = 'RM ' + parseFloat(newValue).toFixed(2);
            } else {
                element.textContent = newValue;
            }

            // Reset animation
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 300);
        }
    }

    /**
     * Flash an element to draw attention
     */
    flashElement(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.classList.add('flash-update');
            setTimeout(() => {
                element.classList.remove('flash-update');
            }, 1000);
        }
    }

    /**
     * Show notification
     */
    showNotification(title, message, type = 'info') {
        // Check if notification permission is granted
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/images/logo.png',
                badge: '/images/badge.png'
            });
        }

        // Also show in-page notification
        this.showToast(title, message, type);
    }

    /**
     * Show in-page toast notification
     */
    showToast(title, message, type = 'info') {
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type} show`;
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="toast-body">${message}</div>
        `;

        toastContainer.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    /**
     * Create toast container if it doesn't exist
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Refresh all analytics data
     */
    async refreshAllData() {
        try {
            const response = await fetch('/admin/reports/live-analytics');
            const result = await response.json();

            if (result.success) {
                const data = result.data;

                // Update all stat cards
                this.updateStatCard('revenue', data.total_revenue);
                this.updateStatCard('orders', data.total_orders);
                this.updateStatCard('avg-order', data.avg_order_value);
                this.updateStatCard('qr-orders', data.qr_orders);
                this.updateStatCard('promotions', data.promotions_used);
                this.updateStatCard('discounts', data.promotion_discounts);
                this.updateStatCard('rewards', data.rewards_redeemed);
                this.updateStatCard('bookings', data.table_bookings);

                // Update customer metrics
                this.updateCustomerMetrics(data);

                console.log('✅ Analytics data refreshed:', result.timestamp);
            }
        } catch (error) {
            console.error('❌ Failed to refresh analytics:', error);
        }
    }

    /**
     * Update customer retention metrics
     */
    updateCustomerMetrics(data) {
        const elements = {
            newCustomers: document.getElementById('new-customers'),
            returningCustomers: document.getElementById('returning-customers'),
            retentionRate: document.getElementById('retention-rate')
        };

        if (elements.newCustomers) elements.newCustomers.textContent = data.new_customers;
        if (elements.returningCustomers) elements.returningCustomers.textContent = data.returning_customers;
        if (elements.retentionRate) elements.retentionRate.textContent = data.customer_retention_rate.toFixed(1) + '%';
    }

    /**
     * Refresh all charts
     */
    async refreshCharts() {
        try {
            const response = await fetch('/admin/reports/chart-data?days=30');
            const result = await response.json();

            if (result.success) {
                // Update charts if they exist
                console.log('📊 Refreshing charts with new data...');
                // Implementation depends on your chart library (Chart.js, etc.)
                // This would update the existing chart instances
            }
        } catch (error) {
            console.error('❌ Failed to refresh charts:', error);
        }
    }

    /**
     * Setup charts (placeholder for chart initialization)
     */
    setupCharts() {
        // This will be implemented based on your existing chart setup
        console.log('📊 Chart handlers ready');
    }

    /**
     * Setup manual refresh button
     */
    setupRefreshButton() {
        const refreshBtn = document.getElementById('refresh-analytics-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                refreshBtn.disabled = true;
                refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';

                this.refreshAllData().then(() => {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    this.showToast('Success', 'Analytics data refreshed', 'success');
                });
            });
        }
    }

    /**
     * Show connection status indicator
     */
    showConnectionStatus(status) {
        const indicator = document.getElementById('connection-status');
        if (indicator) {
            const statusMap = {
                'connected': { text: 'Live', color: '#10b981', icon: 'circle' },
                'disconnected': { text: 'Offline', color: '#ef4444', icon: 'times-circle' }
            };

            const config = statusMap[status] || statusMap.disconnected;
            indicator.innerHTML = `
                <i class="fas fa-${config.icon}" style="color: ${config.color}"></i>
                <span style="color: ${config.color}">${config.text}</span>
            `;
        }
    }

    /**
     * Request notification permission
     */
    static requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log('Notification permission:', permission);
            });
        }
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    RealtimeAnalytics.requestNotificationPermission();

    // Initialize real-time analytics
    window.realtimeAnalytics = new RealtimeAnalytics();
});
