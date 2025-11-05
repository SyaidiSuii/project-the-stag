# 🔍 VOUCHER DEBUGGING GUIDE

## Issues Encountered

### 1. **404 Error - Route Not Found**
```
POST http://the_stag.test/customer/rewards/apply-voucher 404 (Not Found)
```

**Cause**: Laravel cache not updated after adding route  
**Solution**: 
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### 2. **"Voucher not found or already used"**
```
Error: Voucher not found or already used
    at processVoucherTypeReward (cart-voucher.js:440:19)
```

**Cause**: CustomerReward or CustomerVoucher lookup failed  
**Root Causes**:
- CustomerReward ID tidak exist atau tidak belong to user
- CustomerReward.status bukan 'active'
- CustomerReward.reward_type bukan 'voucher'
- CustomerReward.expires_at sudah lalu
- CustomerReward.redemption_code kosong/null
- CustomerVoucher tidak created dengan matching voucher_code

---

## Debug Steps

### **Check Laravel Logs**
```bash
tail -f d:/ProgramsFiles/laragon/www/the_stag/storage/logs/laravel.log
```

**Look for**:
```
applyVoucherFromReward: CustomerReward found
applyVoucherFromReward: Looking for CustomerVoucher
applyVoucherFromReward: Success, returning voucher data
```

### **Database Queries**
```sql
-- Check if CustomerReward exists
SELECT * FROM customer_rewards WHERE id = 6;

-- Check CustomerReward details
SELECT 
    cr.id,
    cr.customer_profile_id,
    cr.status,
    cr.redemption_code,
    cr.expires_at,
    r.reward_type,
    r.voucher_template_id,
    r.title
FROM customer_rewards cr
JOIN rewards r ON cr.reward_id = r.id
WHERE cr.id = 6;

-- Check if CustomerVoucher exists dengan same voucher_code
SELECT * FROM customer_vouchers 
WHERE voucher_code = (SELECT redemption_code FROM customer_rewards WHERE id = 6);

-- Check all reward vouchers untuk this customer
SELECT * FROM customer_vouchers 
WHERE source = 'reward' 
AND customer_profile_id = (SELECT customer_profile_id FROM customer_rewards WHERE id = 6);
```

---

## Expected Flow

### **Step 1: Reward Redemption** ✅
```php
// RewardRedemptionService.php
CustomerReward::create([...]);
// IF reward_type = 'voucher' THEN:
CustomerVoucher::create([
    'voucher_code' => 'RWD-XXXXX',
    'source' => 'reward'
]);
$customerReward->update(['redemption_code' => 'RWD-XXXXX']);
```

### **Step 2: Apply Voucher to Cart** 
```javascript
// cart-voucher.js
const redemptionId = '6'; // From reward_6
processVoucherTypeReward(rewardData, '6', modal, originalContent);

// Calls:
POST /customer/rewards/apply-voucher
{
    "customer_reward_id": "6"
}
```

### **Step 3: Backend Processing** 
```php
// RewardsController.php
1. Find CustomerReward WHERE id = 6
2. Check status = 'active'
3. Check reward_type = 'voucher'
4. Check not expired
5. Check redemption_code exists
6. Find CustomerVoucher WHERE voucher_code = redemption_code
7. Return voucher_id
```

### **Step 4: Apply to Cart**
```javascript
POST /customer/cart/apply-voucher
{
    "voucher_id": [voucher_id_from_step_3]
}
```

---

## Common Issues & Solutions

### **Issue A: CustomerReward Not Found**
**Symptoms**: 404 dari findOrFail  
**Solution**: 
- Verify ID exists dalam database
- Verify belongs to current user (customer_profile_id match)

### **Issue B: Status Not Active**
**Symptoms**: "This reward is no longer active"  
**Solution**: Check CustomerReward.status
- Should be 'active'
- If 'pending', need to mark as active first
- If 'redeemed', sudah used

### **Issue C: Reward Type Not Voucher**
**Symptoms**: "This reward is not a voucher type"  
**Solution**: 
- Check rewards.reward_type harus 'voucher'
- If 'points' atau 'product', different flow

### **Issue D: No Redemption Code**
**Symptoms**: "Voucher code not generated yet"  
**Solution**: 
- CustomerReward.redemption_code should be set when voucher created
- If empty, RewardRedemptionService may not have run properly
- Or reward_type !== 'voucher'

### **Issue E: CustomerVoucher Not Found**
**Symptoms**: "Voucher not found or already used"  
**Solution**: 
- Check customer_vouchers table untuk voucher_code match
- Check source = 'reward'
- Check status = 'active'
- Check not expired

---

## Prevention Tips

1. **Always clear Laravel cache** after adding/modifying routes
2. **Add comprehensive logging** untuk debugging
3. **Verify data consistency** between CustomerReward dan CustomerVoucher
4. **Test each step** individually:
   - ✅ Reward redemption creates both records
   - ✅ CustomerReward has redemption_code
   - ✅ CustomerVoucher has matching voucher_code
   - ✅ Apply voucher API returns voucher_id

---

## Quick Fix Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart Laravel server
php artisan serve

# Check routes
php artisan route:list | grep apply-voucher

# Check logs
tail -f storage/logs/laravel.log
```

---

**Status**: Ready for testing after fixes ✅
