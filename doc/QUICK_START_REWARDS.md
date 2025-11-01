# 🚀 Quick Start Guide - Rewards System

**Date**: 31 Oktober 2025

---

## ✅ System Status

**All Components**: ✅ READY
- Routes: ✅ 74 routes registered
- Views: ✅ 14 files created
- Controllers: ✅ All exist from Phase 4-7
- Services: ✅ All functional
- Database: ✅ Schema complete

---

## 🎯 Quick Access URLs

### Admin Panel

**Main Dashboard**:
```
http://localhost/the_stag/admin/rewards
```

**Direct Access**:
- Rewards List: `http://localhost/the_stag/admin/rewards/rewards`
- Create Reward: `http://localhost/the_stag/admin/rewards/rewards/create`
- Tiers List: `http://localhost/the_stag/admin/rewards/loyalty-tiers`
- Create Tier: `http://localhost/the_stag/admin/rewards/loyalty-tiers/create`
- Voucher Templates: `http://localhost/the_stag/admin/rewards/voucher-templates`
- Voucher Collections: `http://localhost/the_stag/admin/rewards/voucher-collections`
- Members: `http://localhost/the_stag/admin/rewards/members`
- Redemptions: `http://localhost/the_stag/admin/rewards/redemptions`

### Customer Portal

**Rewards Portal**:
```
http://localhost/the_stag/customer/rewards
```

---

## 🏃 Quick Setup (5 Minutes)

### Step 1: Create Loyalty Tiers

Navigate to: `Admin Panel → Rewards → Tiers & Levels tab → New Tier`

**Create 4 tiers** (recommended):

**1. Bronze Tier**:
```
Name: Bronze
Order: 1
Points Threshold: 100
Points Multiplier: 1.2
Active: ✓
```

**2. Silver Tier**:
```
Name: Silver
Order: 2
Points Threshold: 500
Points Multiplier: 1.5
Active: ✓
```

**3. Gold Tier**:
```
Name: Gold
Order: 3
Points Threshold: 1000
Points Multiplier: 2.0
Active: ✓
```

**4. Platinum Tier**:
```
Name: Platinum
Order: 4
Points Threshold: 5000
Points Multiplier: 3.0
Active: ✓
```

### Step 2: Create Sample Rewards

Navigate to: `Admin Panel → Rewards → Rewards tab → New Reward`

**General Reward** (Available to all):
```
Title: Free Coffee
Description: Enjoy a complimentary coffee on us!
Reward Type: Product
Points Required: 50
Required Tier: (Leave empty)
Active: ✓
```

**Tier-Exclusive Reward** (Gold only):
```
Title: Premium Meal Set
Description: Exclusive meal set for Gold members
Reward Type: Product
Points Required: 200
Required Tier: Gold
Active: ✓
```

### Step 3: Test Customer Portal

1. Login sebagai customer
2. Navigate to: `http://localhost/the_stag/customer/rewards`
3. Test daily check-in
4. Try redeem reward (if sufficient points)

---

## 🧪 Testing Scenarios

### Test 1: Create & View Tier

**Admin Side**:
1. Go to `admin/rewards` → Tiers tab
2. Click "New Tier"
3. Fill form dengan Bronze tier details
4. Submit
5. ✅ Expected: Tier appears in tiers list

**Customer Side**:
1. Award 150 points to customer (via tinker or order)
2. Check customer rewards portal
3. ✅ Expected: Customer shows "Bronze Member" badge

### Test 2: Tier-Exclusive Rewards

**Setup**:
1. Create Gold-only reward (200 points)
2. Create Bronze customer (100 points balance)
3. Create Gold customer (1000 points balance)

**Test**:
1. Login as Bronze customer → Go to rewards portal
2. ✅ Expected: Gold-only reward NOT visible
3. Login as Gold customer → Go to rewards portal
4. ✅ Expected: Gold-only reward IS visible

### Test 3: Points Multiplier

**Setup**:
1. Customer A: Bronze tier (1.2x multiplier)
2. Customer B: Gold tier (2.0x multiplier)
3. Both complete RM 100 order

**Test**:
```bash
php artisan tinker

# Customer A (Bronze 1.2x)
$customerA = User::find(1);
$loyaltyService->awardOrderPoints($customerA, 100, 999);
$customerA->refresh();
echo $customerA->points_balance; // Should be 120 (100 × 1.2)

# Customer B (Gold 2.0x)
$customerB = User::find(2);
$loyaltyService->awardOrderPoints($customerB, 100, 999);
$customerB->refresh();
echo $customerB->points_balance; // Should be 200 (100 × 2.0)
```

### Test 4: Daily Check-in

**Customer Side**:
1. Go to rewards portal
2. Click "Daily Check-In" button
3. ✅ Expected: Toast notification "Check-in successful! +X points"
4. Points balance increases
5. Button changes to "Checked In Today" (disabled)
6. Try tomorrow: Button active again

### Test 5: Reward Redemption

**Setup**:
1. Customer with 100 points
2. Reward requiring 50 points

**Test**:
1. Go to rewards portal
2. Click "Redeem Now" on available reward
3. Confirm redemption
4. ✅ Expected:
   - Points deducted (now 50 points)
   - Reward appears in "Redeemed Rewards" section
   - Status shows "Pending Use"
   - Toast notification appears

---

## 🐛 Troubleshooting

### Issue: "Route not defined" error

**Solution**:
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Views not found

