# Cara Enable Firebase Cloud Messaging API

## Issue Anda Sekarang
Error: "Request is missing required authentication credential"
Maksudnya: **Firebase Cloud Messaging API BELUM enabled**

## Langkah-Langkah Exact

### Step 1: Go to Google Cloud Console
URL: https://console.cloud.google.com/

### Step 2: Select Project
- Atas sekali ada dropdown project
- Pilih: **"the-stag-notification"**

### Step 3: Go to API Library
- Sidebar kiri → **"APIs & Services"**
- Click **"Library"**
- Atau direct link: https://console.cloud.google.com/apis/library?project=the-stag-notification

### Step 4: Search for FCM API
Dalam search box, type: **"Firebase Cloud Messaging API"**

OR click direct link:
```
https://console.cloud.google.com/apis/library/fcm.googleapis.com?project=the-stag-notification
```

### Step 5: Enable API
- Click pada **"Firebase Cloud Messaging API"**
- Click button besar **"ENABLE"**
- Tunggu loading (2-3 minit)
- Should show "API enabled" message

### Step 6: Enable Additional APIs (Optional but Recommended)
Enable jugak yang ni:
1. FCM Registration API
2. Firebase Cloud Messaging Data API

### Step 7: Verify
Check if enabled:
1. Go to: https://console.cloud.google.com/apis/dashboard?project=the-stag-notification
2. Dalam "Enabled APIs & services"
3. Should see **"Firebase Cloud Messaging API"** dalam list

### Step 8: Wait & Test
1. **Tunggu 5 minit** untuk changes propagate
2. **Clear browser cache** (Ctrl + Shift + Delete → Clear all)
3. **Close dan reopen browser**
4. Buka: http://localhost/test-simple-fcm.html
5. Click "Get Token (No Auth)"
6. Should SUCCESS dapat token!

## Kalau Masih Error

### Check 1: Billing Account
FCM API sometimes requires billing enabled (even for free tier)

1. Go to: https://console.cloud.google.com/billing?project=the-stag-notification
2. Make sure ada billing account linked
3. Even kalau guna free tier, kena link billing account

### Check 2: Project Permissions
Make sure account anda ada permission:
- Go to: https://console.cloud.google.com/iam-admin/iam?project=the-stag-notification
- Check your email ada role "Owner" atau "Editor"

### Check 3: Use gcloud CLI
Kalau ada gcloud installed:

```bash
gcloud auth login
gcloud config set project the-stag-notification
gcloud services enable fcm.googleapis.com
gcloud services enable fcmregistrations.googleapis.com
```

## Expected Result After Enable

### Before Enable:
```
❌ ERROR: Request is missing required authentication credential
```

### After Enable:
```
✅ SUCCESS! API is enabled!
Token: eib3k4EJOvgKFF9ZmFro...
```

## Next Steps After API Enabled

1. ✅ Test simple FCM → PASS
2. ✅ Go to customer portal: http://localhost/customer
3. ✅ Click notification bell
4. ✅ Should get token automatically
5. ✅ Send test notification dari tinker
6. ✅ Notification popup di Windows!

## Need Help?

Kalau after enable masih error:
1. Screenshot error message
2. Screenshot Google Cloud Console showing "API enabled"
3. Check Laravel logs: `tail -50 storage/logs/laravel-2025-11-07.log`

---

**PENTING:** Masalah anda 100% sebab API belum enabled. Once enabled, everything will work!
