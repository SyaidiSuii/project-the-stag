# 🔍 Firebase Setup Verification Checklist

## Current Status
✅ Anonymous sign-in: WORKING (User ID obtained)
❌ FCM token generation: FAILING (Authentication error)

## Root Cause
The error "Request is missing required authentication credential" when getting FCM token
indicates that the **Firebase Cloud Messaging API is not properly enabled or configured**.

## Critical Steps to Fix (IN ORDER)

### Step 1: Enable Firebase Cloud Messaging API ⭐ MOST IMPORTANT

1. Go to: https://console.cloud.google.com/
2. Login with the same Google account as Firebase
3. **Select project: "the-stag-notification"** (top dropdown)
4. Click **"APIs & Services"** in left sidebar
5. Click **"Enable APIs and Services"** (+ button at top)
6. Search for: **"Firebase Cloud Messaging API"**
7. Click on it
8. Click **"ENABLE"** button
9. Wait 2-3 minutes for API to activate

### Step 2: Verify Cloud Messaging Settings

1. Go back to Firebase Console: https://console.firebase.google.com/
2. Select "the-stag-notification" project
3. Click gear icon → **Project settings**
4. Go to **"Cloud Messaging"** tab
5. Verify you have:
   - ✅ Cloud Messaging API (Enabled)
   - ✅ Server key (for legacy HTTP)
   - ✅ Sender ID
   - ✅ Web Push certificate (Key pair)

### Step 3: Regenerate Web Push Certificate (If Needed)

If the VAPID key is old or missing:

1. In Cloud Messaging tab
2. Scroll to **"Web Push certificates"**
3. If no key pair exists, click **"Generate key pair"**
4. If key exists but issues persist, click **"Rotate key pair"**
5. Copy the new key
6. Update `.env` file with new `FIREBASE_VAPID_KEY`
7. Update `firebase-messaging-sw.js` (not needed, uses config)
8. Update customer layout to use new VAPID key

### Step 4: Check IAM Permissions

1. Go to Google Cloud Console: https://console.cloud.google.com/
2. Select "the-stag-notification"
3. Go to **"IAM & Admin"** → **"IAM"**
4. Find the Firebase service account (ends with @firebase-adminsdk.iam.gserviceaccount.com)
5. Ensure it has these roles:
   - Firebase Cloud Messaging Admin
   - Firebase Admin
   - Service Account Token Creator

### Step 5: Verify App Check is NOT Blocking

1. Firebase Console → **"App Check"** (Build section)
2. Check if enforcement is enabled
3. If yes, either:
   - **Disable enforcement** (for development)
   - Or **configure reCAPTCHA v3** provider

### Step 6: Test After Changes

After completing steps 1-5:

1. Wait 5 minutes for changes to propagate
2. Clear browser cache (Ctrl + Shift + Delete)
3. Close and reopen browser
4. Go to: http://localhost/test-fcm-auth-fix.html
5. Click "Test with Anonymous Auth"
6. Should now succeed!

## Alternative: Use Different Firebase Project

If the above doesn't work, the project might have restrictive settings. Consider:

1. Create a NEW Firebase project
2. Enable FCM from the start
3. Configure it properly
4. Update all credentials in Laravel

## Common Issues

### Issue: "Firebase Cloud Messaging API" not found in Cloud Console

**Solution:**
- Make sure you're in the correct project (top dropdown)
- Try searching for just "FCM API" or "Cloud Messaging"
- Check if it's already enabled under "APIs & Services" → "Enabled APIs"

### Issue: Permission denied when enabling API

**Solution:**
- You need Owner or Editor role in Google Cloud project
- Ask project owner to enable the API
- Or ask owner to grant you "Project Editor" role

### Issue: API enabled but still getting error

**Solution:**
- Wait 5-10 minutes for changes to propagate
- Clear ALL browser data (not just cache)
- Try in incognito/private window
- Check if VAPID key is correct

## Quick Verification Commands

Check if device token exists in database:
```sql
SELECT * FROM user_fcm_devices WHERE user_id = 2 ORDER BY id DESC LIMIT 1;
```

Check Laravel logs:
```bash
tail -50 storage/logs/laravel-2025-11-07.log | grep FCM
```

Check if Firebase credentials are loaded:
```bash
php artisan tinker --execute="
echo 'Project ID: ' . config('services.fcm.project_id') . PHP_EOL;
echo 'VAPID Key: ' . substr(config('services.fcm.vapid_key'), 0, 20) . '...' . PHP_EOL;
"
```

## Expected Outcome

After fixing, you should see:
```
[timestamp] Starting test WITH anonymous authentication...
[timestamp] ✓ Signed in with user ID: [uid]
[timestamp] ✓ Service worker ready
[timestamp] ✅ SUCCESS! Token obtained: [long-token-string]
```

Then notifications will work! 🎉

