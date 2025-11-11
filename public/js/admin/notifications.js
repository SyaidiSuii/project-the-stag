document.addEventListener('DOMContentLoaded', () => {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    // These will be defined in the main layout file
    const FETCH_URL = window.notificationConfig.fetchUrl;
    const MARK_READ_URL = window.notificationConfig.markReadUrl;
    const USER_ID = window.notificationConfig.userId;

    if (!notificationBtn || !FETCH_URL || !MARK_READ_URL || !USER_ID) {
        console.error('Notification script is missing required configuration.');
        return;
    }

    const renderNotification = (notification) => {
        const item = document.createElement('a');
        item.href = notification.link || '#';
        item.className = `notification-item ${!notification.read_at ? 'unread' : ''}`;
        item.dataset.id = notification.id;

        const iconClass = notification.type === 'new_order' ? 'new_order' : 'new_booking';
        const icon = notification.type === 'new_order' ? 'fa-shopping-bag' : 'fa-calendar-check';

        item.innerHTML = `
            <div class="notification-icon ${iconClass}">
                <i class="fas ${icon}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${notification.title}</div>
                <div class="notification-text">${notification.message}</div>
                <div class="notification-time">${timeAgo(new Date(notification.created_at))}</div>
            </div>
        `;
        return item;
    };

    const updateBadge = (count) => {
        if (count > 0) {
            notificationBadge.textContent = count > 9 ? '9+' : count;
            notificationBadge.classList.add('visible');
        } else {
            notificationBadge.classList.remove('visible');
        }
    };

    const fetchNotifications = async () => {
        try {
            const response = await fetch(FETCH_URL);
            if (!response.ok) return;

            const data = await response.json();
            
            notificationList.innerHTML = '';
            if (data.notifications.length === 0) {
                notificationList.innerHTML = '<div class="notification-text" style="padding: 20px; text-align: center;">No notifications yet.</div>';
            } else {
                data.notifications.forEach(notification => {
                    notificationList.appendChild(renderNotification(notification));
                });
            }
            updateBadge(data.unread_count);
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await fetch(MARK_READ_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            updateBadge(0);
        } catch (error) {
            console.error('Error marking notifications as read:', error);
        }
    };

    // Event Listeners
    notificationBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
            notificationDropdown.classList.remove('active');
        }
    });

    markAllReadBtn.addEventListener('click', markAllAsRead);

    // Initial fetch
    fetchNotifications();

    // Listen for real-time notifications
    if (window.Echo) {
        window.Echo.private(`App.Models.User.${USER_ID}`)
            .notification((notification) => {
                console.log('Real-time notification received:', notification);
                
                const newNotificationElement = renderNotification(notification);
                
                // Remove placeholder if it exists
                const placeholder = notificationList.querySelector('.notification-text');
                if(placeholder) placeholder.remove();

                notificationList.prepend(newNotificationElement);

                const currentCount = parseInt(notificationBadge.textContent) || 0;
                updateBadge(currentCount + 1);

                const audio = new Audio('/sounds/notification.mp3');
                audio.play().catch(e => console.log('Could not play sound:', e));
            });
    }
});

function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 5) return "just now";
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + " years ago";
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + " months ago";
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + " days ago";
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + " hours ago";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + " minutes ago";
    return Math.floor(seconds) + " seconds ago";
}
