# Panduan Menggunakan Sistem Loyalty - The Stag SmartDine

## Status: ✅ SISTEM SIAP DIGUNAKAN

**Tarikh**: 31 Oktober 2025

---

## 🚀 Cara Mulakan Sistem

### 1. Start Queue Worker (PENTING!)

Sistem loyalty menggunakan **background jobs** untuk notifications dan automated tasks. Mesti start queue worker:

```bash
# Method 1: Simple (untuk development)
php artisan queue:work

# Method 2: With restart on code changes
php artisan queue:work --timeout=60

# Method 3: For production (recommended - using Supervisor)
# Configure Supervisor dan set:
# command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
```

**Kenapa perlu?**
- TierUpgraded notifications akan dihantar via queue
- PointsAwarded notifications async processing
- Background jobs untuk expire vouchers/rewards

---

## 📱 Admin Panel - Cara Akses

### URL Utama
```
http://localhost/the_stag/admin/rewards
```

### Tab Yang Tersedia

1. **Rewards** - Urus katalog rewards
2. **Voucher Templates** - Create voucher templates
3. **Check-in Settings** - Daily check-in rewards settings
4. **Special Events** - Limited-time events (double points, etc.)
5. **Tiers & Levels** - Konfigurasi loyalty tiers (Bronze, Silver, Gold, Platinum)
6. **Redemptions** - View semua redemptions dari customers
7. **Members** - View loyalty members, adjust points manually
8. **Achievements** - Milestone achievements
9. **Voucher Collection** - Issue vouchers untuk customers
10. **Bonus Points** - Bonus challenges

---

## 🎯 Cara Setup Loyalty Tiers

### Step 1: Create Tiers

Navigate ke: **Admin Panel → Rewards → Tiers & Levels → New Tier**

**Contoh Setup:**

**Bronze Tier**
```
Name: Bronze
Order: 1
Points Threshold: 100 points
Spending Threshold: RM 100
Points Multiplier: 1.2x (20% bonus)
Benefits: "Early access to promotions, 20% extra points"
Active: Yes
```

**Silver Tier**
```
Name: Silver
Order: 2
Points Threshold: 500 points
Spending Threshold: RM 500
Points Multiplier: 1.5x (50% bonus)
Benefits: "Free birthday meal, 50% extra points, exclusive rewards"
Active: Yes
```

**Gold Tier**
```
Name: Gold
Order: 3
Points Threshold: 1000 points
Spending Threshold: RM 1000
Points Multiplier: 2.0x (100% bonus)
Benefits: "Priority reservations, 100% extra points, VIP events"
Active: Yes
```

**Platinum Tier**
```
Name: Platinum
Order: 4
Points Threshold: 5000 points
Spending Threshold: RM 5000
Points Multiplier: 3.0x (200% bonus)
Benefits: "Personal concierge, 200% extra points, unlimited vouchers"
Active: Yes
```

### Step 2: Verify Tiers

```bash
php artisan tinker

# Check tiers
\App\Models\LoyaltyTier::orderBy('order')->get(['name', 'points_threshold', 'points_multiplier']);
```

---

## 🎁 Cara Create Rewards

### Step 1: Create Reward

Navigate ke: **Admin Panel → Rewards → Rewards → New Reward**

**Contoh Reward Biasa:**
```
Title: Free Coffee
Description: Redeem untuk free regular coffee
Reward Type: voucher
Points Required: 50
Expiry Days: 30 days
Active: Yes
Required Tier: (none - semua boleh redeem)
```

**Contoh Tier-Exclusive Reward:**
```
Title: Free Premium Set Meal
Description: Exclusive untuk Gold members sahaja
Reward Type: voucher
Points Required: 200
Expiry Days: 30 days
Active: Yes
Required Tier: Gold (hanya Gold & Platinum boleh redeem)
```

### Step 2: Verify Reward Created

```bash
php artisan tinker

# List rewards
\App\Models\Reward::with('requiredTier')->get(['title', 'points_required', 'required_tier_id']);
```

---

## 💰 Cara Award Points Kepada Customer

### Method 1: Via Tinker (Manual Testing)

```bash
php artisan tinker

# Get user
$user = \App\Models\User::find(1);

# Award points
$loyaltyService = app(\App\Services\Loyalty\LoyaltyService::class);
$loyaltyService->awardPoints($user, 100, 'Test points reward');

# Check balance
$user->refresh();
echo "Balance: " . $user->points_balance . " points";

# Check tier
echo "Tier: " . ($user->loyaltyTier->name ?? 'No tier yet');
```

### Method 2: Via Order (Automatic)

Bila customer complete order, sistem automatically award points:

```php
// Dalam PaymentController atau OrderController
use App\Services\Loyalty\LoyaltyService;

public function completeOrder(Order $order)
{
    // ... payment processing ...

    if ($order->user) {
        $loyaltyService = app(LoyaltyService::class);

        // Award points based on order amount
        // Default: 1 point per RM1
        $loyaltyService->awardOrderPoints(
            $order->user,
            $order->total_amount,
            $order->id
        );

        // System automatically:
        // 1. Apply tier multiplier (Gold = 1.5x bonus)
        // 2. Check for tier upgrade
        // 3. Send notifications (queued)
    }

    // ...
}
```

