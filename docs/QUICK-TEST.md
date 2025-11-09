# 🚀 Quick Test - Pusher + FCM (2 Minutes)

## Pra-syarat
```bash
# Pastikan queue worker berjalan
php artisan queue:work
```

---

## Test 1: Toggle Persistence (30 saat)

1. Buka: `http://localhost/customer/account`
2. Scroll ke "Preferences"
3. Klik toggle "Push Notifications"
4. Allow browser permission
5. **Refresh page (F5)**
6. ✅ Toggle masih ON (hijau) - **INI YANG DIBETULKAN**

---

## Test 2: Push Notification (1 minit)

1. Buka: `http://localhost/customer/orders`
2. Di admin, tukar status order ke **preparing**
3. ✅ Windows notification muncul
4. Tukar ke **ready**
5. ✅ Notification muncul lagi
6. Tukar ke **cancelled**
7. ✅ TIADA notification (filtered out)

**Filter aktif:** preparing, ready, completed sahaja

---

## Test 3: Real-Time Update (30 saat)

1. Buka: `http://localhost/customer/orders`
2. Buka console (F12)
3. Di admin, tukar status order
4. ✅ Badge tukar warna instantly (tanpa refresh)
5. ✅ Pulse animation main
6. ✅ Toast notification keluar

---

## ⚠️ PENTING

**Tanpa queue worker:**
- ❌ Events tak broadcast
- ❌ Tiada real-time update
- ❌ Push notification tak keluar

**Dengan queue worker:**
- ✅ Semua berfungsi
- ✅ Update dalam 1 saat
- ✅ Notification sampai

---

## 🔧 Kalau Ada Masalah

**Toggle off lepas refresh?**
- Sepatutnya dah fix ✅
- Guna `Notification.permission` sekarang

**Tiada notification?**
1. Toggle ON dalam preferences?
2. Browser permission allow?
3. Status dalam filter list? (preparing/ready/completed)
4. Queue worker running?

**Tiada real-time update?**
1. Queue worker running?
2. Console ada error?
3. Hard refresh: Ctrl+Shift+R

---

**Fail lengkap:** `PUSHER-FCM-COMPLETE-TEST.md`
