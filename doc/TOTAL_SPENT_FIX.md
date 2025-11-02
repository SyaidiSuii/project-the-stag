# Fix: Customer total_spent Not Updated on Payment

## Problem
`customer_profiles.total_spent` was **always 0** despite successful payments because the system was only adding loyalty points but **not updating the total spending amount**.

## Root Cause
Payment flow in the following locations did NOT update `customer_profiles.total_spent`:
1. `PaymentService::handleGatewayCallback()` - Online payment callback
2. `PaymentController::placeOrder()` - Counter payment
3. `PaymentController::paymentReturn()` - Payment gateway return

## Solution Implemented

### 1. Added Methods to CustomerProfile Model
**File**: `app/Models/CustomerProfile.php`

```php
// New method: Update total_spent AND loyalty tier
public function updateTotalSpendingAndTier()
{
    $totalSpending = Order::where('user_id', $this->user_id)
        ->where('payment_status', 'paid')
        ->sum('total_amount');
    
    $this->update(['total_spent' => $totalSpending]);
    
    // Update loyalty tier based on new spending
    $tier = LoyaltyTier::active()
        ->where('minimum_spending', '<=', $totalSpending)
        ->orderBy('minimum_spending', 'desc')
        ->first();
    
    if ($tier && $this->loyalty_tier_id !== $tier->id) {
        $this->update(['loyalty_tier_id' => $tier->id]);
    }
    
    return $tier;
}

// Helper method: Add spending incrementally
public function addSpending(float $amount)
{
    $currentSpent = $this->total_spent ?? 0;
    $this->update(['total_spent' => $currentSpent + $amount]);
    
    // Also update loyalty tier if spending increased
    $this->updateTotalSpendingAndTier();
}
```

### 2. Updated Payment Flow
Updated the following files to call `addSpending()` when payment successful:

#### PaymentService.php (Line 482-501)
```php
// Award points AND update total_spent
if ($pendingOrderData['user_id']) {
    $user = \App\Models\User::find($pendingOrderData['user_id']);
    if ($user) {
        $points = floor($pendingOrderData['total_amount']);
        $user->addPoints($points, 'Order #' . $order->confirmation_code);
        
        // ✅ NEW: Update customer profile total spending
        if ($user->customerProfile) {
            $user->customerProfile->addSpending($pendingOrderData['total_amount']);
        }
    }
}
```

#### PaymentController.php - Counter Orders (Line 320-330)
```php
// ✅ NEW: Update customer profile total_spent for counter orders
if ($user && $user->customerProfile) {
    $user->customerProfile->addSpending($finalTotal);
}
```

#### PaymentController.php - Online Payment Return (Line 665-673)
```php
// ✅ NEW: Update customer profile total spending
if ($user->customerProfile) {
    $user->customerProfile->addSpending($pendingOrderData['total_amount']);
}
```

### 3. Created Fix Command
**File**: `app/Console/Commands/Loyalty/UpdateCustomerTotalSpent.php`

Command to recalculate and fix `total_spent` for existing users:

```bash
# Dry run (check what will be updated)
php artisan loyalty:update-total-spent --dry-run

# Update all users
php artisan loyalty:update-total-spent

# Update specific user
php artisan loyalty:update-total-spent --user_id=1
```

## Testing

### Before Fix
```
Admin User: RM 100.00 (should be RM 174.00)
johny: RM 0.00 (should be RM 4.00)
```

### After Fix
```bash
php artisan loyalty:update-total-spent
```

Output:
```
Found 5 users with customer profiles

User #1 - Admin User
  Current: RM 100.00
  Should be: RM 174.00
  Difference: RM 74.00
  ✅ Updated!

User #5 - johny
  Current: RM 0.00
  Should be: RM 4.00
  Difference: RM 4.00
  ✅ Updated!

Summary:
  Total users checked: 5
  Updated: 2
  Unchanged: 3
  Total spending added: RM 78.00
```

### Verification
```
Admin User: RM 174.00 ✅
johny: RM 4.00 ✅
```

## Impact
- ✅ **Past orders**: Fixed with `loyalty:update-total-spent` command
- ✅ **Future orders**: Will automatically update `total_spent` on payment success
- ✅ **Loyalty tiers**: Will also be recalculated based on new spending

## Files Changed
1. `app/Models/CustomerProfile.php` - Added spending tracking methods
2. `app/Services/PaymentService.php` - Update total_spent on gateway payment
3. `app/Http/Controllers/Customer/PaymentController.php` - Update total_spent on counter/online payment
4. `app/Console/Commands/Loyalty/UpdateCustomerTotalSpent.php` - Fix command for existing data

## Notes
- QR orders (guest orders without user_id) are NOT tracked as they have no customer profile
- Counter payments update `total_spent` immediately even if payment status is "pending" (payment will be confirmed at counter)
- The system now tracks spending incrementally using `addSpending()` method
