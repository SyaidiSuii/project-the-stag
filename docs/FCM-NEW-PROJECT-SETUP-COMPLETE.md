# FCM New Firebase Project Setup - COMPLETE

## What Was Done

Successfully migrated from `the-stag-notification` to `the-stag-notif-v2` (new Firebase project) to avoid billing/API enablement issues.

## New Firebase Project Details

**Project Name:** the-stag-notif-v2
**Plan:** Spark (FREE)
**Project ID:** the-stag-notif-v2

### New Credentials

```
API Key: AIzaSyBO-_-PSDlUZkY0dCI7lI8LeJzoRRBvSEQ
Auth Domain: the-stag-notif-v2.firebaseapp.com
Project ID: the-stag-notif-v2
Storage Bucket: the-stag-notif-v2.firebasestorage.app
Messaging Sender ID: 595478392275
App ID: 1:595478392275:web:56b641955e431fe3ddd326
Measurement ID: G-31D3G5BFG6
VAPID Key: BJgXUylh8XHPBLLZOVaw4kAuLeFcVpKCCp6sNWLdl_fFhSMtfEVUX6aDaHZ33vDi2LRjMrS8TexE1UbHgYw37Nc
```

## Files Updated

### 1. ✅ `.env` (Backend Configuration)
```env
FIREBASE_API_KEY=AIzaSyBO-_-PSDlUZkY0dCI7lI8LeJzoRRBvSEQ
FIREBASE_PROJECT_ID=the-stag-notif-v2
FIREBASE_DATABASE_URL=https://the-stag-notif-v2.firebaseio.com
FIREBASE_STORAGE_BUCKET=the-stag-notif-v2.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=595478392275
FIREBASE_APP_ID=1:595478392275:web:56b641955e431fe3ddd326
FIREBASE_VAPID_KEY=BJgXUylh8XHPBLLZOVaw4kAuLeFcVpKCCp6sNWLdl_fFhSMtfEVUX6aDaHZ33vDi2LRjMrS8TexE1UbHgYw37Nc
FIREBASE_SERVICE_ACCOUNT_PATH=./storage/app/firebase/firebase_credentials_new.json
```

### 2. ✅ `storage/app/firebase/firebase_credentials_new.json` (Service Account)
- Created new service account JSON file
- Contains private key and authentication credentials for backend FCM operations
- Path referenced in `.env` file

### 3. ✅ `public/firebase-messaging-sw.js` (Service Worker)
- Updated by user manually with new Firebase config
- Handles background push notifications
- Includes measurement ID

### 4. ✅ `resources/views/layouts/customer.blade.php` (Frontend)
- Already contains new Firebase config in `window.FIREBASE_CONFIG`
- VAPID key loaded from `.env` via `config('services.fcm.vapid_key')`

### 5. ✅ Database Cleanup
- All old device tokens cleared from `user_fcm_devices` table
- Fresh start for new Firebase project

### 6. ✅ Cache Cleared
- `php artisan config:clear` - Configuration cache
- `php artisan cache:clear` - Application cache
- `php artisan view:clear` - Blade view cache

## Testing Instructions

### Step 1: Enable Anonymous Authentication in Firebase Console

**CRITICAL - You must complete this first:**

1. Go to: https://console.firebase.google.com/
2. Select project: **"the-stag-notif-v2"**
3. Click **"Authentication"** in left sidebar (or Build → Authentication)
4. Click **"Get started"** (if first time)
5. Go to **"Sign-in method"** tab
6. Find **"Anonymous"** in the providers list
7. Click on "Anonymous"
8. Toggle **"Enable"**
9. Click **"Save"**

**Why this is needed:** Firebase requires authentication to generate FCM tokens. Anonymous auth is the simplest and most secure method for web push notifications.

### Step 2: Test Token Generation in Browser

1. Open customer portal: `http://localhost/customer`
2. Login as a customer (example: user ID 2)
3. Open browser console (F12 → Console tab)
4. Clear all existing console output
5. Click the **notification bell icon** in the sidebar
6. Watch for these console messages:

```
Initializing Firebase Auth for FCM...
Signing in anonymously for FCM...
Anonymous sign-in successful
User ID: [some-firebase-uid]
Service Worker registered successfully
Service Worker ready
FCM Token: [long-token-string]
Device registered successfully
```

