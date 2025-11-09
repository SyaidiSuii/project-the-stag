// Firebase Cloud Messaging Service Worker
// This service worker handles background push notifications

importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Initialize Firebase in the service worker
// Note: This configuration will be set dynamically when the service worker is registered
firebase.initializeApp({
    apiKey: "AIzaSyBO-_-PSDlUZkY0dCI7lI8LeJzoRRBvSEQ",
    authDomain: "the-stag-notif-v2.firebaseapp.com",
    projectId: "the-stag-notif-v2",
    storageBucket: "the-stag-notif-v2.firebasestorage.app",
    messagingSenderId: "595478392275",
    appId: "1:595478392275:web:56b641955e431fe3ddd326",
    measurementId: "G-31D3G5BFG6"
});

// Retrieve an instance of Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    // Customize notification (using compatible syntax)
    const notificationTitle = (payload.notification && payload.notification.title)
        ? payload.notification.title
        : 'The Stag - SmartDine';

    const notificationOptions = {
        body: (payload.notification && payload.notification.body)
            ? payload.notification.body
            : 'You have a new notification',
        icon: '/images/logo.png',
        badge: '/images/logo.png',
        tag: (payload.data && payload.data.type) ? payload.data.type : 'general',
        data: payload.data || {},
        requireInteraction: true, // Keep notification until user interacts
        actions: [
            {
                action: 'view',
                title: 'View'
            },
            {
                action: 'close',
                title: 'Close'
            }
        ]
    };

    // Show notification
    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification clicked:', event);

    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    // Handle different notification types
    const data = event.notification.data;
    let urlToOpen = '/customer';

    if (data) {
        switch (data.type) {
            case 'order_status':
                urlToOpen = '/customer/orders';
                break;
            case 'reservation':
                urlToOpen = '/customer/booking';
                break;
            case 'promotion':
                urlToOpen = '/customer/promotions';
                break;
            case 'new_order':
                // Admin notification: new order from customer
                urlToOpen = data.click_action || '/admin/orders';
                break;
            case 'new_reservation':
                // Admin notification: new table reservation
                urlToOpen = data.click_action || '/admin/table-reservation';
                break;
            default:
                urlToOpen = '/customer';
        }
    }

    // Open or focus the app window
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (let client of clientList) {
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }
                // If no window is open, open a new one
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});