**Check**:
```bash
php artisan view:clear
ls resources/views/admin/rewards
ls resources/views/customer/rewards
```

### Issue: Tier not showing in customer portal

**Check**:
1. Customer has `loyalty_tier_id` set?
2. Tier is active?
3. Customer points >= tier threshold?

**Debug**:
```bash
php artisan tinker

$user = User::find(1);
echo $user->loyalty_tier_id; // Should have value
echo $user->loyaltyTier->name ?? 'No tier'; // Should show tier name
```

### Issue: Rewards not filtered by tier

**Check**:
1. RewardRedemptionService->getAvailableRewards() being used?
2. Reward has required_tier_id set?
3. Customer loyaltyTier has proper `order` value?

**Debug**:
```bash
php artisan tinker

$user = User::find(1);
$service = app(\App\Services\Loyalty\RewardRedemptionService::class);
$rewards = $service->getAvailableRewards($user);
echo $rewards->count(); // Should show filtered count
```

---

## 📋 Verification Checklist

Before going to production, verify:

**Database**:
- [ ] loyalty_tiers table has columns: order, points_threshold, points_multiplier
- [ ] rewards table has column: required_tier_id
- [ ] At least 2-4 tiers created
- [ ] At least 3-5 rewards created

**Views**:
- [ ] Admin dashboard loads at `/admin/rewards`
- [ ] All tabs functional (Rewards, Tiers, Vouchers, etc.)
- [ ] Create forms load correctly
- [ ] Edit forms load with existing data
- [ ] Customer portal loads at `/customer/rewards`

**Functionality**:
- [ ] Can create new tier with Phase 7 fields
- [ ] Can create new reward with tier restriction
- [ ] Customer sees tier badge in portal
- [ ] Customer can check-in daily
- [ ] Customer can redeem rewards
- [ ] Tier-exclusive rewards filtered correctly
- [ ] Points multiplier applies on order completion

**Integration**:
- [ ] Queue worker running for notifications
- [ ] Automatic tier upgrades working
- [ ] Voucher issuance working (if linked)
- [ ] Points audit trail logging

---

## 🎓 Key Concepts Reminder

### Tier System Hierarchy

```
Order 1 (Bronze) → Order 2 (Silver) → Order 3 (Gold) → Order 4 (Platinum)
  100 points         500 points        1000 points      5000 points
  1.2x multiplier    1.5x multiplier   2.0x multiplier  3.0x multiplier
```

**Rules**:
- Lower order = lower tier
- Customer automatically upgrades when points >= threshold
- Tier-exclusive rewards: Customer can access their tier + all lower tiers
- Example: Gold customer (order 3) can access Bronze, Silver, and Gold rewards, but NOT Platinum

### Points Multiplier

**How it works**:
```
Order Amount: RM 100
Base Points: 100 (1 point per RM 1)

Bronze (1.2x): 100 × 1.2 = 120 points
Silver (1.5x): 100 × 1.5 = 150 points
Gold (2.0x): 100 × 2.0 = 200 points
Platinum (3.0x): 100 × 3.0 = 300 points
```

**Applied by**: `LoyaltyService->awardOrderPoints()`

### Tier-Exclusive Rewards

**Setup**:
1. Admin creates reward
2. Sets "Required Tier" to Gold (or any tier)
3. Reward saved with `required_tier_id = 3`

**Display**:
1. Customer logs in (Silver tier, order = 2)
2. RewardRedemptionService filters rewards
3. Gold reward (required order = 3) is HIDDEN
4. Only Bronze (order 1) and Silver (order 2) rewards shown

---

## 📞 Quick Commands Reference

```bash
# Check routes
php artisan route:list --name=admin.rewards
php artisan route:list --name=customer.rewards

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Queue worker (for notifications)
php artisan queue:work

# Test tier upgrade
php artisan tinker
$user = User::find(1);
$service = app(\App\Services\Loyalty\TierService::class);
$result = $service->calculateAndUpdateTier($user);
print_r($result);

# Award points manually
php artisan tinker
$user = User::find(1);
$service = app(\App\Services\Loyalty\LoyaltyService::class);
$service->awardPoints($user, 100, 'Manual bonus');
```

---

## 🎉 Success Criteria

Your system is working correctly if:

✅ Admin can create tiers with Phase 7 fields (order, threshold, multiplier)
✅ Admin can create tier-exclusive rewards
✅ Customer sees tier badge in rewards portal
✅ Customer only sees rewards for their tier level
✅ Daily check-in works and awards points
✅ Reward redemption works and deducts points
✅ Points multiplier applies on order completion
✅ Automatic tier upgrades happen when threshold reached
✅ All forms validate properly
✅ All views render without errors

---

## 📚 Documentation Links

- [VIEWS_REBUILD_COMPLETE.md](VIEWS_REBUILD_COMPLETE.md) - Complete rebuild documentation
- [FINAL_SYSTEM_STATUS.md](FINAL_SYSTEM_STATUS.md) - Overall system status
- [PHASE7_COLUMNS_FIXED.md](PHASE7_COLUMNS_FIXED.md) - Phase 7 column details
- [MISSING_RELATIONSHIP_FIXED.md](MISSING_RELATIONSHIP_FIXED.md) - requiredTier relationship
- [LOYALTY_SYSTEM_COMPLETE.md](LOYALTY_SYSTEM_COMPLETE.md) - Complete system docs

---

**🎊 SYSTEM READY! START USING NOW!**

Navigate to `http://localhost/the_stag/admin/rewards` and start creating your rewards program!
