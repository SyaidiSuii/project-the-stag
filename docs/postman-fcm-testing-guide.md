# 📱 Postman Testing Guide - FCM API

## 🔑 Authentication Setup

**Base URL:** http://thestag.shop (or your local domain)

### Step 1: Get API Token
1. First, login to get a token:
   ```
   POST /api/auth/login
   Body:
   {
     "email": "your-email@example.com",
     "password": "your-password"
   }
   ```

2. Copy the `access_token` from the response

3. In Postman:
   - Go to **Authorization** tab
   - Select **Bearer Token**
   - Paste your access token

---

## 📋 API Endpoints Testing

### 1. Register Device Token
**Endpoint:** `POST /api/fcm/register`

**Headers:**
```
Content-Type: application/json
Authorization: Bearer YOUR_ACCESS_TOKEN
```

**Body:**
```json
{
  "device_token": "your-device-fcm-token-here",
  "device_type": "web",
  "platform": "Chrome",
  "browser": "Chrome",
  "version": "120.0"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Device registered successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "device_token": "your-device-fcm-token-here",
    "device_type": "web",
    "platform": "Chrome",
    "browser": "Chrome",
    "version": "120.0",
    "is_active": true,
    "created_at": "2025-11-06T...",
    "updated_at": "2025-11-06T..."
  }
}
```

---

### 2. Get User Devices
**Endpoint:** `GET /api/fcm/devices`

**Headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "device_token": "your-device-fcm-token-here",
      "device_type": "web",
      "platform": "Chrome",
      "browser": "Chrome",
      "version": "120.0",
      "is_active": true,
      "last_used_at": "2025-11-06T...",
      "created_at": "2025-11-06T..."
    }
  ]
}
```

---

### 3. Send Notification to Self
**Endpoint:** `POST /api/fcm/send`

**Body:**
```json
{
  "title": "Test Notification",
  "body": "This is a test notification from The Stag!",
  "type": "order",
  "data": {
    "order_id": "123",
    "action_url": "/orders/123"
  }
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Notification queued for delivery",
  "data": {
    "notification_id": "fcm-xxxxx-xxxxx-xxxxx",
    "title": "Test Notification",
    "body": "This is a test notification from The Stag!",
    "type": "order",
    "recipient_count": 1
  }
}
```

---

### 4. Get Notification History
**Endpoint:** `GET /api/fcm/history`

**Query Parameters (optional):**
- `limit=10` (number of results)
- `type=order` (filter by type)

**Example:** `/api/fcm/history?limit=10&type=order`

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "title": "Test Notification",
      "body": "This is a test notification from The Stag!",
      "type": "order",
      "status": "sent",
      "data": {
        "order_id": "123"
      },
      "fcm_message_id": "fcm-xxxxx-xxxxx-xxxxx",
      "created_at": "2025-11-06T..."
    }
  ]
}
```

---

### 5. Deactivate Device
**Endpoint:** `DELETE /api/fcm/devices/{deviceId}`

**Example:** `/api/fcm/devices/1`

**Expected Response:**
```json
{
  "success": true,
  "message": "Device deactivated successfully"
}
```

---

## 🔐 Admin-Only Endpoints

*Note: Admin endpoints require admin role*

### 6. Get FCM Statistics
**Endpoint:** `GET /api/fcm/statistics`

**Headers:**
```
Authorization: Bearer YOUR_ADMIN_ACCESS_TOKEN
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "total_devices": 5,
    "active_devices": 4,
    "devices_by_type": {
      "web": 3,
      "android": 1,
      "ios": 1
    },
    "recent_notifications": 12,
    "notifications_by_type": {
      "order": 8,
      "reservation": 3,
      "promotion": 1
    },
    "success_rate": 95.5
  }
}
```

---

### 7. Test FCM Connection
**Endpoint:** `POST /api/fcm/test`

**Body:**
```json
{
  "title": "FCM Test",
  "body": "Checking Firebase connection...",
  "target": "admin"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "FCM connection test successful",
  "data": {
    "firebase_project_id": "the-stag-notification",
    "test_message_id": "fcm-test-xxxxx",
    "timestamp": "2025-11-06T..."
  }
}
```

---

## 🎯 Complete Testing Workflow

### Test Sequence:

1. **Login** → Get access token
2. **Register Device** → Register FCM token for testing
3. **Send Test Notification** → Verify notification delivery
4. **Check History** → Verify notification was logged
5. **Get Devices** → Verify device is registered
6. **Get Statistics** → Check overall stats (if admin)
7. **Test Connection** → Verify Firebase connection (if admin)

---

## 🔍 How to Get FCM Device Token

### For Web Push Notifications:

1. **Open your browser's Developer Tools** (F12)
2. **Go to Console tab**
3. **Paste this code:**

```javascript
// Request notification permission
Notification.requestPermission().then((permission) => {
  if (permission === 'granted') {
    console.log('Notification permission granted');
    
    // Get FCM token (you need Firebase SDK loaded)
    // This is for when you integrate with your web app
    getToken().then((currentToken) => {
      if (currentToken) {
        console.log('FCM Token:', currentToken);
      }
    });
  }
});
```

4. **Copy the token** from console output
5. **Use it in Postman** for testing

---

## 📊 Monitoring in Firebase Console

After sending notifications, check delivery status:

1. Go to: https://console.firebase.google.com/
2. Select project: **the-stag-notification**
3. Navigate to: **Cloud Messaging**
4. Click: **Message History**
5. See sent messages and delivery stats

---

## ⚠️ Common Issues & Solutions

### Error: "Unauthenticated"
- **Solution:** Ensure Bearer token is set in Authorization header

### Error: "Device token invalid"
- **Solution:** Use a valid FCM token from Firebase

### Error: "Insufficient permissions"
- **Solution:** Use admin token for statistics/test endpoints

### Error: "Firebase connection failed"
- **Solution:** Check Firebase credentials file exists at storage/app/firebase/firebase_credentials.json

---

## ✅ Success Indicators

- ✅ Status code: 200 or 201
- ✅ "success": true in response
- ✅ Data objects returned
- ✅ No error messages
- ✅ Check Firebase Console for sent messages

---

**Happy Testing! 🎉**