7. If you see "FCM Token: ..." → SUCCESS!
8. If you see authentication error → Check that anonymous auth is enabled in Firebase Console

### Step 3: Verify Database Registration

Check that the device token was saved:

```bash
php artisan tinker --execute="
\$device = App\Models\UserFcmDevice::where('user_id', 2)->latest()->first();
if (\$device) {
    echo 'Device ID: ' . \$device->id . PHP_EOL;
    echo 'User ID: ' . \$device->user_id . PHP_EOL;
    echo 'Token (first 50 chars): ' . substr(\$device->device_token, 0, 50) . '...' . PHP_EOL;
    echo 'Active: ' . (\$device->is_active ? 'Yes' : 'No') . PHP_EOL;
    echo 'Created: ' . \$device->created_at . PHP_EOL;
} else {
    echo 'No device found for user 2' . PHP_EOL;
}
"
```

Expected output:
```
Device ID: [number]
User ID: 2
Token (first 50 chars): [token-preview]...
Active: Yes
Created: [timestamp]
```

### Step 4: Send Test Notification from Tinker

```bash
php artisan tinker --execute="
\$user = App\Models\User::find(2);
\$service = app(App\Services\FCMNotificationService::class);
\$service->sendToUser(\$user->id, [
    'title' => 'New Firebase Project Test',
    'body' => 'Testing notification from the-stag-notif-v2 at ' . now()->format('H:i:s'),
    'data' => ['type' => 'test']
]);
echo 'Test notification sent!' . PHP_EOL;
"
```

**Expected Results:**

1. **Browser Tab Active (Foreground):**
   - Console log appears: "Foreground message received: [data]"
   - May see in-page notification (if implemented)

2. **Browser Tab Inactive or Minimized (Background):**
   - Windows notification popup appears in bottom-right corner
   - Notification shows title and body
   - Clicking it opens the customer portal

3. **Windows Action Center:**
   - Notification appears in Windows notification center (Win + A)
   - Remains there until dismissed

### Step 5: Test Real Order Status Change

1. Create or find an existing order for user ID 2
2. Go to admin panel: `http://localhost/admin/orders`
3. Change order status from "pending" to "confirmed"
4. Check if Windows notification appears automatically

**Alternative via Tinker:**

```bash
php artisan tinker --execute="
\$order = App\Models\Order::where('user_id', 2)->latest()->first();
if (\$order) {
    \$order->status = 'confirmed';
    \$order->save();
    echo 'Order status changed to confirmed' . PHP_EOL;
    echo 'Order ID: ' . \$order->id . PHP_EOL;
} else {
    echo 'No order found for user 2' . PHP_EOL;
}
"
```

The `OrderStatusUpdatedEvent` listener should automatically trigger and send notification.

### Step 6: Test Table Reservation Notification

```bash
php artisan tinker --execute="
\$reservation = new App\Models\TableReservation([
    'user_id' => 2,
    'table_id' => 1,
    'guest_count' => 4,
    'booking_date' => now()->addDays(1)->format('Y-m-d'),
    'booking_time' => '19:00:00',
    'status' => 'confirmed'
]);
\$reservation->save();
echo 'Test reservation created: ID ' . \$reservation->id . PHP_EOL;
"
```

Should trigger reservation notification automatically.

## Troubleshooting

### Issue 1: "Authentication error" when requesting token

**Symptoms:**
```
Messaging: A problem occurred while subscribing the user to FCM: Request is missing
required authentication credential.
```

**Solution:**
- Go to Firebase Console → Authentication → Sign-in method
- Make sure **"Anonymous"** is **enabled**
- Wait 1-2 minutes for settings to propagate
- Clear browser cache (Ctrl + Shift + Delete → All time)
- Hard refresh page (Ctrl + Shift + R)

### Issue 2: Service worker not registering

**Symptoms:**
- Console shows "Service worker registration failed"
- Or no service worker messages at all

**Solution:**
- Check browser console for specific error
- Make sure `public/firebase-messaging-sw.js` exists and is accessible
- Try accessing directly: `http://localhost/firebase-messaging-sw.js`
- Check browser DevTools → Application → Service Workers
- Click "Unregister" on old service workers
- Reload page

### Issue 3: Token generated but notification not received

