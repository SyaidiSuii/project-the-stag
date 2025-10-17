# 🚀 LARAVEL REVERB SETUP GUIDE

## ✅ **SYSTEM REQUIREMENTS** (Already Met!)

```
✅ PHP: 8.3.24 (Requirement: 8.2+) - PASS!
✅ Laravel: 10.48.29 (Requirement: 10.47+) - PASS!
```

**NO UPGRADE NEEDED!** You're ready to install Reverb immediately!

---

## 📋 **STEP-BY-STEP IMPLEMENTATION**

### **Step 1: Install Laravel Reverb Package** ⏱️ 2 min

Open terminal di project folder dan run:

```bash
composer require laravel/reverb
```

**Expected Output:**
```
Using version ^1.x-dev for laravel/reverb
./composer.json has been updated
Running composer update laravel/reverb
Loading composer repositories with package information
Updating dependencies
Lock file operations: 15 installs, 0 updates, 0 removals
  - Locking clue/redis-react (v2.7.0)
  - Locking laravel/reverb (v1.x-dev)
  ...
Writing lock file
Installing dependencies from lock file
  ...
Package operations: 15 installs, 0 updates, 0 removals
  - Downloading laravel/reverb (v1.x-dev)
  ...
  - Installing laravel/reverb (v1.x-dev): Extracting archive
Generating optimized autoload files
...
Package manifest generated successfully.
```

---

### **Step 2: Run Reverb Installation** ⏱️ 2 min

```bash
php artisan reverb:install
```

**Expected Prompts & Answers:**

**Prompt 1:**
```
 Would you like to install the Reverb broadcasting driver? (yes/no) [no]
```
**Answer:** Type `yes` and press Enter

**Prompt 2:**
```
 Would you like to install and build Node dependencies? (yes/no) [no]
```
**Answer:** Type `yes` if you want auto npm install, or `no` if you'll do it manually later

**What This Command Does:**
- ✅ Creates `config/reverb.php` configuration file
- ✅ Updates `.env` with Reverb credentials (auto-generated)
- ✅ Publishes migration files (if any)
- ✅ Installs npm packages (if you chose yes)
- ✅ Sets `BROADCAST_DRIVER=reverb` in `.env`

**Expected Output:**
```
   INFO  Reverb installed successfully!

  The following environment variables have been added to your .env file:

  BROADCAST_DRIVER=reverb
  REVERB_APP_ID=123456
  REVERB_APP_KEY=abcdefghijklmnop
  REVERB_APP_SECRET=xyz123secret
  REVERB_HOST=localhost
  REVERB_PORT=8080
  REVERB_SCHEME=http

  Reverb is ready to use!
```

---

### **Step 3: Verify .env Configuration** ⏱️ 1 min

Buka file `.env` dan verify ada lines ni:

```env
BROADCAST_DRIVER=reverb

REVERB_APP_ID=your-auto-generated-id
REVERB_APP_KEY=your-auto-generated-key
REVERB_APP_SECRET=your-auto-generated-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**If NOT present**, add them manually with the values from installation output.

---

### **Step 4: Install NPM Dependencies** ⏱️ 2 min

```bash
npm install --save-dev laravel-echo pusher-js
```

**Expected Output:**
```
added 2 packages, and audited X packages in Xs

X packages are looking for funding
  run `npm fund` for details

found 0 vulnerabilities
```

**If you get npm errors:**
- Make sure Node.js is installed: `node --version`
- Try: `npm cache clean --force` then retry

---

### **Step 5: Enable BroadcastServiceProvider** ⏱️ 1 min

Check if `BroadcastServiceProvider` is **uncommented** in `config/app.php`:

```bash
# Open file
notepad config/app.php
```

Find this section (around line 165-195):
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    /*
     * Package Service Providers...
     */

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,  ← REMOVE THE // HERE!
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
])->toArray(),
```

**Change to:**
```php
    App\Providers\BroadcastServiceProvider::class,  // ✅ Uncommented!
```

---

### **Step 6: Clear All Caches** ⏱️ 1 min

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

**Expected Output:**
```
Configuration cache cleared successfully.
Application cache cleared successfully.
Compiled views cleared successfully.
Route cache cleared successfully.
```

---

### **Step 7: Start Reverb Server** ⏱️ 1 min

**Open NEW terminal** (keep it running) dan execute:

```bash
php artisan reverb:start
```

