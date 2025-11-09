# Firebase Cloud Messaging (FCM) Setup Guide

## Step-by-Step Guide untuk Setup Firebase Project

### **Step 1: Create Firebase Project**

1. Pergi ke [Firebase Console](https://console.firebase.google.com/)
2. Click **"Create a project"**
3. Enter project name: `the-stag-smartdine` (atau nama lain pilihan anda)
4. Enable Google Analytics: **Optional** (tapi recommended untuk insights)
5. Click **"Create project"**

### **Step 2: Register Web App**

1. Dalam Firebase Console, click **"Add app"** (web icon)
2. Enter app nickname: `The Stag Web App`
3. **✅ Check** "Also set up Firebase Hosting for this app" (jika anda guna hosting)
4. Click **"Register app"**

### **Step 3: Get Configuration Values**

Copy semua configuration values yang diberikan:

```javascript
const firebaseConfig = {
  apiKey: "your-api-key",
  authDomain: "your-project.firebaseapp.com",
  projectId: "your-project-id",
  storageBucket: "your-project.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abcdef123456",
};
```

### **Step 4: Enable Cloud Messaging**

1. Pergi ke **"Build"** > **"Cloud Messaging"** dalam sidebar
2. Click **"Get started"**
3. Enable **"Web Push certificates"** tab
4. Generate **VAPID key** (Web push certificates):
   - Click **"Generate key pair"**
   - Copy the **VAPID key** (long string starting dengan `BD` atau `BN`)

### **Step 5: Generate Service Account Key**

1. Pergi ke **"Project Settings"** (gear icon)
2. Pilih tab **"Service Accounts"**
3. Click **"Generate new private key"**
4. Confirm dan download JSON file
5. Rename file kepada `firebase-service-account.json`
6. Place file dalam Laravel project root directory: `the_stag/firebase-service-account.json`

### **Step 6: Update Environment Variables**

Copy semua values dari Firebase Console dan update `.env` file:

```bash
# Firebase Configuration
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

### **Step 7: Configure Laravel**

1. Make sure `firebase-service-account.json` file ada dalam project root
2. Update `.env` file dengan semua values dari Step 6
3. Run command untuk refresh config:

```bash
php artisan config:clear
php artisan config:cache
```

### **Step 8: Test FCM Connection**

Test dengan command ini untuk verify connection:

```bash
php artisan tinker
```

Then run:

```php
app(\Kreait\Firebase\Contract\Messaging::class)->validateRegistrationTokens(['test-token']);
```

---

## **Configuration Values Reference**

| Variable | Description | Where to Find |
|----------|-------------|---------------|
| `FIREBASE_PROJECT_ID` | Unique project identifier | Firebase Console > Project Settings |
| `FIREBASE_DATABASE_URL` | Realtime Database URL | Firebase Console > Project Settings > General |
| `FIREBASE_STORAGE_BUCKET` | Cloud Storage bucket | Firebase Console > Project Settings > General |
| `FIREBASE_MESSAGING_SENDER_ID` | FCM Sender ID | Firebase Console > Cloud Messaging |
| `FIREBASE_APP_ID` | Application ID | Firebase Console > Project Settings > General |
| `FIREBASE_VAPID_KEY` | Web push certificate key | Firebase Console > Cloud Messaging > Web Push certificates |
| `FIREBASE_SERVICE_ACCOUNT_PATH` | Path ke service account JSON | Generated dalam Step 5 |

---

## **Security Notes**

⚠️ **Important Security Practices:**

1. **Never commit** `firebase-service-account.json` to git
2. **Add** `firebase-service-account.json` dalam `.gitignore`:

```gitignore
# Firebase
firebase-service-account.json
```

3. **Use** environment variables untuk production deployment
4. **Restrict** API keys dalam Firebase Console Security Rules
5. **Regularly rotate** service account keys

---

## **Troubleshooting**

### **Common Issues & Solutions**

#### Issue: "Permission denied" error
**Solution:**
- Check service account path dalam `.env`
- Verify JSON file validity
- Ensure file permissions are correct

#### Issue: "Invalid registration token"
**Solution:**
- Token might have expired
- Device might have unregistered
- Check token format (should be long string)

#### Issue: Notifications not showing
**Solution:**
- Check browser notification permissions
- Verify VAPID key is correct
- Check console for JavaScript errors
- Ensure service worker is registered

#### Issue: "Project ID mismatch"
**Solution:**
- Verify `FIREBASE_PROJECT_ID` matches actual project ID
- Check all config values are from same Firebase project

---

## **Next Steps**

After successful setup:
1. ✅ FCM integration akan active
2. ✅ Automatic notifications akan working
3. ✅ Admin boleh send manual promotional notifications
4. ✅ Web push notifications akan berfungsi pada customer portal

---

## **Support Resources**

- [Firebase Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Laravel Firebase Package Docs](https://kreait.com/laravel-firebase/)
- [FCM Web Push Guide](https://firebase.google.com/docs/cloud-messaging/js/receive-webpush-apps)
- [Firebase Console](https://console.firebase.google.com/)

---

**Setup Complete!** 🎉

Your notification system sekarang ready untuk testing dan deployment!