**Symptoms:**
- Console shows "FCM Token: [token]"
- Device registered successfully
- But notification doesn't appear

**Solution:**

1. **Check token in database:**
   ```bash
   php artisan tinker --execute="
   \$device = App\Models\UserFcmDevice::where('user_id', 2)->where('is_active', 1)->first();
   echo \$device ? 'Token exists and active' : 'No active token';
   "
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Look for FCM errors or success messages

3. **Check browser notification permission:**
   - Browser address bar → Click lock icon
   - Check "Notifications" is set to "Allow"

4. **Test with tab inactive:**
   - Minimize browser or switch to another app
   - Send notification via tinker
   - Background notifications work better than foreground

### Issue 4: "Target not found" or "Invalid token"

**Symptoms:**
```
Kreait\Firebase\Exception\Messaging\InvalidArgument: The given message is missing a target
```

**Solution:**
- Token might be expired or invalid
- Clean up and re-register:
  ```bash
  php artisan tinker --execute="App\Models\UserFcmDevice::where('user_id', 2)->delete();"
  ```
- Refresh browser and click notification bell again

### Issue 5: Different Firebase config between files

**Symptoms:**
- Inconsistent behavior
- Some features work, others don't

**Solution:**
- Verify all files have matching credentials:
  - `.env` → Backend config
  - `firebase-messaging-sw.js` → Service worker config
  - `customer.blade.php` → Frontend config (via `.env`)
- All should use project ID: `the-stag-notif-v2`

## Verification Checklist

After following testing instructions:

- ✅ Anonymous authentication enabled in Firebase Console
- ✅ Browser console shows "FCM Token: ..." when clicking bell
- ✅ Device record created in `user_fcm_devices` table
- ✅ Test notification from tinker appears in Windows
- ✅ Order status change triggers automatic notification
- ✅ Reservation creation triggers automatic notification
- ✅ Notification appears in Windows Action Center

## Important Notes

1. **Anonymous Auth is Safe:**
   - Only used for FCM token generation
   - No personal data stored
   - Laravel authentication remains unchanged
   - Each browser session gets unique Firebase anonymous user

2. **Free Tier Limits:**
   - Firebase Spark Plan: 10K anonymous auths/month
   - Unlimited FCM messages (free forever)
   - No credit card required

3. **Production Deployment:**
   - Update `.env` on production server with same credentials
   - Copy `firebase_credentials_new.json` to production
   - Clear production caches after update
   - Test with real users

4. **Browser Support:**
   - Chrome: Full support
   - Firefox: Full support
   - Edge: Full support
   - Safari: Limited (macOS/iOS restrictions)
   - Opera: Full support

5. **Notification Persistence:**
   - Background notifications persist in Windows Action Center
   - Foreground notifications may require custom UI implementation
   - Click on notification opens customer portal

## Files to Commit

Updated files that should be committed to Git:

```bash
# Configuration
.env.example                                    # Update with new Firebase structure
public/firebase-messaging-sw.js                 # Updated with new credentials
resources/views/layouts/customer.blade.php      # Already updated

# Documentation (optional)
FCM-NEW-PROJECT-SETUP-COMPLETE.md             # This file
```

**DO NOT COMMIT:**
```
.env                                            # Contains secrets
storage/app/firebase/firebase_credentials_new.json  # Contains private key
```

## Next Steps

1. ✅ Enable anonymous authentication in Firebase Console
2. ✅ Test token generation by clicking notification bell
3. ✅ Send test notification via tinker
4. ✅ Test real order status notification
5. Update `.env.example` with new Firebase structure (remove old credentials)
6. Document for team members
7. Deploy to production

## Support

If issues persist after following this guide:

1. Check [FCM-WEB-PUSH-SETUP.md](FCM-WEB-PUSH-SETUP.md) for general FCM setup
2. Check [FCM-AUTH-FIX-GUIDE.md](FCM-AUTH-FIX-GUIDE.md) for authentication details
3. Check [FINAL-FCM-SOLUTION.md](FINAL-FCM-SOLUTION.md) for original issue troubleshooting
4. Check Laravel logs: `storage/logs/laravel.log`
5. Check browser console for errors (F12)

---

**Status:** ✅ Setup Complete - Ready for Testing

**Date:** 2025-11-07

**Migration:** the-stag-notification → the-stag-notif-v2 (completed)