**Expected Output:**
```
   INFO  Starting server on 0.0.0.0:8080.

  ┌───────────────────────────────────────────────────┐
  │ Reverb Server v1.x                                 │
  ├───────────────────────────────────────────────────┤
  │ Application ID ......... 123456                    │
  │ Host ................... 0.0.0.0                   │
  │ Port ................... 8080                      │
  │ Allowed Origins ........ *                         │
  ├───────────────────────────────────────────────────┤

   INFO  Reverb server started successfully.

   Press Ctrl+C to stop the server
```

**⚠️ IMPORTANT:** Keep this terminal **RUNNING**! Don't close it!

If you see **"Address already in use"** error:
- Port 8080 is busy
- Change port in `.env`: `REVERB_PORT=8081`
- Update view file line 792 dengan port baru
- Restart Reverb: `php artisan reverb:start`

---

### **Step 8: Start Laravel Server** (If not running) ⏱️ 1 min

**Open ANOTHER terminal** dan run:

```bash
php artisan serve
```

**Expected Output:**
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

---

## 🧪 **TESTING REAL-TIME UPDATES**

### **Test 1: Check Dashboard Connection** ⏱️ 1 min

1. **Open browser** → `http://localhost:8000/admin/reports`
2. **Login** as admin
3. **Look at top right** corner

**Expected Connection Status:**
- 🟢 **"Live"** (green circle) - SUCCESS! WebSocket connected!
- 🟠 **"Polling"** (orange) - WebSocket not connected, using fallback

**If shows "Polling":**
- Check Reverb terminal still running
- Check browser console (F12) for errors
- Verify `.env` has `BROADCAST_DRIVER=reverb`

---

### **Test 2: Check Browser Console** ⏱️ 1 min

**Press F12** → **Console** tab

**Expected output:**
```javascript
🚀 Initializing Real-time Analytics...
✅ WebSocket connected to analytics-updates channel
✅ Real-time Analytics initialized
```

**If you see errors:**
```javascript
❌ WebSocket connection failed: ...
⚠️ Laravel Echo not available. Using polling fallback.
```

**Solutions:**
- Check Reverb server running: Terminal should show "Reverb server started"
- Check port 8080 accessible: `netstat -ano | findstr :8080`
- Verify Echo scripts loaded: Check Network tab for `pusher.min.js` and `echo.iife.js`

---

### **Test 3: Fire Test Event** ⏱️ 2 min

**Open THIRD terminal** (keep Laravel + Reverb running):

```bash
php artisan tinker
```

Then execute these commands:

```php
// Get first order
$order = App\Models\Order::first();

// Load relationships
$order->load('user', 'items');

// Fire OrderPaidEvent
event(new App\Events\OrderPaidEvent($order));
```

**Expected Behavior:**

**In Browser (within < 1 second):**
1. **Revenue card** flashes purple ✨
2. **Numbers update** with animation
3. **Toast notification** slides in from right:
   ```
   🟢 New Order
   Order #ORD-XXX paid: RM XXX.XX
   ```

**In Browser Console:**
```javascript
📦 Order Paid Event: {
  order_id: 1,
  order_number: "ORD-2025-001",
  total_amount: 125.50,
  order_type: "dine-in",
  payment_status: "paid",
  analytics: {
    total_revenue: 125.50,
    total_orders: 1,
    ...
  },
  timestamp: "2025-10-17 15:30:45"
}
```

**In Reverb Terminal:**
```
[2025-10-17 15:30:45] analytics-updates: Broadcasting event [order.paid]
[2025-10-17 15:30:45] analytics-updates: 1 connection(s) received message
```

---

### **Test 4: Real Order Payment** ⏱️ 2 min

**Best test** is dengan actual order:

1. **Open `/admin/order`** (admin panel)
2. **Find any order** with `payment_status = 'unpaid'`
3. **Click "Update Payment"** button
4. **Change to "Paid"**
5. **Switch tab** to `/admin/reports` dashboard

**Dashboard should update INSTANTLY** (< 1 second)! ⚡

---

## 🎯 **EXPECTED RESULTS**

### **Performance Comparison:**

| Metric | Before (Polling) | After (Reverb) | Improvement |
|--------|------------------|----------------|-------------|
| **Update Speed** | 30 seconds | < 1 second | **30x faster** ⚡ |
| **Server Load** | 1 request/30s/user | 1 connection/user | Much lighter |
| **User Experience** | Delayed | Real-time | Excellent 🎯 |
| **Notifications** | None | Toast + Desktop | Added ✨ |
| **Cost** | Free | Free | $0 🎉 |

### **Dashboard Features Now Active:**

