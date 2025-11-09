# 🧪 Cara Test Pusher Real-Time Updates

## Bug Yang Dah Fixed
✅ **Pusher script load order fixed** - Script CDN sekarang load dulu sebelum code yang guna Pusher

---

## Cara Test (Mudah)

### Step 1: Buka Customer Order Page

Pilih salah satu:

**Option A: Order Show Page**
```
http://localhost/customer/orders/27
```
(Ganti `27` dengan order ID yang ada)

**Option B: Orders List Page**
```
http://localhost/customer/orders
```

### Step 2: Buka Browser Console

Tekan **F12** → Tab **Console**

Anda patut nampak:
```
Pusher: Listening for order status updates on kitchen-display channel
```

### Step 3: Test Real-Time Update

**Cara 1: Guna Test Script (Paling Mudah)**
```bash
cd D:\ProgramsFiles\laragon\www\the_stag
php test-pusher-debug.php
```

**Cara 2: Guna Admin Panel**
1. Buka tab baru → Login sebagai admin
2. Pergi ke Orders → Pilih order yang sama
3. Klik "Update Status"
4. Tukar status (contoh: Pending → Confirmed)
5. Save

**Cara 3: Guna Tinker**
```bash
php artisan tinker

$order = App\Models\Order::find(27);
$oldStatus = $order->order_status;
$order->order_status = 'confirmed';
$order->save();
event(new App\Events\OrderStatusUpdatedEvent($order, $oldStatus, 'admin'));
```

### Step 4: Verify Update Received

Di browser console, anda patut nampak:

**✅ Show Page:**
```
Pusher event received: {order_id: 27, new_status: "confirmed", old_status: "pending", ...}
Order status changed: pending → confirmed
```
- Toast notification keluar
- Page reload lepas 2 saat

**✅ Index Page:**
```
Pusher: Order status update received {order_id: 27, new_status: "confirmed", ...}
Updating order card status badge: pending → confirmed
```
- Toast notification keluar
- Status badge tukar warna/text dengan animation
- Tiada page reload

---

## Troubleshooting

### ❌ Console error: "Pusher is not defined"

**Sebab:** Script CDN tak load atau load lambat

**Fix:**
1. Check Network tab → Filter by "pusher"
2. Pastikan `pusher.min.js` loaded (status 200)
3. Hard refresh: Ctrl+Shift+R

---

### ❌ Tak nampak "Pusher: Listening..." di console

**Sebab:** Code Pusher tak execute atau JavaScript error

**Fix:**
1. Check Console tab untuk errors (red text)
2. Pastikan user login (Pusher code hanya untuk authenticated users)
3. Pastikan page betul (show.blade.php atau index.blade.php)

---

### ❌ Pusher connect OK tapi tak dapat event

**Sebab:** Event tak broadcast atau channel/event name salah

**Fix:**

**1. Check Pusher credentials:**
```bash
grep PUSHER .env
```
Pastikan:
```
PUSHER_APP_KEY=03effa88c34803b4248c
PUSHER_APP_CLUSTER=ap1
```

**2. Check Broadcasting driver:**
```bash
grep BROADCAST_DRIVER .env
```
Mesti:
```
BROADCAST_DRIVER=pusher
```

