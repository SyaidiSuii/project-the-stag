# 🧪 REWARDS SYSTEM TESTING GUIDE

## Pre-Testing Setup

### 1. Update View (Manual Step Required)
**File**: `resources/views/customer/rewards/index.blade.php`

Replace lines 206-215 (voucher display section) dengan code dari `REWARDS_REFACTOR_SUMMARY.md`

### 2. Verify Backend Changes
Check files dah modified:
- ✅ `app/Services/Loyalty/RewardRedemptionService.php`
- ✅ `app/Http/Controllers/Customer/RewardsController.php`
- ✅ `routes/web.php`
- ✅ `public/js/customer/rewards.js`

---

## Testing Checklist

### **Test 1: Backend - Voucher Creation**

**Step 1**: Admin creates voucher-type reward
- Go to Admin Panel → Rewards → Create New
- Type: "voucher"
- Points Required: 500
- Voucher Template: Select existing template
- Save

**Expected**: Reward created successfully

---

### **Test 2: Database - Dual Record Creation**

**Step 2**: Customer redeems reward
- Login as customer
- Go to /customer/rewards
- Find reward (500 points)
- Click "Redeem"

**Check Database**:
```sql
-- Should have CustomerReward record
SELECT * FROM customer_rewards WHERE customer_profile_id = [customer_id];

-- Should have CustomerVoucher record with source = 'reward'
SELECT * FROM customer_vouchers WHERE customer_profile_id = [customer_id] AND source = 'reward';
```

**Expected Results**:
- ✅ `customer_rewards` table: record dengan `status='active'`, `redemption_code='RWD-XXXXX'`
- ✅ `customer_vouchers` table: record dengan `source='reward'`, `status='active'`, `voucher_code='RWD-XXXXX'`

---

### **Test 3: Frontend - Voucher Code Display**

**Step 3**: Customer view "My Rewards"
- Go to /customer/rewards
- Look at redeemed rewards section

**Expected Results**:
- ✅ Voucher-type reward show "Voucher Code" section
- ✅ Show unique code (format: RWD-XXXXX)
- ✅ "Copy" button available
- ✅ "Apply to Cart" button visible

---

### **Test 4: JavaScript - Apply Voucher**

**Step 4**: Customer apply voucher to cart
- Click "Apply to Cart" button
- Check browser console untuk logs
- Watch for success/error messages

**Expected Results**:
- ✅ Console logs: "Applying voucher reward"
- ✅ Success message: "Voucher '[name]' applied successfully!"
- ✅ Cart refresh automatically
- ✅ Button shows loading state ("Applying...")

---

### **Test 5: Cart - Voucher Applied**

**Step 5**: Verify voucher applied in cart
- Go to /customer/food or /customer/cart
- Check applied voucher section

**Expected Results**:
- ✅ Applied voucher section show
- ✅ Voucher name dan discount displayed
- ✅ Cart total updated dengan discount

---

### **Test 6: Payment - Voucher Discount**

**Step 6**: Complete order with voucher
- Add items to cart
- Apply voucher (from step 4)
- Go to checkout/payment
- Review order summary

**Expected Results**:
- ✅ Voucher discount applied to order
- ✅ Final total = (Subtotal - Voucher Discount)
- ✅ No errors dalam console

---

### **Test 7: Payment Completion**

**Step 7**: Complete payment
- Proceed dengan payment (can use test payment)
- Wait for order confirmation

**Expected Results**:
- ✅ Order created successfully
- ✅ Voucher marked as redeemed
- ✅ CustomerReward marked as redeemed
- ✅ No errors

---

### **Test 8: Database - Post-Payment Status**

**Step 8**: Check database after payment
```sql
-- Check CustomerVoucher status
SELECT * FROM customer_vouchers WHERE source = 'reward' ORDER BY id DESC LIMIT 5;

-- Check CustomerReward status  
SELECT * FROM customer_rewards ORDER BY id DESC LIMIT 5;
```

**Expected Results**:
- ✅ `customer_vouchers.status = 'redeemed'`
- ✅ `customer_vouchers.redeemed_at` set
- ✅ `customer_rewards.status = 'redeemed'`
- ✅ `customer_rewards.redeemed_at` set

---

## **Regression Testing**

### **Test 9: Existing Voucher Collections**

**Step 9**: Test existing voucher collection system
- Create voucher template (source_type = 'collection')
- Customer collect voucher (not through rewards)
- Apply to cart
- Complete order

**Expected Results**:
- ✅ Collection vouchers still work
- ✅ No conflicts dengan reward vouchers
- ✅ Both systems coexist properly

---

## **Error Handling Tests**

### **Test 10: Expired Voucher**

**Step 10**: Apply expired voucher reward
- Edit customer_voucher record, set expiry_date to past date
- Try apply voucher in cart

**Expected Results**:
- ❌ Error message: "Voucher has expired"
- ❌ Voucher not applied
- ✅ No crashes

---

### **Test 11: Already Used Voucher**

**Step 11**: Try apply redeemed voucher
- Find voucher dengan status='redeemed'
- Try apply to cart

**Expected Results**:
- ❌ Error message: "Voucher already used"
- ❌ Voucher not applied
- ✅ No crashes

---

## **Console Commands untuk Debug**

```javascript
// Check rewardHandler state
console.log(window.rewardHandler.state);

// Check applied vouchers
console.log(window.rewardHandler.state.appliedVouchers);

// Check session applied voucher
console.log(sessionStorage.getItem('applied_voucher'));
// OR
console.log(JSON.parse(localStorage.getItem('smartdine_applied_voucher')));
```

---

## **Quick Test Commands**

```bash
# Check database records
mysql -u root -p -e "SELECT * FROM the_stag.customer_rewards ORDER BY id DESC LIMIT 5;"
mysql -u root -p -e "SELECT * FROM the_stag.customer_vouchers WHERE source='reward' ORDER BY id DESC LIMIT 5;"

# Check Laravel logs
tail -f d:/ProgramsFiles/laragon/www/the_stag/storage/logs/laravel.log

# Clear cache (if needed)
php artisan cache:clear
php artisan config:clear
```

---

## **Success Criteria**

All tests must pass:
- [ ] Admin creates voucher-type reward ✅
- [ ] Customer redeem creates BOTH records ✅
- [ ] Voucher code displayed properly ✅
- [ ] Apply to cart works ✅
- [ ] Payment processes voucher ✅
- [ ] Records marked as redeemed ✅
- [ ] Existing voucher collections still work ✅

---

**Ready untuk Testing**: Backend ✅ | Frontend ✅ | Manual view update ⚠️
