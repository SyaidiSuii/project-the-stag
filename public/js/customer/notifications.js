/**
 * FCM Web Push Notifications Handler
 * Handles notification permissions, token registration, and message reception
 */

class FCMNotificationManager {
    constructor() {
        this.messaging = null;
        this.currentToken = null;
        this.isSupported = this.checkSupport();
        this.vapidKey = window.FIREBASE_VAPID_KEY || null;
    }

    /**
     * Check if browser supports notifications
     */
    checkSupport() {
        if (!('Notification' in window)) {
            console.warn('This browser does not support notifications');
            return false;
        }
        if (!('serviceWorker' in navigator)) {
            console.warn('This browser does not support service workers');
            return false;
        }
        return true;
    }

    /**
     * Initialize FCM
     */
    async initialize() {
        if (!this.isSupported) {
            return false;
        }

        try {
            // Dynamically import Firebase modules
            const { initializeApp } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js');
            const { getMessaging, getToken, onMessage } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js');
            const { getAuth, signInAnonymously, onAuthStateChanged } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js');

            // Initialize Firebase
            const firebaseConfig = {
                apiKey: window.FIREBASE_CONFIG?.apiKey || '',
                authDomain: window.FIREBASE_CONFIG?.authDomain || '',
                projectId: window.FIREBASE_CONFIG?.projectId || '',
                storageBucket: window.FIREBASE_CONFIG?.storageBucket || '',
                messagingSenderId: window.FIREBASE_CONFIG?.messagingSenderId || '',
                appId: window.FIREBASE_CONFIG?.appId || ''
            };

            const app = initializeApp(firebaseConfig);

            // Initialize Firebase Authentication for FCM token generation
            console.log('Initializing Firebase Auth for FCM...');
            const auth = getAuth(app);

            // Sign in anonymously if not already signed in
            await new Promise((resolve, reject) => {
                const unsubscribe = onAuthStateChanged(auth, async (user) => {
                    unsubscribe();
                    if (!user) {
                        console.log('Signing in anonymously for FCM...');
                        try {
                            await signInAnonymously(auth);
                            console.log('Anonymous sign-in successful');
                            resolve();
                        } catch (error) {
                            console.error('Anonymous sign-in failed:', error);
                            reject(error);
                        }
                    } else {
                        console.log('Already authenticated:', user.uid);
                        resolve();
                    }
                });
            });

            this.messaging = getMessaging(app);

            // Register service worker
            await this.registerServiceWorker();

            // Setup foreground message handler
            this.setupForegroundMessageHandler(onMessage);

            console.log('FCM initialized successfully');
            return true;

        } catch (error) {
            console.error('Error initializing FCM:', error);
            return false;
        }
    }

