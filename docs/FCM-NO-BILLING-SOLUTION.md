# FCM Solution WITHOUT Billing Account

## Problem
Card ditolak bila cuba link billing account.

## Solution: Use Firebase Spark Plan (FREE)

Firebase ada plan percuma yang tak perlu billing account!

### Step 1: Check Current Plan

1. Go to: https://console.firebase.google.com/
2. Select "the-stag-notification"
3. Click gear icon (⚙️) → **"Usage and billing"**
4. Check current plan

### Step 2: Ensure Spark Plan (Free)

If not on Spark plan:
1. Click **"Details & settings"**
2. Look for **"Modify plan"** or **"Downgrade plan"**
3. Select **"Spark plan"** (FREE)
4. Confirm

Spark Plan includes:
- ✅ Unlimited FCM notifications
- ✅ Authentication (10K users)
- ✅ Realtime Database (1GB)
- ✅ Cloud Functions (125K invocations/month)
- ✅ NO CREDIT CARD REQUIRED!

### Step 3: Re-enable APIs Without Billing

Once on Spark plan, try enabling APIs again:

1. Go to: https://console.cloud.google.com/apis/library?project=the-stag-notification
2. Search: "Firebase Cloud Messaging API"
3. Enable it
4. Should work without billing prompt!

## Alternative: Create New Firebase Project (Fresh Start)

If above doesn't work, create new project from scratch:

### Create New Project

1. Go to: https://console.firebase.google.com/
2. Click **"Add project"**
3. Name: "the-stag-notification-free"
4. **IMPORTANT**: When asked about billing, select **"Not right now"** or **"Skip"**
5. Choose **Spark plan (FREE)**
6. Disable Google Analytics (to avoid billing prompts)
7. Create project

### Configure New Project

1. **Add Web App**:
   - Project settings → Your apps → Web icon
   - Nickname: "The Stag Web"
   - Register app
   - Copy Firebase config

2. **Enable Authentication**:
   - Build → Authentication → Get started
   - Sign-in method → Anonymous → Enable

3. **Enable Cloud Messaging**:
   - Already enabled by default in Spark plan!

4. **Generate Web Push Certificate**:
   - Project settings → Cloud Messaging
   - Web Push certificates → Generate key pair
   - Copy VAPID key

5. **Download Service Account**:
   - Project settings → Service accounts
   - Generate new private key
   - Download JSON file

### Update Laravel .env

Update dengan credentials baru:

```env
FIREBASE_PROJECT_ID=the-stag-notification-free
FIREBASE_DATABASE_URL=https://the-stag-notification-free.firebaseio.com
FIREBASE_STORAGE_BUCKET=the-stag-notification-free.appspot.com
FIREBASE_MESSAGING_SENDER_ID=[new-sender-id]
FIREBASE_APP_ID=[new-app-id]
FIREBASE_VAPID_KEY=[new-vapid-key]
FIREBASE_SERVICE_ACCOUNT_PATH=./storage/app/firebase/firebase_credentials_new.json
```

### Update Frontend Config

Update `resources/views/layouts/customer.blade.php`:

```javascript
window.FIREBASE_CONFIG = {
    apiKey: "[new-api-key]",
    authDomain: "the-stag-notification-free.firebaseapp.com",
    projectId: "the-stag-notification-free",
    storageBucket: "the-stag-notification-free.appspot.com",
    messagingSenderId: "[new-sender-id]",
    appId: "[new-app-id]"
};

window.FIREBASE_VAPID_KEY = "[new-vapid-key]";
```

### Update Service Worker

Update `public/firebase-messaging-sw.js`:

```javascript
firebase.initializeApp({
    apiKey: "[new-api-key]",
    authDomain: "the-stag-notification-free.firebaseapp.com",
    projectId: "the-stag-notification-free",
    storageBucket: "the-stag-notification-free.appspot.com",
    messagingSenderId: "[new-sender-id]",
    appId: "[new-app-id]"
});
```

### Test New Project

```bash
php artisan config:clear
php artisan cache:clear
```

Then test:
1. http://localhost/test-simple-fcm.html
2. Click "Get Token (No Auth)"
3. Should work WITHOUT billing!

## Why This Works

Spark Plan (FREE):
- No credit card needed
- Perfect for development & small apps
- FCM is completely free (unlimited notifications)
- Only need billing for:
  - Cloud Functions > 125K/month
  - Realtime DB > 1GB
  - Storage > 5GB
  - (You won't hit these limits!)

## Alternative Method: Use Different Card

If you want to keep current project:

### Try These Card Options:

1. **Maybank Debit Card** - Usually accepted
2. **CIMB Debit Card** - Works with Google
3. **Touch 'n Go eWallet Visa** - Virtual card
4. **BigPay Virtual Card** - Easy to get
5. **Grab PayLater Card** - If you have Grab account

### Or Ask Team Member

If working in team:
- Ask colleague with valid card to link billing
- They can remove themselves after linking
- You can continue as project owner

## Recommendation

**BEST OPTION**: Create new Firebase project dengan Spark Plan (FREE)

Advantages:
- No billing issues
- Fresh start with correct config
- Takes only 15 minutes
- Guaranteed to work

Disadvantages:
- Need update all credentials
- Need migrate existing data (if any)

**But for FCM only, this is the EASIEST solution!**

