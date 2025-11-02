# 🎁 Cara Buat Voucher "New Customer Discount"

**Date**: 31 Oktober 2025

---

## 📋 Overview

Ada beberapa cara untuk buat voucher discount untuk new customer. Saya akan tunjukkan semua cara, dari yang paling mudah hingga yang paling advanced.

---

## 🎯 Cara 1: Manual Create Voucher Template (Paling Mudah)

### Step 1: Create Voucher Template

1. **Login sebagai Admin**
2. Navigate to: `http://localhost/the_stag/admin/rewards`
3. Click tab **"Voucher Templates"**
4. Click button **"New Template"**

### Step 2: Fill Form

```
=== BASIC INFORMATION ===
Template Name: New Customer Welcome Voucher
Description: Welcome discount for first-time customers - enjoy 10% off your first order!

=== DISCOUNT SETTINGS ===
Discount Type: Percentage (%)
Discount Value: 10

ATAU kalau nak fixed amount:
Discount Type: Fixed Amount (RM)
Discount Value: 5.00

Minimum Order (RM): 20.00
Max Discount (RM): 10.00
(Kalau percentage, maximum discount adalah RM10 - so order RM200 dapat discount RM10 je, bukan RM20)

=== USAGE LIMITS ===
Total Usage Limit: 100
(Hanya 100 vouchers boleh diissue untuk campaign ni)

Per User Limit: 1
(Setiap customer boleh guna 1 kali je - perfect untuk new customer!)

=== VALIDITY PERIOD ===
Valid From: (today atau start date campaign)
Valid Until: (30 days from now atau end date campaign)

=== TERMS & CONDITIONS ===
"For new customers only. One-time use per customer. Minimum order RM20. Valid for 30 days from date of issue. Cannot be combined with other promotions."

=== STATUS ===
☑ Active (check this box)
```

5. Click **"Create Template"**

✅ **Done!** Voucher template dah created.

---

## 🎯 Cara 2: Link Voucher dengan Reward (Auto-Issue)

Lepas create voucher template, awak boleh link dengan reward supaya bila customer redeem reward, auto dapat voucher.

### Step 1: Create Reward

1. Navigate to: `Admin Dashboard → Rewards tab → New Reward`
2. Fill form:

```
Title: New Customer Welcome Reward
Description: Redeem this to get your welcome voucher!
Reward Type: Voucher
Points Required: 0
(Set to 0 kalau nak free untuk new customer, atau 10-50 points kalau nak mereka collect points dulu)

Link to Voucher Template: New Customer Welcome Voucher
(Select dari dropdown)

Usage Limit per User: 1
(Sekali je boleh redeem)

Expiry Days: 30
(Valid for 30 days after redemption)

Active: ☑
```

3. Click **"Create Reward"**

**Result**: Bila customer redeem reward ni, system auto-issue voucher kepada mereka!

---

## 🎯 Cara 3: Create Voucher Collection Campaign

Ini cara untuk buat campaign "Spend RM50, Get Voucher".

### Step 1: Create Voucher Collection

1. Navigate to: `Admin Dashboard → Voucher Collections tab → New Collection`
2. Fill form:

```
Collection Name: New Customer Spend & Earn
Description: Spend RM50 on your first order and unlock exclusive voucher!

Spending Requirement (RM): 50.00
(Customer kena spend minimum RM50 untuk collect voucher)

Valid Until: (campaign end date)

Active: ☑
```

3. Click **"Create Collection"**

**Result**: Voucher akan appear dalam customer rewards portal di section "Collect Vouchers". Customer boleh click "Collect" bila mereka qualified.

---

## 🚀 Cara 4: Auto-Issue Upon Registration (Advanced)

Ini cara paling best - automatically issue voucher bila customer register account baru.

### Implementation

Kita perlu tambah code dalam `UserObserver` untuk auto-issue voucher kepada new customer.

**File**: `app/Observers/UserObserver.php`

Tambah method baru:

```php
use App\Models\VoucherTemplate;
use App\Services\Loyalty\VoucherService;

/**
 * Handle the User "created" event
 * Auto-issue welcome voucher to new customers
 */
public function created(User $user): void
{
    // Check if this is a customer (has customer role)
    if ($user->hasRole('customer')) {
        $this->issueWelcomeVoucher($user);
    }
}

/**
 * Issue welcome voucher to new customer
 */
protected function issueWelcomeVoucher(User $user): void
{
    try {
        // Find "New Customer Welcome Voucher" template
        $template = VoucherTemplate::where('name', 'New Customer Welcome Voucher')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            \Log::warning('Welcome voucher template not found for new user: ' . $user->id);
            return;
        }

        // Issue voucher to new customer
        $voucherService = app(VoucherService::class);
        $voucher = $voucherService->issueVoucher($user, $template, 'new_customer_registration');

        \Log::info('Welcome voucher issued to new customer', [
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'template_name' => $template->name
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to issue welcome voucher to new customer', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
    }
}
```

**Result**: Setiap new customer yang register akan automatically dapat welcome voucher!

---

## 📊 Comparison: Which Method to Use?

| Method | Pros | Cons | Best For |
|--------|------|------|----------|
| **Manual Template** | Simple, full control | Need manual distribution | Small campaigns |
| **Link dengan Reward** | Customer feels rewarded | Customer must redeem manually | Engagement-focused |
| **Voucher Collection** | Encourages spending | Requires minimum purchase | Revenue-focused |
| **Auto-Issue on Registration** | Fully automated, best UX | Requires code change | Scalable, professional |