**3. Check Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```
Cari error berkaitan Pusher/Broadcasting

**4. Test manual broadcast:**
```bash
php test-pusher-debug.php
```

---

### ❌ Event received tapi UI tak update

**Sebab:** Order ID tak match atau DOM element tak jumpa

**Fix:**

**Show Page:**
- Check console: "Order status changed: ..." ada ke?
- Kalau tak ada, order_id mismatch
- Check: `const currentOrderId = {{ $order->id }};` betul ke?

**Index Page:**
- Check console: "Updating order card status badge: ..." ada ke?
- Kalau tak ada, order card tak jumpa
- Check: `data-id` attribute order card sama dengan order_id event

---

### ❌ WebSocket tak connect

**Sebab:** Pusher credentials salah atau network issue

**Fix:**

**1. Check WebSocket connection:**
- F12 → Network tab → WS filter
- Patut ada connection ke `ws-ap1.pusher.com`
- Status: 101 Switching Protocols

**2. Check Pusher dashboard:**
- Login: https://dashboard.pusher.com/
- Check "Debug Console"
- Cuba trigger event manual, nampak ke?

---

## Testing Checklist

Gunakan checklist ini untuk test comprehensive:

### ✅ Show Page
- [ ] Page load, console shows "Pusher: Listening..."
- [ ] WebSocket connected (check Network → WS tab)
- [ ] Change status via admin → Event received
- [ ] Console logs "Pusher event received"
- [ ] Console logs "Order status changed: X → Y"
- [ ] Toast notification appears
- [ ] Page reloads after 2 seconds
- [ ] New status displayed after reload

### ✅ Index Page
- [ ] Page load, console shows "Pusher: Listening..."
- [ ] WebSocket connected
- [ ] Change status via admin → Event received
- [ ] Console logs "Pusher: Order status update received"
- [ ] Console logs "Updating order card status badge"
- [ ] Toast notification appears
- [ ] Status badge updates instantly (no page reload)
- [ ] Pulse animation plays on badge
- [ ] If filtered by category, order stays visible if still in category

### ✅ Multiple Orders
- [ ] Test with 2+ orders visible on index page
- [ ] Change status of order A → Only order A updates
- [ ] Change status of order B → Only order B updates
- [ ] No cross-contamination

### ✅ Edge Cases
- [ ] Order completed → No longer listening (show page)
- [ ] Order cancelled → No longer listening (show page)
- [ ] Guest user → No Pusher (wrapped in @auth)
- [ ] Wrong order_id in event → No update (filtered correctly)

---

## Expected Console Output

### Normal Flow (Show Page)
```
[Page Load]
Pusher: Listening for order 27

[Status Changed via Admin]
Pusher event received: {order_id: 27, new_status: "confirmed", old_status: "pending", updated_by: "admin", timestamp: "2025-11-07 15:00:00"}
Order status changed: pending → confirmed

[2 seconds later]
[Page Reload]
```

### Normal Flow (Index Page)
```
[Page Load]
Pusher: Listening for order status updates on kitchen-display channel

[Status Changed via Admin]
Pusher: Order status update received {order_id: 27, new_status: "confirmed", ...}
Updating order card status badge: pending → confirmed

[No page reload - status badge updated in DOM]
```

---

## Quick Fix Commands

### Clear cache kalau tak jalan:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Restart queue worker (kalau guna queue):
```bash
php artisan queue:restart
```

### Test Pusher credentials:
```bash
php artisan tinker

// Check config
echo config('broadcasting.default');
echo config('broadcasting.connections.pusher.key');
echo config('broadcasting.connections.pusher.options.cluster');
```

---

## Success Indicators

Kalau semua okay, anda patut nampak:

✅ Browser console: "Pusher: Listening..."
✅ Network tab: WebSocket connection active
✅ Admin tukar status → Instant update di customer page
✅ Toast notification keluar
✅ Status badge update (index) atau page reload (show)
✅ Tiada JavaScript errors di console
✅ Tiada 404 errors untuk pusher.min.js

---

## Contact/Debug Info

**Pusher App Key:** 03effa88c34803b4248c
**Pusher Cluster:** ap1
**Channel Name:** kitchen-display (public)
**Event Name:** order.status.updated

**Files Modified:**
- `resources/views/customer/order/show.blade.php`
- `resources/views/customer/order/index.blade.php`

**Test Scripts:**
- `test-pusher-debug.php` - Comprehensive debug test
- `test-pusher-update.php` - Simple status change test

---

**Updated:** 2025-11-07
**Status:** ✅ Script load order fixed, ready to test
