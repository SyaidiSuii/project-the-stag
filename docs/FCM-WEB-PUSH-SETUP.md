# 🔔 FCM Web Push Notifications - Setup Guide

## Panduan Lengkap Untuk Setup Web Push Notifications di Windows Browser

---

## 📋 Apa Yang Telah Dipasang

Sistem FCM Web Push Notifications telah siap dengan komponen berikut:

### ✅ Backend Components
- ✅ `FCMNotificationService` - Service untuk handle notifications
- ✅ `UserFcmDevice` Model - Untuk simpan device tokens
- ✅ `PushNotification` Model - Untuk log notifications
- ✅ Event Listeners - Auto send notifications bila order/reservation berubah
- ✅ API Endpoints - `/api/fcm/*` untuk register devices
- ✅ Database migrations - Sudah run

### ✅ Frontend Components
- ✅ `firebase-messaging-sw.js` - Service Worker untuk background notifications
- ✅ `notifications.js` - JavaScript helper untuk FCM
- ✅ Notification Bell UI - Icon di sidebar untuk enable notifications
- ✅ Auto-registration - Bila user enable, auto register device

---

## 🚀 Cara Setup & Aktifkan

### Step 1: Setup Firebase Project

1. **Pergi ke Firebase Console**
   - Buka: https://console.firebase.google.com/
   - Login dengan Google account

2. **Create Project (jika belum ada)**
   - Click "Add project"
   - Masukkan nama: "The Stag SmartDine"
   - Enable Google Analytics (optional)
   - Click "Create project"

3. **Enable Cloud Messaging**
   - Dalam Firebase project, pergi ke **Project settings** (gear icon)
   - Click tab **Cloud Messaging**
   - Copy **Server key** (untuk backend)

4. **Register Web App**
   - Dalam Project settings, scroll ke bahagian **Your apps**
   - Click **Web icon** (</>)
   - Masukkan nickname: "The Stag Web"
   - ✅ Check "Also set up Firebase Hosting" (optional)
   - Click "Register app"

5. **Get Firebase Config**
   - Selepas register, akan dapat **Firebase SDK configuration**
   - Copy config object yang ada nilai:
     ```javascript
     {
       apiKey: "AIza...",
       authDomain: "project-id.firebaseapp.com",
       projectId: "project-id",
       storageBucket: "project-id.appspot.com",
       messagingSenderId: "123456789",
       appId: "1:123456789:web:abc123"
     }
     ```

6. **Generate VAPID Key (Web Push Certificate)**
   - Dalam Cloud Messaging tab
   - Scroll ke **Web configuration**
   - Click **Generate key pair** di bawah "Web Push certificates"
   - Copy **Key pair** value (contoh: BN4zN...)

7. **Download Service Account JSON**
   - Dalam Project settings
   - Tab **Service accounts**
   - Click **Generate new private key**
   - Download file JSON
   - Save as: `storage/app/firebase/firebase_credentials.json`

### Step 2: Update .env File

Tambah Firebase credentials ke file `.env`:

```env
# Firebase Cloud Messaging (FCM)
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_DATABASE_URL=https://your-project-id.firebaseio.com
FIREBASE_STORAGE_BUCKET=your-project-id.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789012
FIREBASE_APP_ID=1:123456789012:web:abcdef123456
FIREBASE_VAPID_KEY=BN4zN...your-vapid-key...xyz

# Notification Settings
NOTIFICATIONS_ENABLED=true
NOTIFICATION_LOG_CHANNEL=daily
```

**⚠️ PENTING:**
- Gantikan semua nilai dengan Firebase config anda
- `FIREBASE_VAPID_KEY` adalah Web Push certificate key pair

### Step 3: Update Service Worker Config

Edit file `public/firebase-messaging-sw.js`, replace placeholder values:

```javascript
firebase.initializeApp({
    apiKey: "YOUR_API_KEY",              // Ganti dengan apiKey dari Firebase config
    authDomain: "YOUR_AUTH_DOMAIN",      // Ganti dengan authDomain
    projectId: "YOUR_PROJECT_ID",        // Ganti dengan projectId
    storageBucket: "YOUR_STORAGE_BUCKET",// Ganti dengan storageBucket
    messagingSenderId: "YOUR_SENDER_ID", // Ganti dengan messagingSenderId
    appId: "YOUR_APP_ID"                 // Ganti dengan appId
});
```

### Step 4: Clear Cache & Restart

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🎯 Cara Guna di Browser

### Enable Notifications (Customer Side)

1. **Login ke Customer Portal**
   - Pergi ke: `http://localhost/customer`
   - Login dengan customer account

2. **Enable Notifications**
   - Tengok sidebar, ada **notification bell icon** di bawah logo
   - Status awal: "Click to enable" (grey color)
   - Click pada bell icon
   - Browser akan popup untuk request permission
   - Click **Allow** / **Izinkan**

3. **Notification Bell Status**
   - 🟢 **Green** (Enabled) - Notifications aktif
   - 🔴 **Red** (Blocked) - User block notifications, perlu enable di browser settings
   - ⚪ **Grey** (Default) - Belum enable, click untuk activate

### Test Notifications

#### Test 1: Manual Test Notification
```bash
# Test dari backend menggunakan Tinker
php artisan tinker

# Send test notification
$user = App\Models\User::find(1); // Ganti dengan customer user ID
$service = app(App\Services\FCMNotificationService::class);
$service->sendToUser($user->id, [
    'title' => 'Test Notification',
    'body' => 'This is a test from Laravel!',
    'data' => ['type' => 'test']
]);
```

#### Test 2: Order Status Change
1. Login sebagai **customer**
2. Buat order baru
3. Login sebagai **admin** di tab lain
4. Tukar status order (contoh: pending → confirmed)
5. Customer akan dapat notification di Windows!

#### Test 3: Reservation Confirmation
1. Customer buat reservation
2. Admin confirm reservation
3. Customer dapat notification

---

## 🔍 Troubleshooting

### Issue: Notification Bell Tidak Muncul
**Solution:**
- Pastikan anda login sebagai customer
- Check browser console untuk errors
- Refresh page (Ctrl + F5)

### Issue: Permission Denied / Blocked
**Solution:**
- Click padlock icon di browser address bar
- Go to **Site settings** → **Notifications**
- Change to **Allow**
- Refresh page