✅ **Connection Status Indicator** (top right)
- 🟢 "Live" = WebSocket connected
- 🟠 "Polling" = Fallback mode
- 🔴 "Offline" = Disconnected

✅ **Real-time Stat Card Updates**
- Revenue
- Total Orders
- Avg Order Value
- QR Orders
- Table Bookings
- Promotions Used
- Rewards Redeemed

✅ **Toast Notifications**
- New Order notifications
- Promotion applied alerts
- Reward redemption notices
- Table booking confirmations

✅ **Visual Animations**
- Purple flash on card update
- Number scale animation
- Smooth transitions

✅ **Browser Notifications** (with permission)
- Desktop alerts even when browser minimized
- Persistent until dismissed

---

## 🐛 **TROUBLESHOOTING**

### **Problem 1: Connection shows "Polling" instead of "Live"**

**Diagnosis:**
```bash
# Check if Reverb running
# In Reverb terminal, should see "Server running on 0.0.0.0:8080"

# Check port 8080 listening
netstat -ano | findstr :8080
# Should show: TCP    0.0.0.0:8080    0.0.0.0:0    LISTENING    XXXX
```

**Solution:**
1. Restart Reverb server
2. Clear browser cache (Ctrl+Shift+Delete)
3. Hard refresh page (Ctrl+F5)
4. Check firewall not blocking port 8080

---

### **Problem 2: "Echo is not defined" error**

**Diagnosis:**
Open browser Console (F12), type:
```javascript
console.log(window.Echo);
```

If shows `undefined`, CDN scripts not loaded.

**Solution:**
1. Check internet connection
2. Verify CDN URLs accessible:
   - https://cdn.jsdelivr.net/npm/pusher-js@8.0.1/dist/web/pusher.min.js
   - https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js
3. Check browser Network tab (F12) - scripts should return 200 OK
4. Try clearing browser cache

---

### **Problem 3: Events not reaching dashboard**

**Diagnosis:**
```bash
# Check if event fired
php artisan tinker
>>> event(new App\Events\OrderPaidEvent(App\Models\Order::first()));

# Check Reverb terminal for broadcast message
# Should see: "Broadcasting event [order.paid]"
```

**Solution:**
1. Verify `BroadcastServiceProvider` uncommented in `config/app.php`
2. Check EventServiceProvider has OrderPaidEvent registered
3. Verify `.env` has `BROADCAST_DRIVER=reverb`
4. Clear config cache: `php artisan config:clear`

---

### **Problem 4: Port 8080 already in use**

**Error message:**
```
[Symfony\Component\Process\Exception\ProcessFailedException]
The command "php artisan reverb:start" failed.
Exit Code: 1 (General error)
[...] Address already in use
```

**Solution:**
1. **Find process using port 8080:**
   ```bash
   netstat -ano | findstr :8080
   ```
   Output: `TCP    0.0.0.0:8080    0.0.0.0:0    LISTENING    1234`

2. **Kill the process:**
   ```bash
   taskkill /PID 1234 /F
   ```

3. **OR change Reverb port:**
   Edit `.env`:
   ```env
   REVERB_PORT=8081
   ```

   Update view (line 792):
   ```javascript
   wsPort: {{ env('REVERB_PORT', 8081) }},
   ```

4. **Restart Reverb:**
   ```bash
   php artisan reverb:start
   ```

---

### **Problem 5: "Class 'App\Events\OrderPaidEvent' not found"**

**Solution:**
```bash
# Regenerate autoload files
composer dump-autoload

# Clear cache
php artisan config:clear
php artisan cache:clear
```

---

## 🏭 **PRODUCTION DEPLOYMENT**

### **Option 1: Using Supervisor (Recommended)**

Create supervisor config file:

**Windows (Not available, skip to Option 2)**

**Linux:**
```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```

Content:
```ini
[program:reverb]
command=php /var/www/your-app/artisan reverb:start
directory=/var/www/your-app
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/reverb.log
stopwaitsecs=3600
```

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
```

Check status:
```bash
sudo supervisorctl status reverb
```

---

### **Option 2: Windows Service (for Production on Windows)**

Use **NSSM** (Non-Sucking Service Manager):

1. Download NSSM: https://nssm.cc/download
2. Extract ke folder (e.g., `C:\nssm`)
3. Open CMD as Administrator:

```cmd
cd C:\nssm\win64

