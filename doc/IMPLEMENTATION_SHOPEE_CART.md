# Shopee-Style Unavailable Cart Items Implementation

## ✅ Implementation Complete (Backend)

### Overview
Implemented "Shopee-style" cart behavior where deleted/unavailable menu items remain visible in cart (grayed out) instead of auto-deleting, giving users transparency and control.

---

## 📋 What Was Implemented

### 1. Database Changes ✅
**File**: `database/migrations/2025_10_19_222753_modify_user_carts_foreign_key_and_add_tracking.php`

**Changes**:
- Removed CASCADE DELETE on `menu_item_id` foreign key
- Changed to RESTRICT to prevent auto-deletion when menu items are deleted
- Added `last_checked_at` timestamp - tracks when item availability was last verified
- Added `unavailable_since` timestamp - tracks when item became unavailable

**Result**: Cart items persist even when menu items are soft deleted

---

### 2. UserCart Model Enhancements ✅
**File**: `app/Models/UserCart.php`

**New Methods**:
```php
isMenuItemAvailable()                    // Check if menu item exists & available
getAvailabilityStatus()                  // Get detailed availability status with reason
markAsUnavailable()                      // Mark item as unavailable with timestamp
markAsAvailable()                        // Clear unavailable timestamp
updateLastChecked()                      // Update last checked timestamp
getAvailableCartTotal($userId)           // Get total for available items only
getUnavailableCount($userId)             // Count unavailable items
scopeUnavailableForDays($query, $days)   // Query items unavailable for X+ days
```

**Key Feature**: Relationship includes soft-deleted menu items with `->withTrashed()`

---

### 3. Cart Controller Updates ✅
**File**: `app/Http/Controllers/Customer/CartController.php`

**Enhanced `index()` method**:
- Returns availability status for each cart item
- Automatically marks items as unavailable/available
- Provides separate totals (all items vs available items only)
- Returns unavailable count for UI notifications

**API Response Example**:
```json
{
  "success": true,
  "cart": [
    {
      "id": 123,
      "name": "Nasi Lemak",
      "is_available": false,
      "availability_reason": "deleted",
      "availability_message": "Produk telah dikeluarkan oleh penjual",
      "unavailable_since": "2 days ago"
    }
  ],
  "total": 45.50,
  "available_total": 30.00,
  "count": 3,
  "unavailable_count": 1
}
```

**New Method**: `removeUnavailableItems()` - Bulk delete unavailable items

**New Route**: `DELETE /customer/cart/remove-unavailable`

---

### 4. Checkout Validation ✅

#### Customer Payment Controller
**File**: `app/Http/Controllers/Customer/PaymentController.php`

- Validates menu items before checkout
- Auto-filters unavailable items
- Returns warning with skipped items list
- Prevents checkout if ALL items unavailable

#### QR Menu Controller
**File**: `app/Http/Controllers/QR/MenuController.php`

- Same validation for QR orders
- Updates cart prices from current menu items
- Filters out deleted/unavailable items

**Response Example** (if items skipped):
```json
{
  "success": true,
  "order_id": "STG-20251019-456",
  "warning": "2 item(s) tidak tersedia dan telah dikecualikan dari pesanan",
  "skipped_items": [
    {
      "id": 123,
      "name": "Nasi Lemak",
      "reason": "Item telah dikeluarkan"
    }
  ]
}
```

---

### 5. Admin Warning System ✅
**File**: `app/Http/Controllers/Admin/MenuItemController.php`

**Enhanced `destroy()` method**:
- Checks how many users have item in cart before deletion
- Shows warning message with cart count
- Informs admin that item will appear as unavailable in user carts

**Example Message**:
```
Menu item deleted successfully. Note: 5 user(s) had this item in their cart.
The item will appear as unavailable in their carts.
```

---

### 6. Auto-Cleanup System ✅

#### Cleanup Command
**File**: `app/Console/Commands/CleanupUnavailableCartItems.php`

**Command**: `php artisan cart:cleanup-unavailable`
**Options**: `--days=7` (default: 7 days, Shopee-style)

**Function**:
- Finds cart items unavailable for 7+ days
- Deletes them automatically
- Logs each deletion with details

#### Scheduled Task
**File**: `app/Console/Kernel.php`

**Schedule**: Daily at 2:00 AM
```php
$schedule->command('cart:cleanup-unavailable')->dailyAt('02:00');
```

---

## 🎨 Frontend Integration Required

### What Needs to Be Done

The backend API is complete and ready. You need to update the frontend JavaScript to:

### 1. Update Cart Display Logic

**Current Behavior**: Cart items shown normally

**Required Behavior**:
- Show unavailable items with visual styling (grayed out, blur effect)
- Add status badges ("Tidak tersedia", "Produk telah dikeluarkan")
- Disable checkboxes/quantity controls for unavailable items
- Show warning banner if unavailable items exist

**API Endpoint**: `GET /customer/cart`

**Response Fields to Use**:
```javascript
{
  is_available: false,                          // Use for conditional rendering
  availability_message: "Produk telah...",     // Show to user
  unavailable_count: 2                          // For banner notification
}
```

### 2. CSS Styling for Unavailable Items

**Suggested Styles**:
```css
.cart-item.unavailable {
  opacity: 0.5;
  filter: grayscale(100%);
  pointer-events: none;
}

.cart-item.unavailable::after {
  content: "Tidak tersedia";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(239, 68, 68, 0.9);
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
}
```

### 3. Warning Banner

**Display When**: `unavailable_count > 0`