### Method 3: Via Admin Panel

Navigate ke: **Admin Panel → Rewards → Members → [Select Customer] → Adjust Points**

```
Points to Add: 100
Reason: "Birthday bonus - 100 points"
```

---

## 🎉 Cara Automatic Tier Upgrade Berfungsi

### Flow Diagram

```
Customer buat order RM 200
    ↓
PaymentController process payment
    ↓
LoyaltyService::awardOrderPoints($user, 200, $orderId)
    ↓
System check tier: Customer ada Gold tier (1.5x multiplier)
    ↓
Award 300 points (200 × 1.5)
    ↓
PointsAwarded event → Notification sent (queued)
    ↓
TierService check: Dah capai 1000 points untuk Platinum?
    ↓ YA!
Update user.loyalty_tier_id = Platinum
    ↓
TierUpgraded event dispatched
    ↓
SendTierUpgradedNotification listener (queued)
    ↓
Customer terima: "Tahniah! Anda telah diupgrade ke Platinum tier!"
```

### Test Automatic Upgrade

```bash
php artisan tinker

# Setup
$user = \App\Models\User::find(1);
$loyaltyService = app(\App\Services\Loyalty\LoyaltyService::class);

echo "Before: " . $user->points_balance . " points\n";
echo "Tier: " . ($user->loyaltyTier->name ?? 'None') . "\n";

# Award enough points to trigger upgrade
$loyaltyService->awardPoints($user, 500, 'Test untuk trigger upgrade');

# Check hasil
$user->refresh();
echo "\nAfter: " . $user->points_balance . " points\n";
echo "Tier: " . ($user->loyaltyTier->name ?? 'None') . "\n";

# Check logs
echo "\nCheck logs untuk TierUpgraded event:\n";
# tail -f storage/logs/laravel.log | grep "tier upgraded"
```

---

## 🎫 Cara Customer Redeem Rewards

### Method 1: Via Customer Portal

Customer navigate ke: `/customer/rewards`

1. View available rewards (filtered by tier automatically)
2. Click "Redeem" pada reward yang nak
3. System validate:
   - Customer ada cukup points?
   - Customer tier cukup tinggi untuk reward ni?
   - Reward masih available?
   - Usage limit tak exceeded?
4. Deduct points & create CustomerReward
5. Send notification (queued)

### Method 2: Via Tinker (Testing)

```bash
php artisan tinker

# Setup
$user = \App\Models\User::find(1);
$reward = \App\Models\Reward::first();
$redemptionService = app(\App\Services\Loyalty\RewardRedemptionService::class);

# Check available rewards untuk user
$available = $redemptionService->getAvailableRewards($user);
echo "Available rewards: " . $available->count() . "\n";

# Redeem reward
try {
    $customerReward = $redemptionService->redeemReward($user, $reward);
    echo "Success! Reward ID: " . $customerReward->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Method 3: Staff Mark As Redeemed

Staff di counter navigate ke: **Admin Panel → Rewards → Redemptions**

1. Customer tunjuk reward code
2. Staff click "Mark as Redeemed"
3. Reward status changed: active → redeemed
4. Customer dapat service/item

---

## 🔍 Cara Check System Health

### 1. Verify Queue Running

```bash
# Check queue worker status
ps aux | grep "queue:work"

# Should see something like:
# user  1234  php artisan queue:work

# If not running, start it:
php artisan queue:work
```

### 2. Verify Points Balance Integrity

```bash
# Run balance verification (weekly automated)
php artisan loyalty:verify-balance

# If discrepancies found, auto-fix:
php artisan loyalty:verify-balance --fix
```

### 3. Check Scheduled Tasks

```bash
# List scheduled tasks
php artisan schedule:list

# Should see:
# loyalty:expire-vouchers  Daily at 01:00
# loyalty:expire-rewards   Daily at 02:00
# loyalty:verify-balance   Weekly on Sunday at 03:00
```

### 4. Monitor Logs

```bash
# Watch all loyalty activity
tail -f storage/logs/laravel.log | grep -E "Points|Tier|Reward|Voucher"

# Watch errors only
tail -f storage/logs/laravel.log | grep ERROR

# Check today's tier upgrades
grep "tier upgraded" storage/logs/laravel-$(date +%Y-%m-%d).log
```

---

## 🐛 Troubleshooting

### Problem 1: Tier tidak auto-upgrade

**Diagnosis:**
```bash
php artisan tinker

$user = \App\Models\User::find(1);
echo "Points: " . $user->points_balance . "\n";

$tierService = app(\App\Services\Loyalty\TierService::class);
$eligible = $tierService->calculateEligibleTier($user);
echo "Eligible for: " . ($eligible->name ?? 'No tier') . "\n";
```

**Solution:**
- Check tier thresholds betul ke tidak
- Make sure `points_balance` updated correctly
- Check logs untuk errors: `tail -f storage/logs/laravel.log | grep "tier"`

### Problem 2: Notifications tidak send

**Diagnosis:**
```bash
# Check queue worker running?
ps aux | grep queue:work