---

## 💡 Best Practice Recommendation

**Recommended Approach**: **Combo Method**

1. **Create Voucher Template** (Cara 1) untuk define voucher
2. **Auto-Issue on Registration** (Cara 4) untuk automatically give to new customer
3. **OPTIONAL**: Juga buat Reward (Cara 2) untuk customer yang miss auto-issue

**Why this combo?**
- ✅ Automatic - no manual work
- ✅ Guaranteed delivery - all new customers get voucher
- ✅ Backup option - if auto-issue fails, customer can redeem via reward
- ✅ Professional - like Grab, Foodpanda, Shopee

---

## 🧪 Testing

### Test New Customer Voucher Flow:

**Step 1: Create Voucher Template**
```
1. Login as admin
2. Go to admin/rewards → Voucher Templates
3. Create "New Customer Welcome Voucher" dengan 10% discount
4. Set per_user_limit = 1
5. Save
```

**Step 2: Test Manual Issue**
```
1. Login as admin
2. Go to admin/rewards → Members tab
3. Find a customer
4. (Would need to add "Issue Voucher" button - currently not in UI)
```

**Step 3: Test via Reward Redemption**
```
1. Create reward linked to voucher template
2. Login as customer
3. Go to customer/rewards
4. Redeem the reward
5. Check "My Vouchers" section - voucher should appear!
```

**Step 4: Test Auto-Issue** (if implemented)
```
1. Register new customer account
2. Login as that customer
3. Go to customer/rewards
4. Check "My Vouchers" - welcome voucher should be there!
```

---

## 🎨 Customer View - How Voucher Appears

When customer go to `http://localhost/the_stag/customer/rewards`, mereka akan nampak:

### My Voucher Collection Section:

```
┌─────────────────────────────────┐
│ 🎁 New Customer Welcome Voucher│
│                                  │
│ 10% OFF                          │
│ Welcome discount for first order │
│                                  │
│ Expires: Nov 30, 2025           │
│                                  │
│ [USE NOW] ← Click this           │
└─────────────────────────────────┘
```

When click "USE NOW":
- Redirects to menu page
- Voucher akan available dalam cart untuk apply
- Discount automatically applied during checkout

---

## 📝 Important Notes

### Voucher Usage Flow:

1. **Customer receives voucher** (via auto-issue, redemption, atau collection)
2. **Voucher appears** dalam "My Voucher Collection"
3. **Customer browses menu** and adds items to cart
4. **At checkout**, customer dapat option to apply voucher
5. **Discount applied** to order total
6. **Voucher marked as used** after order completed

### Key Validations (Automatic):

- ✅ **Minimum order check** - Order must meet minimum_order_amount
- ✅ **Expiry check** - Voucher not expired
- ✅ **Usage limit check** - Not exceed per_user_limit
- ✅ **One-time use** - Cannot reuse same voucher
- ✅ **Status check** - Voucher must be active

All validation handled by `VoucherService` automatically!

---

## 🔧 Admin Management

### View All Vouchers Issued:

```bash
php artisan tinker

// See all issued vouchers
\App\Models\CustomerVoucher::with(['user', 'voucherTemplate'])->get();

// See how many customers got welcome voucher
\App\Models\CustomerVoucher::whereHas('voucherTemplate', function($q) {
    $q->where('name', 'New Customer Welcome Voucher');
})->count();

// See usage rate
$total = \App\Models\CustomerVoucher::where('voucher_template_id', 1)->count();
$used = \App\Models\CustomerVoucher::where('voucher_template_id', 1)
    ->where('status', 'used')->count();
$rate = ($used / $total) * 100;
echo "Usage rate: {$rate}%";
```

---

## 🎯 Quick Start Summary

**Fastest way to create new customer voucher NOW**:

```bash
1. Login admin → http://localhost/the_stag/admin/rewards
2. Click "Voucher Templates" tab
3. Click "New Template"
4. Fill:
   - Name: "New Customer Welcome"
   - Discount Type: Percentage
   - Value: 10
   - Min Order: 20
   - Per User Limit: 1
5. Click "Create"
6. Done! ✅
```

**To distribute voucher**:
- Option A: Link dengan reward (customer redeem manually)
- Option B: Implement auto-issue code (automatic distribution)
- Option C: Create voucher collection campaign (spend & earn)

---

## 📞 Need Help?

Common issues:

**Q: Voucher tidak appear dalam customer portal?**
A: Check:
1. Voucher template is active?
2. CustomerVoucher record created dengan correct user_id?
3. Voucher not expired?
4. Check `customer_vouchers` table dalam database

**Q: Discount tidak apply dalam cart?**
A: Check:
1. CartController has voucher apply logic?
2. Order amount meets minimum_order_amount?
3. Voucher status = 'active'?
4. Check browser console for JavaScript errors

**Q: Auto-issue not working?**
A: Check:
1. UserObserver registered dalam `AppServiceProvider`?
2. Voucher template name exactly matches code?
3. Check logs: `tail -f storage/logs/laravel.log`

---

**🎉 SEKARANG AWAK DAH TAHU CARA BUAT NEW CUSTOMER VOUCHER!**

Pilih method yang sesuai dengan needs awak:
- Simple & quick: Cara 1 (Manual template)
- Automated & professional: Cara 4 (Auto-issue)
- Engagement-focused: Cara 2 (Link dengan reward)
- Revenue-focused: Cara 3 (Voucher collection campaign)
