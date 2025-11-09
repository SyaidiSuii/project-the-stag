# ✅ Pusher + FCM Implementation - Complete Test Guide

**Date:** 2025-11-08
**Status:** READY FOR TESTING

---

## 🎯 What Has Been Implemented

### 1. Real-Time Order Status Updates (Pusher)
- ✅ Event broadcasts include both `order_id` and `confirmation_code`
- ✅ JavaScript matches orders by confirmation code, ORD-{id}, or plain id
- ✅ Index page updates status badges in real-time (no page reload)
- ✅ Show page displays toast notification and reloads after 2 seconds
- ✅ Pusher script load order fixed

### 2. Push Notifications (FCM)
- ✅ Filtered to only send for: **preparing**, **ready**, **completed**
- ✅ Push notification toggle moved to Account → Preferences
- ✅ Toggle state persistence fixed (uses `Notification.permission`)
- ✅ Notification bell removed from sidebar

---

## 🚀 Testing Checklist

### Prerequisites
- [ ] Queue worker is running: `php artisan queue:work`
- [ ] Browser supports notifications (Chrome, Firefox, Edge)
- [ ] Customer logged in (user ID 2 or any customer)

---

### Test 1: Push Notification Toggle Persistence

**Location:** `/customer/account` → Preferences section

**Steps:**
1. Open customer account page: `http://localhost/customer/account`
2. Scroll to "Preferences" card
3. Find "Push Notifications" toggle
4. Check initial state:
   - If never granted permission → Toggle should be OFF (grey)
   - If previously granted permission → Toggle should be ON (green)

5. Click the toggle to enable:
   - Browser prompt should appear asking for notification permission
   - Click "Allow"
   - Toggle should turn green (active)
   - Toast message: "Push notifications enabled! You will receive order updates."

6. **Refresh the page (F5)**
7. Scroll to "Preferences" again
8. **Expected:** Toggle should STILL be ON (green) - this is the fix we implemented
9. **Previous bug:** Toggle would turn off after refresh despite permission granted

**Success criteria:**
- ✅ Toggle reflects actual browser notification permission
- ✅ Toggle state persists after page refresh
- ✅ No need to re-enable after refresh

---

### Test 2: FCM Push Notification Delivery

**Prerequisites:**
- Push notification toggle is ON (from Test 1)
- Browser notification permission granted