# Check failed jobs
php artisan queue:failed
```

**Solution:**
```bash
# Start queue worker
php artisan queue:work

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Problem 3: Points balance tidak tally

**Diagnosis:**
```bash
php artisan loyalty:verify-balance
```

**Solution:**
```bash
# Auto-fix discrepancies
php artisan loyalty:verify-balance --fix
```

### Problem 4: Cannot redeem tier-exclusive reward

**Check:**
```bash
php artisan tinker

$user = \App\Models\User::find(1);
$reward = \App\Models\Reward::find(1);

echo "User tier: " . ($user->loyaltyTier->name ?? 'None') . "\n";
echo "User tier order: " . ($user->loyaltyTier->order ?? 0) . "\n";
echo "Reward requires: " . ($reward->requiredTier->name ?? 'None') . "\n";
echo "Required order: " . ($reward->requiredTier->order ?? 0) . "\n";

# User tier order must be >= reward required tier order
```

---

## 📊 Analytics & Reports

### 1. Tier Distribution

```bash
php artisan tinker

DB::table('users')
    ->join('loyalty_tiers', 'users.loyalty_tier_id', '=', 'loyalty_tiers.id')
    ->select('loyalty_tiers.name', DB::raw('COUNT(*) as count'))
    ->groupBy('loyalty_tiers.name')
    ->get();
```

### 2. Top Points Earners

Navigate ke: **Admin Panel → Rewards → Members**

Sort by points balance (descending).

### 3. Most Redeemed Rewards

```bash
php artisan tinker

\App\Models\Reward::withCount('customerRewards')
    ->orderBy('customer_rewards_count', 'desc')
    ->take(10)
    ->get(['title', 'customer_rewards_count']);
```

### 4. Recent Tier Upgrades

```bash
# Check logs
grep "tier upgraded" storage/logs/laravel.log | tail -20
```

---

## 🎓 Best Practices

### 1. Queue Management

```bash
# Production: Use Supervisor untuk keep queue worker alive
# Development: Manually start: php artisan queue:work
```

### 2. Scheduled Tasks

```bash
# Add to crontab (production):
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Database Backups

```bash
# Daily backup before expiry tasks run
# loyalty:expire-vouchers runs at 01:00
# loyalty:expire-rewards runs at 02:00
# So backup at 00:30
```

### 4. Monitoring

- Setup log monitoring (Sentry, Laravel Telescope)
- Alert on failed jobs
- Alert on balance discrepancies
- Track tier upgrade velocity

---

## 📞 Support

### Commands Reference

```bash
# Loyalty commands
php artisan loyalty:expire-vouchers    # Expire old vouchers
php artisan loyalty:expire-rewards     # Expire old rewards
php artisan loyalty:verify-balance     # Verify points integrity

# Queue management
php artisan queue:work                 # Start queue worker
php artisan queue:failed               # List failed jobs
php artisan queue:retry all            # Retry failed jobs
php artisan queue:flush                # Clear failed jobs

# Development
php artisan route:list | grep rewards  # List all reward routes
php artisan event:list | grep Loyalty  # List loyalty events
php artisan tinker                     # Interactive shell
```

### Files Reference

- **Services**: `app/Services/Loyalty/`
  - `LoyaltyService.php` - Points management
  - `TierService.php` - Tier management & upgrades
  - `RewardRedemptionService.php` - Reward redemptions
  - `VoucherService.php` - Voucher management

- **Controllers**: `app/Http/Controllers/Admin/`
  - `RewardManagementController.php`
  - `VoucherManagementController.php`
  - `LoyaltyTierManagementController.php`
  - `RedemptionManagementController.php`
  - `LoyaltyMemberController.php`

- **Views**: `resources/views/admin/rewards/`
  - `index.blade.php` - Main dashboard
  - `rewards/` - Rewards views
  - `voucher-templates/` - Voucher views
  - `loyalty-tiers/` - Tier views
  - `members/` - Member management views

### Documentation Files

- `LOYALTY_SYSTEM_COMPLETE.md` - Complete system documentation
- `PHASE_7_SUMMARY.md` - Phase 7 detailed documentation
- `CARA_GUNA_SISTEM_LOYALTY.md` - This file (Bahasa Malaysia guide)

---

## ✅ Quick Start Checklist

- [ ] Start queue worker: `php artisan queue:work`
- [ ] Setup crontab untuk scheduled tasks
- [ ] Create loyalty tiers (Bronze, Silver, Gold, Platinum)
- [ ] Create beberapa rewards (free + tier-exclusive)
- [ ] Test award points: `php artisan tinker`
- [ ] Test tier upgrade: Award enough points untuk trigger
- [ ] Test redeem reward: Via customer portal
- [ ] Monitor logs: `tail -f storage/logs/laravel.log`
- [ ] Setup Supervisor untuk production queue worker
- [ ] Configure email notifications (optional)

---

**Status**: 🎉 **SISTEM SIAP DIGUNAKAN!**

Loyalty system kini production-ready dengan enterprise-grade features setaraf Starbucks Rewards, Marriott Bonvoy, dan Amazon Prime.
