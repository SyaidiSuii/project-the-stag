// Firebase Configuration for Web Push Notifications
// This file initializes Firebase for the web application

// Import the functions you need from the Firebase SDK
import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js';

// Your web app's Firebase configuration
// These values should match your Firebase project settings
const firebaseConfig = {
    apiKey: window.FIREBASE_CONFIG?.apiKey || '',
    authDomain: window.FIREBASE_CONFIG?.authDomain || '',
    projectId: window.FIREBASE_CONFIG?.projectId || '',
    storageBucket: window.FIREBASE_CONFIG?.storageBucket || '',
    messagingSenderId: window.FIREBASE_CONFIG?.messagingSenderId || '',
    appId: window.FIREBASE_CONFIG?.appId || ''
};

// Initialize Firebase
let app = null;
let messaging = null;

try {
    app = initializeApp(firebaseConfig);
    messaging = getMessaging(app);
    console.log('Firebase initialized successfully');
} catch (error) {
    console.error('Firebase initialization error:', error);
}

// Export for use in other files
export { app, messaging, getToken, onMessage };
