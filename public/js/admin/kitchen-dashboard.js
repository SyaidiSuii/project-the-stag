class KitchenDashboard {
    constructor() {
        this.refreshInterval = 10000; // 10 seconds
        this.notificationsEnabled = true;
        this.init();
    }

    init() {
        this.startAutoRefresh();
        this.setupNotifications();
    }

    startAutoRefresh() {
        setInterval(() => {
            this.fetchKitchenStatus();
        }, this.refreshInterval);
    }

    async fetchKitchenStatus() {
        try {
            const response = await fetch('/admin/kitchen/api/status');
            const data = await response.json();

            if (data.success) {
                this.updateStationCards(data.stations);
            }
        } catch (error) {
            console.error('Failed to fetch kitchen status:', error);
        }
    }

    updateStationCards(stations) {
        stations.forEach(station => {
            const card = document.querySelector(`[data-station-id="${station.id}"]`);
            if (card) {
                // Update progress bar
                const progressBar = card.querySelector('.progress-bar');
                progressBar.style.width = `${station.load_percentage}%`;

                // Update load text
                card.querySelector('.load-text').textContent =
                    `${station.current_load} / ${station.max_capacity}`;

                // Update percentage
                card.querySelector('.load-percentage').textContent =
                    `${station.load_percentage}%`;

                // Toggle overloaded class
                if (station.is_overloaded) {
                    card.classList.add('overloaded');
                    this.triggerNotification(station);
                } else {
                    card.classList.remove('overloaded');
                }
            }
        });
    }

    setupNotifications() {
        // Request browser notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    triggerNotification(station) {
        // Audio notification
        this.playNotificationSound();

        // Browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('🚨 Kitchen Alert!', {
                body: `${station.name} is at ${station.load_percentage}% capacity`,
                icon: '/images/kitchen-alert.png',
                requireInteraction: true
            });
        }

        // Toast notification
        this.showToast(`⚠ ${station.name} approaching capacity!`);

        // Title bar flash
        this.flashTitle(`🔴 KITCHEN ALERT!`);
    }

    playNotificationSound() {
        const audio = new Audio('/sounds/kitchen-bell.mp3');
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Audio play failed:', e));
    }

    showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast toast-warning';
        toast.textContent = message;
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);

        // Remove after 5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    flashTitle(alertTitle) {
        const originalTitle = document.title;
        let flashCount = 0;

        const flashInterval = setInterval(() => {
            document.title = document.title === originalTitle ? alertTitle : originalTitle;
            flashCount++;

            if (flashCount > 10) { // Flash 5 times
                clearInterval(flashInterval);
                document.title = originalTitle;
            }
        }, 1000);

        // Stop flashing when user clicks page
        document.addEventListener('click', () => {
            clearInterval(flashInterval);
            document.title = originalTitle;
        }, { once: true });
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
    new KitchenDashboard();
});