**Example**:
```html
<div class="cart-warning-banner">
  ⚠️ 2 items tidak tersedia lagi
  <button onclick="removeUnavailableItems()">Keluarkan semua</button>
</div>
```

### 4. Bulk Remove Function

**API Endpoint**: `DELETE /customer/cart/remove-unavailable`

**JavaScript Example**:
```javascript
async function removeUnavailableItems() {
  const response = await fetch('/customer/cart/remove-unavailable', {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  });

  const data = await response.json();

  if (data.success) {
    showNotification(data.message);
    refreshCart(); // Reload cart display
  }
}
```

### 5. Checkout Validation Handling

**Handle Warnings from Checkout Response**:
```javascript
if (response.warning) {
  showModal({
    title: 'Beberapa item tidak dimasukkan',
    message: response.warning,
    skippedItems: response.skipped_items,
    confirmText: 'OK, Teruskan'
  });
}
```

---

## 🧪 Testing Guide

### Manual Testing Steps

#### Test 1: Soft Delete Menu Item
1. Add menu item to cart
2. Admin soft deletes the menu item
3. Open cart → Item should show as unavailable (grayed out)
4. Try checkout → Should proceed without that item

#### Test 2: Toggle Availability
1. Add menu item to cart
2. Admin sets `availability = false`
3. Open cart → Item should show "Tidak tersedia buat masa ini"

#### Test 3: Bulk Remove
1. Have 2-3 unavailable items in cart
2. Click "Remove all unavailable" button
3. All unavailable items should be deleted

#### Test 4: Auto-Cleanup (7 days)
1. Manually set `unavailable_since` to 8 days ago in database
2. Run command: `php artisan cart:cleanup-unavailable`
3. Item should be deleted

#### Test 5: Checkout Validation
1. Cart with mix of available & unavailable items
2. Proceed to checkout
3. Should create order with available items only
4. Should show warning about skipped items

---

## 📊 Database State

### Before Implementation
```
user_carts:
- Foreign key: CASCADE DELETE (items auto-deleted when menu deleted)
- No tracking timestamps
```

### After Implementation
```
user_carts:
- Foreign key: RESTRICT (items persist, prevents force delete)
- last_checked_at: tracks availability checks
- unavailable_since: tracks when item became unavailable
```

---

## 🔄 User Flow Comparison

### OLD Behavior (Before):
1. User adds item to cart
2. Admin deletes menu item
3. ❌ **Cart item disappears** (CASCADE DELETE)
4. User confused: "Where did my item go?"

### NEW Behavior (After - Shopee-style):
1. User adds item to cart
2. Admin deletes menu item
3. ✅ **Cart item stays** (grayed out, marked unavailable)
4. User sees: "Produk telah dikeluarkan oleh penjual"
5. User can:
   - Remove manually
   - Remove all unavailable items at once
   - Proceed to checkout (item auto-excluded)
6. After 7 days → Auto-cleaned by system

---

## 🛠️ Admin Experience

### When Deleting Menu Item:
**Before**:
```
Menu item deleted successfully
```

**After**:
```
Menu item deleted successfully.
Note: 5 user(s) had this item in their cart.
The item will appear as unavailable in their carts.
```

---

## 📁 Files Modified Summary

### New Files (2):
1. `database/migrations/2025_10_19_222753_modify_user_carts_foreign_key_and_add_tracking.php`
2. `app/Console/Commands/CleanupUnavailableCartItems.php`

### Modified Files (6):
1. `app/Models/UserCart.php` - Added helper methods & withTrashed()
2. `app/Http/Controllers/Customer/CartController.php` - Availability logic
3. `app/Http/Controllers/Customer/PaymentController.php` - Checkout validation
4. `app/Http/Controllers/QR/MenuController.php` - QR checkout validation
5. `app/Http/Controllers/Admin/MenuItemController.php` - Admin warning
6. `app/Console/Kernel.php` - Scheduled cleanup
7. `routes/web.php` - New route for bulk remove

---

## 🚀 Ready for Production

### ✅ Backend Complete
- Database schema updated
- Models enhanced with methods
- Controllers handle unavailable items
- Validation prevents invalid orders
- Auto-cleanup scheduled
- Admin warnings implemented

### ⏳ Frontend Pending
- Cart UI needs styling for unavailable items
- Warning banner needs implementation
- Bulk remove button needs wiring
- Checkout flow needs warning modal

---

## 💡 Next Steps

1. **Update Frontend JavaScript** - Integrate with new API response fields
2. **Add CSS Styling** - Visual indicators for unavailable items
3. **Test Thoroughly** - Follow testing guide above
4. **Deploy** - Run migration in production: `php artisan migrate`

---

## 📞 Support

**Questions?**
- Check API responses match expected format
- Test each scenario in testing guide
- Verify migration ran successfully: `php artisan migrate:status`
- Check scheduled cleanup: `php artisan schedule:list`

**Manual Cleanup**: `php artisan cart:cleanup-unavailable --days=7`

---

## 🎉 Benefits

1. **User Transparency** - Users see what happened to their cart items
2. **Better UX** - No surprise disappearances like Shopee/Lazada
3. **User Control** - Users decide when to remove unavailable items
4. **No Invalid Orders** - Validation prevents checkout with deleted items
5. **Auto-Cleanup** - Old unavailable items don't accumulate forever
6. **Admin Awareness** - Admins know impact of deleting items

---

**Implementation Date**: 2025-10-19
**Status**: Backend Complete ✅ | Frontend Pending ⏳
**Style**: Shopee/Lazada e-commerce pattern
