# New Firebase Project Setup Checklist

## ✅ Checklist - Complete in Order

### Step 1: Create Project ✅
- [x] Go to https://console.firebase.google.com/
- [x] Click "Add project"
- [x] Project name: (your choice)
- [x] Skip Google Analytics OR disable billing prompts
- [x] Select **Spark Plan (FREE)**

### Step 2: Add Web App ⬅️ YOU ARE HERE
- [ ] Project settings → Your apps
- [ ] Click Web icon (</>)
- [ ] App nickname: **"The Stag Web"** or **"The Stag SmartDine"**
- [ ] **DON'T** check "Also set up Firebase Hosting" (unless you want it)
- [ ] Click "Register app"
- [ ] **COPY** the Firebase config object
- [ ] Save config ke tempat selamat (notepad/text file)

Example config format:
```javascript
{
  apiKey: "AIza...",
  authDomain: "project-name.firebaseapp.com",
  projectId: "project-name",
  storageBucket: "project-name.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abc123"
}
```

### Step 3: Enable Authentication
- [ ] Sidebar → Build → **Authentication**
- [ ] Click "Get started"
- [ ] Go to **"Sign-in method"** tab
- [ ] Find **"Anonymous"** in list
- [ ] Click on it
- [ ] Toggle **"Enable"**
- [ ] Click "Save"

### Step 4: Get VAPID Key (Web Push Certificate)
- [ ] Gear icon (⚙️) → **Project settings**
- [ ] Go to **"Cloud Messaging"** tab
- [ ] Scroll to **"Web Push certificates"** section
- [ ] If no key exists, click **"Generate key pair"**
- [ ] If key exists, use existing key
- [ ] **COPY** the VAPID Key (starts with "B...")
- [ ] Save ke tempat selamat

Example VAPID key format:
```
BHl0j5uyqrfxnV8DYpFXEvSDV2gJY1YPgTHY4iYaZPB1eeBJ8-FYd3c3lB2gqPe2V_cUXGS3P6XwdJpROaYrtvk
```

### Step 5: Download Service Account JSON
- [ ] Still in Project settings
- [ ] Go to **"Service accounts"** tab
- [ ] Click **"Generate new private key"** button
- [ ] Confirm by clicking "Generate key"
- [ ] JSON file will download automatically
- [ ] **SAVE** file to: `D:\ProgramsFiles\laragon\www\the_stag\storage\app\firebase\`
- [ ] Rename to: `firebase_credentials_new.json`

### Step 6: Collect All Credentials

After completing above steps, you should have:

1. **Firebase Config** (from Step 2):
   ```
   apiKey:
   authDomain:
   projectId:
   storageBucket:
   messagingSenderId:
   appId:
   ```

2. **VAPID Key** (from Step 4):
   ```
   Starts with "B..."
   ```

3. **Service Account JSON** (from Step 5):
   ```
   Saved in storage/app/firebase/firebase_credentials_new.json
   ```

### Step 7: Ready for Laravel Update

Once you have all 3 items above, let me know and I will:
- Generate exact .env updates
- Update all frontend files
- Update service worker
- Create migration script
- Test everything

---

## After Collecting Credentials

Send me these 3 things:
1. Firebase Config (the JavaScript object)
2. VAPID Key (the long string starting with B)
3. Confirm service account JSON saved

Then I'll generate all the code updates for you automatically!

---

## Troubleshooting

### Can't find "Cloud Messaging" tab?
- It's in Project settings, not in Build section
- Look for tabs: General, Service accounts, Cloud Messaging, etc.

### No "Generate key pair" button?
- Scroll down in Cloud Messaging tab
- Look for "Web Push certificates" section
- If button not there, check if key already exists

### Service account download failed?
- Try different browser
- Check popup blocker
- Try Chrome Incognito mode

---

**NEXT**: Complete Steps 2-6 above, then share credentials dengan saya untuk auto-update!