### Issue: Token Registration Failed
**Solution:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check if user authenticated
# Notification scripts only load for @auth users
```

### Issue: Background Notifications Tidak Muncul
**Solution:**
1. Check service worker registered:
   - Browser DevTools → Application tab → Service Workers
   - Should see `firebase-messaging-sw.js` registered

2. Check Firebase config:
   - Open `public/firebase-messaging-sw.js`
   - Ensure all values are correct (no placeholder text)

3. Unregister and re-register service worker:
   - DevTools → Application → Service Workers
   - Click "Unregister"
   - Refresh page

### Issue: Notifications Tidak Sampai
**Checklist:**
- ✅ Device token registered? Check `user_fcm_devices` table
- ✅ Firebase credentials correct di `.env`?
- ✅ Service account JSON file exists?
- ✅ Event listeners registered di `EventServiceProvider`?
- ✅ Order status actually changed?

---

## 📱 Browser Support

| Browser | Windows | macOS | Android | iOS |
|---------|---------|-------|---------|-----|
| Chrome  | ✅      | ✅    | ✅      | ❌  |
| Firefox | ✅      | ✅    | ✅      | ❌  |
| Edge    | ✅      | ✅    | ✅      | ❌  |
| Safari  | ❌      | ✅*   | N/A     | ❌  |

*Safari macOS requires version 16.1+

**⚠️ Note:** iOS Safari tidak support web push notifications. Perlu native app.

---

## 🎨 Notification Types

### 1. Order Status Notifications
Trigger: Bila order status berubah

Status yang trigger notification:
- `confirmed` → "Order Confirmed! 🍽️"
- `preparing` → "Preparing Your Food 👨‍🍳"
- `ready` → "Order Ready! ✨"
- `completed` → "Order Completed! 🎉"
- `cancelled` → "Order Cancelled"

### 2. Reservation Notifications
Trigger: Bila reservation dibuat atau berubah

Types:
- `confirmed` → "Reservation Confirmed! 📅"
- `cancelled` → "Reservation Cancelled"
- `reminder` → "Reservation Reminder ⏰"

### 3. Promotional Notifications
Admin boleh send promotional notifications ke semua customers:

```php
$service = app(App\Services\FCMNotificationService::class);
$service->sendPromotionalNotification(
    'New Promo! 🎉',
    'Get 20% off on all meals this weekend!',
    'customers' // optional: filter by user type
);
```

---

## 🛠️ Admin Features

### Check Notification Statistics
**Endpoint:** `GET /api/fcm/statistics`

**Required Role:** admin, manager, super-admin

**Response:**
```json
{
    "success": true,
    "data": {
        "total_devices": 156,
        "active_devices": 142,
        "device_types": {
            "web": 98,
            "android": 32,
            "ios": 12
        },
        "recent_notifications": 45
    }
}
```

### Test Connection
**Endpoint:** `POST /api/fcm/test`

Send test notification ke semua active devices.

### View Notification History
**Endpoint:** `GET /api/fcm/history`

Customer boleh tengok notification history mereka.

---

## 📊 Database Tables

### `user_fcm_devices`
Simpan device tokens untuk setiap user.

```sql
id, user_id, device_token, device_type, platform, browser,
version, is_active, last_used_at, created_at, updated_at
```

### `push_notifications`
Log semua notifications yang dihantar.

```sql
id, user_id, order_id, reservation_id, title, message, type,
data, is_sent, sent_at, is_read, read_at, delivery_status,
scheduled_for, created_at, updated_at, deleted_at
```

---

## 🔐 Security Notes

1. **VAPID Key** - Keep secret, jangan commit ke Git
2. **Service Account JSON** - Add to `.gitignore`
3. **Device Tokens** - Automatically hidden dalam API responses
4. **Token Cleanup** - Auto deactivate invalid tokens

---

## 📝 API Endpoints Summary

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/fcm/register` | Register device token | Yes |
| GET | `/api/fcm/devices` | Get user's devices | Yes |
| DELETE | `/api/fcm/devices/{id}` | Deactivate device | Yes |
| POST | `/api/fcm/send` | Send to self (testing) | Yes |
| GET | `/api/fcm/history` | Get notification history | Yes |
| GET | `/api/fcm/statistics` | Get FCM stats | Admin |
| POST | `/api/fcm/test` | Test connection | Admin |

---

## ✨ What Happens When Order Status Changes

1. **Order status diupdate** (contoh: pending → confirmed)
2. **`OrderStatusUpdatedEvent` fired**
3. **`SendOrderStatusNotification` listener triggered**
4. **FCMNotificationService sends notification**
5. **Notification delivered to:**
   - Background: Service Worker shows notification
   - Foreground: Toast appears + notification badge

---

## 🎉 Success Checklist

Bila setup betul, anda akan dapat:

- ✅ Bell icon muncul di customer sidebar
- ✅ Click bell, browser popup permission
- ✅ After allow, bell icon jadi hijau
- ✅ Admin tukar order status → notification popup di Windows
- ✅ Notification bunyi (jika browser sound enabled)
- ✅ Click notification → buka order page
- ✅ Check `user_fcm_devices` table → ada device registered
- ✅ Check `push_notifications` table → ada notification log

---

## 📞 Need Help?

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console (F12) for JavaScript errors
3. Test API endpoints using Postman
4. Check FCM statistics: `/api/fcm/statistics`

---

**Selamat Menggunakan! 🚀**