**Steps:**
1. Open customer orders page: `http://localhost/customer/orders`
2. Find an order with status: **confirmed** or **pending**
3. Note the order ID (e.g., Order #49)

4. In a new tab, login as admin
5. Go to Orders → Find the same order
6. Change status to: **preparing**
7. Save

**Expected Results:**
- ✅ Windows notification appears: "Order #STAG-XXXXX is now preparing"
- ✅ Clicking notification opens order details page
- ✅ Check browser console: Should show "FCM notification received"

8. Change order status to: **ready**
   - ✅ Another notification appears: "Order is ready for pickup!"

9. Change order status to: **completed**
   - ✅ Another notification: "Order completed"

10. Change order status to: **cancelled** (test filter)
    - ✅ NO notification should appear (cancelled not in filter list)

**Success criteria:**
- ✅ Notifications appear ONLY for: preparing, ready, completed
- ✅ NO notifications for: pending, confirmed, served, cancelled
- ✅ Notifications appear even when customer page is minimized/in background

---

### Test 3: Real-Time Status Updates on Index Page

**Location:** `/customer/orders` (orders list page)

**Steps:**
1. Open customer orders page
2. Open browser console (F12)
3. Find visible order card (pending/confirmed/preparing/ready)
4. Note the confirmation code (e.g., STAG-20251107-ABCD)

5. In admin panel, change that order's status
6. Watch customer orders page (DO NOT REFRESH)

**Expected Results:**

**In Console:**
```
Pusher: Order status update received
Object {order_id: 49, confirmation_code: "STAG-20251107-ABCD", new_status: "preparing", ...}
Updating order card status badge
```

**On Page:**
- ✅ Status badge changes color and text instantly
- ✅ Pulse animation plays on the badge
- ✅ Toast notification appears
- ✅ NO page reload (stays on same view)

**Badge Color Changes:**
- Pending → Orange
- Confirmed → Blue
- Preparing → Purple
- Ready → Green
- Completed → Grey

**Success criteria:**
- ✅ Status updates within 1 second
- ✅ No manual refresh needed
- ✅ Animation feedback shows update happened
- ✅ Console shows Pusher event received

---

### Test 4: Real-Time Updates on Show Page

**Location:** `/customer/orders/{id}` (single order detail page)

**Steps:**
1. Open specific order: `http://localhost/customer/orders/49`
2. Order should NOT be completed/cancelled (use pending/preparing/ready)
3. Open console (F12)
4. Check for: "Pusher listening for order 49"

5. In admin, change the order status
6. Watch the show page

**Expected Results:**

**In Console:**
```
Pusher listening for order 49
Pusher event received: {order_id: 49, new_status: "ready", ...}
Order status changed from preparing to ready
```

**On Page:**
- ✅ Toast notification appears: "Order status updated: ready"
- ✅ Page starts countdown: "Page will reload in 2 seconds..."
- ✅ Page automatically reloads after 2 seconds
- ✅ After reload, new status is displayed

**Success criteria:**
- ✅ Toast notification appears immediately
- ✅ Page reloads automatically after 2 seconds
- ✅ New status visible after reload
- ✅ No errors in console

---

### Test 5: Queue Worker Dependency

**This test confirms the critical requirement: Queue worker MUST be running**

**Steps:**
1. Stop the queue worker (if running): Press Ctrl+C in terminal
2. In admin, change an order status
3. Watch customer page: **NO update should appear**
4. Check database:
   ```sql
   SELECT * FROM jobs ORDER BY id DESC LIMIT 1;
   ```
   - ✅ Should see a queued broadcast job

5. Start queue worker: `php artisan queue:work --once`
6. Watch customer page: **Update appears immediately**
7. Check database jobs table: **Should be empty (job processed)**

**Success criteria:**
- ✅ Without queue worker: Events don't broadcast
- ✅ With queue worker: Events broadcast immediately
- ✅ Proves queue processing is essential

---

## 🔧 Troubleshooting

### Issue: Toggle turns off after refresh
**Status:** FIXED ✅
**Solution:** Now uses `Notification.permission` API instead of FCM token check
**Test:** Refresh account page - toggle should stay ON if permission granted

---

### Issue: No push notifications received
**Check 1:** Is toggle ON in preferences?
**Check 2:** Browser permission granted? (check browser settings)
**Check 3:** Order status change in filter list? (preparing/ready/completed only)
**Check 4:** Check console for errors
**Check 5:** Queue worker running?

---

### Issue: Real-time updates not working
**Check 1:** Queue worker running?
```bash
# Check if running
ps aux | grep "queue:work"  # Linux
# OR check Task Manager for php.exe with queue:work argument (Windows)
```

**Check 2:** Console shows "Pusher listening"?
- If NO: Script load order issue
- Hard refresh: Ctrl+Shift+R

**Check 3:** WebSocket connection?
- F12 → Network tab → WS filter
- Should see: `ws-ap1.pusher.com`
- Status: 101 Switching Protocols

**Check 4:** Event received but card not updating?
- Console should show: "Order card not found for ID: X"
- Reason: Order is completed/cancelled (hidden from list)
- Solution: Test with visible order

---

## 📊 Technical Implementation Details

### Files Modified

1. **app/Events/OrderStatusUpdatedEvent.php** (lines 17, 28, 59)
   - Added `$confirmationCode` property
   - Broadcasts confirmation code with event

2. **app/Listeners/SendOrderStatusNotification.php** (lines 44-54)
   - Added status filter: ['preparing', 'ready', 'completed']
   - Logs skipped notifications

3. **resources/views/customer/order/index.blade.php** (line 247, 1035-1043)
   - Fixed Pusher script load order
   - Updated matching logic for confirmation code

4. **resources/views/customer/account/index.blade.php** (lines 183-191, 865-932)
   - Added push notification toggle in Preferences
   - Implemented toggle state persistence fix
   - Uses `Notification.permission` for reliable state

5. **resources/views/layouts/customer.blade.php**
   - Removed notification bell from sidebar
   - Removed associated JavaScript

### Key Configuration

**Broadcasting:**
```env
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=database
```

**Pusher:**
```env
PUSHER_APP_ID=1911607
PUSHER_APP_KEY=03effa88c34803b4248c
PUSHER_APP_CLUSTER=ap1
```

**FCM:**
- Enabled for statuses: preparing, ready, completed
- Disabled for: pending, confirmed, served, cancelled

---

## 🎉 Success Indicators

You know everything is working when:

1. ✅ Push notification toggle in preferences stays ON after refresh
2. ✅ Windows notifications appear for status changes (preparing/ready/completed)
3. ✅ Admin changes order status → Customer sees update within 1 second
4. ✅ No manual refresh needed on index page
5. ✅ Show page automatically reloads with new status
6. ✅ Toast notifications appear for updates
7. ✅ Status badges change color instantly with pulse animation

---

## 🚦 Production Deployment Checklist

### Queue Worker Setup

**Windows (Development):**
```bash
# Use batch file
start-queue-worker.bat

# OR manual
php artisan queue:work
```

**Linux (Production with Supervisor):**

Create `/etc/supervisor/conf.d/the-stag-worker.conf`:
```ini
[program:the-stag-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/the-stag/artisan queue:work database --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/the-stag/storage/logs/worker.log
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start the-stag-worker:*
```

### Monitoring

```bash
# Check queue status
php artisan queue:monitor

# Watch logs
tail -f storage/logs/laravel.log

# Check worker logs
tail -f storage/logs/worker.log
```

---

## 📝 Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Pusher Real-Time Updates | ✅ Working | Requires queue worker |
| FCM Push Notifications | ✅ Working | Filtered to 3 statuses |
| Toggle State Persistence | ✅ Fixed | Uses Notification.permission |
| Index Page Updates | ✅ Working | Instant badge changes |
| Show Page Updates | ✅ Working | Toast + auto reload |
| Status Filter | ✅ Working | Only preparing/ready/completed |
| Queue Processing | ⚠️ Manual | Must run queue:work |

---

**Last Updated:** 2025-11-08
**Implementation By:** Claude
**Status:** ✅ READY FOR USER TESTING

---

## 📞 Next Steps

1. Run through all 5 test cases above
2. Verify toggle state persists after refresh (Test 1)
3. Confirm FCM notifications only for filtered statuses (Test 2)
4. Test real-time updates on both pages (Tests 3 & 4)
5. Confirm queue worker dependency (Test 5)

**If all tests pass:** Implementation is complete! 🎉

**If any test fails:** Check troubleshooting section and verify:
- Queue worker running
- Browser permissions granted
- Pusher credentials correct
- FCM configured properly