    /**
     * Register service worker and wait for it to be ready
     */
    async registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
                scope: '/'
            });
            console.log('Service Worker registered:', registration);

            // Wait for service worker to be active
            if (registration.installing) {
                console.log('Service Worker installing, waiting for activation...');
                await new Promise((resolve) => {
                    registration.installing.addEventListener('statechange', function() {
                        if (this.state === 'activated') {
                            console.log('Service Worker activated');
                            resolve();
                        }
                    });
                });
            } else if (registration.waiting) {
                console.log('Service Worker waiting, waiting for activation...');
                await new Promise((resolve) => {
                    registration.waiting.addEventListener('statechange', function() {
                        if (this.state === 'activated') {
                            console.log('Service Worker activated');
                            resolve();
                        }
                    });
                });
            } else if (registration.active) {
                console.log('Service Worker already active');
            }

            // Extra safety: wait for service worker to be ready
            await navigator.serviceWorker.ready;
            console.log('Service Worker ready');

            return registration;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            throw error;
        }
    }

    /**
     * Request notification permission
     */
    async requestPermission() {
        if (!this.isSupported) {
            return false;
        }

        try {
            const permission = await Notification.requestPermission();

            if (permission === 'granted') {
                console.log('Notification permission granted');
                return true;
            } else {
                console.log('Notification permission denied');
                return false;
            }
        } catch (error) {
            console.error('Error requesting permission:', error);
            return false;
        }
    }

    /**
     * Get FCM token (with service worker ready check)
     */
    async getToken() {
        if (!this.messaging) {
            await this.initialize();
        }

        if (!this.messaging || !this.vapidKey) {
            console.error('FCM not initialized or VAPID key missing');
            return null;
        }

        try {
            // CRITICAL: Ensure service worker is fully ready
            console.log('Waiting for service worker to be ready...');
            const registration = await navigator.serviceWorker.ready;

            // Extra check: ensure it's active
            if (!registration.active) {
                console.error('Service worker registration exists but not active');
                return null;
            }

            console.log('Service worker is ready and active');

            // Small delay to ensure everything is settled
            await new Promise(resolve => setTimeout(resolve, 500));

            // Import getToken dynamically
            const { getToken } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js');

            console.log('Getting FCM token...');
            const token = await getToken(this.messaging, {
                vapidKey: this.vapidKey,
                serviceWorkerRegistration: registration
            });

            if (token) {
                this.currentToken = token;
                console.log('FCM Token:', token);
                return token;
            } else {
                console.log('No registration token available');
                return null;
            }
        } catch (error) {
            console.error('Error getting FCM token:', error);
            return null;
        }
    }

    /**
     * Register device token with backend
     */
    async registerDevice() {
        const token = await this.getToken();

        if (!token) {
            console.error('Cannot register device without token');
            return false;
        }

        try {
            // Get device info
            const deviceInfo = this.getDeviceInfo();

            // Prepare headers
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            };

            // Add Authorization header only if token exists (for API/mobile clients)
            const authToken = localStorage.getItem('auth_token');
            if (authToken && authToken.trim() !== '') {
                headers['Authorization'] = 'Bearer ' + authToken;
            }

            // Send to backend
            const response = await fetch('/api/fcm/register', {
                method: 'POST',
                headers: headers,
                credentials: 'same-origin', // Include cookies for session auth
                body: JSON.stringify({
                    device_token: token,
                    device_type: 'web',
                    platform: deviceInfo.platform,
                    browser: deviceInfo.browser,
                    version: deviceInfo.version
                })
            });

            // Check response status first
            if (!response.ok) {
                const responseText = await response.text();
                console.error('API Error Response:', {
                    status: response.status,
                    statusText: response.statusText,
                    body: responseText.substring(0, 500) // First 500 chars
                });
                return false;
            }

            const data = await response.json();

            if (data.success) {
                console.log('Device registered successfully:', data);
                localStorage.setItem('fcm_device_id', data.data?.device_id);
                localStorage.setItem('fcm_token', token);
                return true;
            } else {
                console.error('Device registration failed:', data);
                return false;
            }

        } catch (error) {
            console.error('Error registering device:', error);
            return false;
        }
    }

    /**
     * Get device information
     */
    getDeviceInfo() {
        const userAgent = navigator.userAgent;
        let browser = 'Unknown';
        let version = 'Unknown';

        if (userAgent.includes('Firefox')) {
            browser = 'Firefox';
            version = userAgent.match(/Firefox\/(\d+\.\d+)/)?.[1] || 'Unknown';
        } else if (userAgent.includes('Chrome')) {
            browser = 'Chrome';
            version = userAgent.match(/Chrome\/(\d+\.\d+)/)?.[1] || 'Unknown';
        } else if (userAgent.includes('Safari')) {
            browser = 'Safari';
            version = userAgent.match(/Version\/(\d+\.\d+)/)?.[1] || 'Unknown';
        } else if (userAgent.includes('Edge')) {
            browser = 'Edge';
            version = userAgent.match(/Edge\/(\d+\.\d+)/)?.[1] || 'Unknown';
        }

        return {
            platform: navigator.platform,
            browser: browser,
            version: version
        };
    }

    /**
     * Setup foreground message handler
     */
    async setupForegroundMessageHandler(onMessage) {
        if (!this.messaging) return;

        console.log('Setting up foreground message handler...');

        onMessage(this.messaging, (payload) => {
            console.log('🔔 Foreground message received:', payload);

            // Show browser notification for foreground messages
            this.showNotification(payload);

            // Update UI if needed
            this.handleForegroundNotification(payload);
        });

        console.log('✅ Foreground message handler setup complete');
    }

    /**
     * Show browser notification
     */
    async showNotification(payload) {
        if (Notification.permission !== 'granted') {
            return;
        }

        const title = payload.notification?.title || 'The Stag - SmartDine';
        const options = {
            body: payload.notification?.body || '',
            icon: '/images/logo.png',
            badge: '/images/logo.png',
            tag: payload.data?.type || 'general',
            data: payload.data,
            requireInteraction: true
        };

        try {
            const registration = await navigator.serviceWorker.ready;
            await registration.showNotification(title, options);
        } catch (error) {
            console.error('Error showing notification:', error);
            // Fallback to regular notification
            new Notification(title, options);
        }
    }

    /**
     * Handle foreground notifications (update UI)
     */
    handleForegroundNotification(payload) {
        const data = payload.data;

        // Show toast notification
        if (window.Toast) {
            window.Toast.info(
                payload.notification?.title || 'Notification',
                payload.notification?.body || ''
            );
        }

        // Trigger custom event for other parts of the app
        window.dispatchEvent(new CustomEvent('fcm-notification', {
            detail: payload
        }));

        // Handle specific notification types
        if (data?.type === 'order_status') {
            // Refresh order list if on orders page
            if (window.location.pathname.includes('/orders')) {
                this.refreshOrderList();
            }
        } else if (data?.type === 'reservation') {
            // Refresh booking list if on booking page
            if (window.location.pathname.includes('/booking')) {
                this.refreshBookingList();
            }
        }
    }

    /**
     * Refresh order list
     */
    refreshOrderList() {
        // Trigger order list refresh
        const event = new CustomEvent('refresh-orders');
        window.dispatchEvent(event);
    }

    /**
     * Refresh booking list
     */
    refreshBookingList() {
        // Trigger booking list refresh
        const event = new CustomEvent('refresh-bookings');
        window.dispatchEvent(event);
    }

    /**
     * Check current permission status
     */
    getPermissionStatus() {
        if (!this.isSupported) {
            return 'not-supported';
        }
        return Notification.permission;
    }

    /**
     * Enable notifications (request permission and register)
     */
    async enable() {
        const hasPermission = await this.requestPermission();

        if (hasPermission) {
            await this.initialize();
            const registered = await this.registerDevice();

            // Mark user as prompted for notifications
            if (registered) {
                await this.markAsPrompted();
            }

            return registered;
        }

        // Mark as prompted even if denied (don't ask again)
        await this.markAsPrompted();
        return false;
    }

    /**
     * Mark user as prompted for notifications
     */
    async markAsPrompted() {
        try {
            await fetch('/api/user/mark-notification-prompted', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });
            console.log('User marked as prompted for notifications');
        } catch (error) {
            console.error('Error marking user as prompted:', error);
        }
    }

    /**
     * Prompt new users for notification permission
     */
    async promptNewUser() {
        if (!this.isSupported) {
            console.log('Notifications not supported - skipping prompt');
            return;
        }

        // Check if permission already granted or denied
        if (Notification.permission !== 'default') {
            console.log('Notification permission already decided:', Notification.permission);
            await this.markAsPrompted();
            return;
        }

        console.log('Prompting new user for notification permission...');

        // Small delay to let page load completely
        setTimeout(async () => {
            const success = await this.enable();
            if (success) {
                console.log('New user notification setup complete');
            } else {
                console.log('User declined notification permission');
            }
        }, 1000); // 1 second delay
    }
}

// Create global instance
window.FCMNotifications = new FCMNotificationManager();

// Auto-initialize if user already granted permission
document.addEventListener('DOMContentLoaded', async () => {
    if (Notification.permission === 'granted') {
        console.log('Auto-initializing FCM (permission already granted)');
        await window.FCMNotifications.initialize();

        // Always register device to ensure token is fresh and currentToken is set
        await window.FCMNotifications.registerDevice();
    }
});
