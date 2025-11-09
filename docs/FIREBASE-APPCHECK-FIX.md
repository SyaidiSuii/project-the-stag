# Firebase App Check / Web Push Authentication Issue

## Problem Analysis

The error message indicates:
```
Request is missing required authentication credential. Expected OAuth 2 access token, 
login cookie or other valid authentication credential.
```

Even though anonymous sign-in succeeds, FCM token generation still fails. This is because:

1. **App Check might be enabled** - Requires additional verification
2. **Web Push certificates not properly configured** - Missing authentication scope
3. **Firebase project requires additional authentication** - Beyond just user auth

## Solution Steps

### Option 1: Disable App Check (For Development)

1. Go to Firebase Console: https://console.firebase.google.com/
2. Select project: "the-stag-notification"
3. Click **"App Check"** in left sidebar (under Build section)
4. Find your web app
5. Click on it
6. **Disable enforcement** or set to "Monitor mode" (not enforced)
7. Save changes

### Option 2: Configure App Check with reCAPTCHA

If App Check is required, configure it:

1. In App Check section
2. Click "Register" for your web app
3. Choose **"reCAPTCHA v3"**
4. Follow setup instructions
5. Add reCAPTCHA site key to your app

### Option 3: Check Firebase Project Settings

1. Go to **Project Settings** (gear icon)
2. Go to **"Service accounts"** tab
3. Ensure service account has proper permissions:
   - Cloud Messaging
   - Firebase Cloud Messaging API

### Option 4: Enable Firebase Cloud Messaging API

This is likely the main issue!

1. Go to Google Cloud Console: https://console.cloud.google.com/
2. Select project: "the-stag-notification"
3. Go to **"APIs & Services"** → **"Library"**
4. Search for **"Firebase Cloud Messaging API"**
5. Click on it
6. Click **"Enable"** if not already enabled
7. Also enable **"FCM Registration API"**

### Option 5: Use Legacy FCM Server Key (Temporary)

If all else fails, use the legacy approach:

1. Firebase Console → Project Settings → Cloud Messaging
2. Check if you have a **"Server key"** (legacy)
3. If not, you need to migrate to HTTP v1 API

## Most Likely Solution

Based on the error, the most common cause is:

**Firebase Cloud Messaging API is not enabled in Google Cloud Console**

Follow Option 4 above to enable it.

## After Enabling API

Wait 5-10 minutes for changes to propagate, then:

1. Clear browser cache
2. Unregister service workers
3. Hard refresh (Ctrl + Shift + R)
4. Try again

