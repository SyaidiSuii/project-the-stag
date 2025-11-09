# FCM Testing Guide - Quick Start

## Step 1: Setup Firebase Project (5 minutes)

### A. Create Firebase Project
1. Pergi ke [Firebase Console](https://console.firebase.google.com/)
2. Click **"Create a project"**
3. Name: `the-stag-test` (atau nama lain)
4. Click **"Create project"**

### B. Get Configuration Values
1. Click **"Add app"** → **Web** icon
2. App nickname: `The Stag Test`
3. Click **"Register app"**
4. Copy semua values (apiKey, authDomain, projectId, dll.) - simpan kat Notepad

### C. Enable Cloud Messaging
1. Sidebar → **"Build"** → **"Cloud Messaging"**
2. Tab **"Web Push certificates"**
3. Click **"Generate key pair"**
4. Copy **VAPID key** - simpan kat Notepad

### D. Service Account Key
1. Top-left gear icon → **"Project Settings"**
2. Tab **"Service Accounts"**
3. Click **"Generate new private key"**
4. Download JSON file
5. Rename jadi `firebase-service-account.json`
6. Place dalam folder: `d:\ProgramsFiles\laragon\www\the_stag\firebase-service-account.json`

---

## Step 2: Configure Laravel (.env)

Update `.env` file dalam project root:

```bash
# Firebase Configuration (GANTI dengan values dari Step 1B & 1C)
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_DATABASE_URL=https://your-project-default-rtdb.firebaseio.com
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abcdef123456
FIREBASE_VAPID_KEY=your-vapid-key
FIREBASE_SERVICE_ACCOUNT_PATH=./firebase-service-account.json

# Notification Settings
NOTIFICATIONS_ENABLED=true
NOTIFICATION_LOG_CHANNEL=daily
```

**Restart Laravel server:**
```bash
php artisan config:clear
php artisan serve
```

---

## Step 3: Test API Endpoints

### 1. Register Device Token
Test device registration using Postman atau curl:

**POST** `http://127.0.0.1:8000/api/fcm/register`
```json
{
    "device_token": "test-device-token-123",
    "device_type": "web",
    "platform": "Chrome",
    "browser": "Chrome",
    "version": "120"
}
```

**Response Expected:**
```json
{
    "success": true,
    "message": "Device registered successfully",
    "data": {
        "device_id": 1,
        "device_type": "web",
        "platform": "Chrome"
    }
}
```

### 2. Get FCM Statistics (Admin Only)
**GET** `http://127.0.0.1:8000/api/fcm/statistics`

**Response Expected:**
```json
{
    "success": true,
    "data": {
        "total_devices": 1,
        "active_devices": 1,
        "device_types": {
            "web": 1
        },
        "recent_notifications": 0
    }
}
```

### 3. Test Connection (Admin Only)
**POST** `http://127.0.0.1:8000/api/fcm/test`

**Response Expected:**
```json
{
    "success": true,
    "message": "Test notification sent",
    "data": {
        "sent_to": "0 devices"
    }
}
```

---

## Step 4: Check Logs

Check Laravel logs untuk verify FCM working:

```bash
tail -f storage/logs/laravel.log
```

Search untuk messages like:
- `Order status notification sent via FCM`
- `Reservation notification sent via FCM`
- `FCM notifications sent successfully`

---

## Step 5: Verify Database

Check database table:

```sql
SELECT * FROM user_fcm_devices;
```

Should show registered devices.

---

## Quick Test Commands

### Via PHP Artisan Tinker
```php
// Test FCM Service
app(\App\Services\FCMNotificationService::class)->getStatistics();

// Test manual notification
app(\App\Services\FCMNotificationService::class)->sendPromotionalNotification(
    'Test Title',
    'This is a test message'
);
```

---

## Troubleshooting

### ❌ Error: "Firebase service account not found"
**Solution:** Check file path dalam `.env` dan pastikan file JSON exist

### ❌ Error: "Invalid registration token"
**Solution:** Device token expired/invalid - normal untuk test

### ❌ Error: "Project ID mismatch"
**Solution:** Check FIREBASE_PROJECT_ID dalam .env match dengan Firebase project

### ❌ No notifications received
**Solution:**
1. Check browser notification permissions
2. Verify VAPID key is correct
3. Check console for JavaScript errors
4. Ensure `firebase-service-account.json` is valid JSON

---

## Success Indicators ✅

1. ✅ Database table `user_fcm_devices` created
2. ✅ API endpoints return success responses
3. ✅ Laravel logs show FCM activity
4. ✅ Statistics endpoint returns device counts
5. ✅ No error messages dalam logs

---

## Next: Frontend Integration

After successful testing, proceed ke **Phase 5 & 6**:
- Phase 5: Admin Interface (dashboard untuk manage notifications)
- Phase 6: Frontend Integration (add FCM SDK to customer portal)

---

## Support

If testing fails:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify all .env values correct
3. Ensure `firebase-service-account.json` is valid
4. Restart Laravel server after .env changes

**Testing Complete! 🎉**