nssm install ReverbWebSocket "C:\laragon\bin\php\php-8.3.24\php.exe" "artisan reverb:start"
nssm set ReverbWebSocket AppDirectory "D:\ProgramsFiles\laragon\www\the_stag"
nssm set ReverbWebSocket AppStdout "D:\ProgramsFiles\laragon\www\the_stag\storage\logs\reverb.log"
nssm set ReverbWebSocket AppStderr "D:\ProgramsFiles\laragon\www\the_stag\storage\logs\reverb-error.log"

nssm start ReverbWebSocket
```

Check status:
```cmd
nssm status ReverbWebSocket
```

---

### **Option 3: PM2 (Node.js Process Manager)**

If you have PM2 installed:

```bash
# Create ecosystem file
pm2 ecosystem
```

Edit `ecosystem.config.js`:
```javascript
module.exports = {
  apps: [{
    name: 'reverb',
    script: 'php',
    args: 'artisan reverb:start',
    cwd: 'D:/ProgramsFiles/laragon/www/the_stag',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
  }]
};
```

Start:
```bash
pm2 start ecosystem.config.js
pm2 save
pm2 startup
```

---

## 📊 **MONITORING & LOGS**

### **View Reverb Logs**

**Development:**
- Check Reverb terminal for live logs
- Connection/disconnection events
- Broadcasting messages

**Production:**
```bash
# If using Supervisor (Linux)
tail -f /var/log/reverb.log

# If using NSSM (Windows)
type D:\ProgramsFiles\laragon\www\the_stag\storage\logs\reverb.log

# Laravel logs
type storage\logs\laravel.log
```

### **Check Active Connections**

Reverb dashboard (if enabled):
```
http://localhost:8080
```

Shows:
- Total connections
- Active channels
- Messages per second
- Uptime

---

## ✅ **FINAL CHECKLIST**

Before going to production:

- [ ] ✅ Reverb package installed
- [ ] ✅ `.env` configured with Reverb credentials
- [ ] ✅ BroadcastServiceProvider uncommented
- [ ] ✅ NPM packages installed (laravel-echo, pusher-js)
- [ ] ✅ View file updated with Reverb config
- [ ] ✅ Caches cleared
- [ ] ✅ Reverb server starts without errors
- [ ] ✅ Dashboard shows "Live" connection
- [ ] ✅ Test event updates dashboard instantly
- [ ] ✅ Real order payment updates dashboard
- [ ] ✅ Toast notifications appear
- [ ] ✅ No errors in browser console
- [ ] ✅ No errors in Reverb terminal
- [ ] ✅ Production service setup (Supervisor/NSSM/PM2)
- [ ] ✅ Logs configured and accessible
- [ ] ✅ Firewall allows port 8080 (or chosen port)
- [ ] ✅ SSL/TLS configured (if needed for production)

---

## 🚀 **QUICK START COMMANDS**

Copy-paste these commands to start everything:

**Terminal 1 - Laravel App:**
```bash
cd D:\ProgramsFiles\laragon\www\the_stag
php artisan serve
```

**Terminal 2 - Reverb Server:**
```bash
cd D:\ProgramsFiles\laragon\www\the_stag
php artisan reverb:start
```

**Terminal 3 - Queue Worker (Optional):**
```bash
cd D:\ProgramsFiles\laragon\www\the_stag
php artisan queue:work
```

**Browser:**
```
http://localhost:8000/admin/reports
```

---

## 📞 **SUPPORT & REFERENCES**

### **Official Documentation**
- Laravel Reverb: https://laravel.com/docs/10.x/reverb
- Laravel Broadcasting: https://laravel.com/docs/10.x/broadcasting
- Laravel Echo: https://github.com/laravel/echo

### **Troubleshooting**
- Check Laravel logs: `storage/logs/laravel.log`
- Check Reverb terminal for errors
- Check browser console (F12) for JavaScript errors
- Verify Event/Listener registration: `php artisan event:list`

### **Community**
- Laravel Discord: https://discord.gg/laravel
- Laravel Forums: https://laracasts.com/discuss
- Stack Overflow: Tag `laravel-reverb`

---

## 🎊 **YOU'RE DONE!**

Congratulations! Your Laravel application now has **REAL-TIME ANALYTICS** with:

✅ **< 1 second update latency**
✅ **Zero monthly cost** (self-hosted)
✅ **Toast notifications**
✅ **Connection status monitoring**
✅ **Smooth animations**
✅ **Production-ready**

**Next Access**: Navigate to `/admin/reports` and watch your dashboard update in REAL-TIME as orders come in!

🎉 **Enjoy your blazing-fast real-time analytics system!** 🚀

