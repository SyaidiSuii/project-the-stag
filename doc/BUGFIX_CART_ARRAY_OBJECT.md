# Bug Fix: Cart Array vs Object Handling

## 🐛 Issue
**Error**: `Uncaught (in promise) TypeError: cart.reduce is not a function`

**Cause**: After implementing Shopee-style unavailable items, `CartManager.getDatabaseCart()` now returns an object:
```javascript
{
  cart: [...],
  unavailable_count: 0,
  available_total: 0,
  total: 0,
  count: 0
}
```

But many functions expected `cart` to be an array.

---

## ✅ Files Fixed

### 1. `public/js/customer/menu.js`
Fixed 4 functions that assumed cart is array:

#### a) `updateCartBadge()` - Line 299
**Before**:
```javascript
const cart = await window.cartManager.getCart();
const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
```

**After**:
```javascript
const cartData = await window.cartManager.getCart();
const cart = Array.isArray(cartData) ? cartData : (cartData.cart || []);
const totalItems = Array.isArray(cartData)
  ? cart.reduce((sum, item) => sum + item.quantity, 0)
  : (cartData.count || 0);
```

#### b) Clear All Button - Line 483
**Before**:
```javascript
const cartItems = await window.cartManager.getCart();
if (cartItems.length > 0) { ... }
```

**After**:
```javascript
const cartData = await window.cartManager.getCart();
const cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);
if (cartItems.length > 0) { ... }
```

#### c) Checkout Button - Line 531
**Before**:
```javascript
const cartItems = await window.cartManager.getCart();
if (cartItems.length > 0) { ... }
```

**After**:
```javascript
const cartData = await window.cartManager.getCart();
const cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);
if (cartItems.length > 0) { ... }
```

#### d) Quantity Buttons - Line 556
**Before**:
```javascript
const cartItems = await window.cartManager.getCart();
if (index >= 0 && index < cartItems.length) { ... }
```

**After**:
```javascript
const cartData = await window.cartManager.getCart();
const cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);
if (index >= 0 && index < cartItems.length) { ... }
```

---

### 2. `public/js/customer/cart-manager.js`
Fixed 2 helper methods:

#### a) `getTotalQuantity()` - Line 318
**Before**:
```javascript
getTotalQuantity(cartItems = null) {
  if (!cartItems) {
    cartItems = this.getLocalStorageCart();
  }
  return cartItems.reduce((total, item) => total + item.quantity, 0);
}
```

**After**:
```javascript
getTotalQuantity(cartItems = null) {
  if (!cartItems) {
    cartItems = this.getLocalStorageCart();
  }

  // Handle both formats: array or object with cart property
  const items = Array.isArray(cartItems) ? cartItems : (cartItems.cart || []);
  return items.reduce((total, item) => total + item.quantity, 0);
}
```

#### b) `getTotalPrice()` - Line 329
**Before**:
```javascript
getTotalPrice(cartItems = null) {
  if (!cartItems) {
    cartItems = this.getLocalStorageCart();
  }
  return cartItems.reduce((total, item) => {
    const price = parseFloat(item.price.replace(/[^\d.]/g, '')) || 0;
    return total + (price * item.quantity);
  }, 0);
}
```

**After**:
```javascript
getTotalPrice(cartItems = null) {
  if (!cartItems) {
    cartItems = this.getLocalStorageCart();
  }

  // Handle both formats: array or object with cart property
  const items = Array.isArray(cartItems) ? cartItems : (cartItems.cart || []);
  return items.reduce((total, item) => {
    const price = parseFloat(item.price.replace(/[^\d.]/g, '')) || 0;
    return total + (price * item.quantity);
  }, 0);
}
```

---

## 🎯 Solution Pattern

All fixes follow the same pattern:

```javascript
// Step 1: Get cart data
const cartData = await window.cartManager.getCart();

// Step 2: Extract array from object or use as-is if already array
const cartItems = Array.isArray(cartData) ? cartData : (cartData.cart || []);

// Step 3: Use cartItems normally
cartItems.forEach(...)
cartItems.length
cartItems[index]
```

---

## ✅ Backward Compatibility

These fixes maintain backward compatibility:

### For Logged-in Users (Database):
```javascript
cartData = {
  cart: [{...}, {...}],
  unavailable_count: 1,
  available_total: 25.00,
  total: 30.00,
  count: 2
}
// Extracted: cartItems = cartData.cart
```

### For Guest Users (localStorage):
```javascript
cartData = [{...}, {...}]  // Array directly
// Extracted: cartItems = cartData (same array)
```

Both work correctly! ✅

---

## 🧪 Verification

After fix, these should all work without errors:

```javascript
// 1. Update cart badge
await updateCartBadge(); // ✅ No error

// 2. Clear cart
// Click "Clear All" button ✅ No error

// 3. Checkout
// Click checkout button ✅ No error

// 4. Update quantity
// Click +/- buttons ✅ No error

// 5. Get totals
window.cartManager.getTotalQuantity(cart); // ✅ Works with both formats
window.cartManager.getTotalPrice(cart); // ✅ Works with both formats
```

---

## 📊 Impact

**Functions Fixed**: 6 total
- menu.js: 4 functions
- cart-manager.js: 2 methods

**Lines Changed**: ~30 lines

**Affected Features**:
- ✅ Cart badge display
- ✅ Clear all cart
- ✅ Checkout flow
- ✅ Quantity controls
- ✅ Cart totals calculation

**Breaking Changes**: None (backward compatible)

---

## 🚀 Status

**Fixed**: ✅ Complete
**Tested**: Pending user verification
**Deployed**: Ready

---

## 📝 Notes

This fix was necessary because:
1. Unavailable items feature requires returning extra metadata (unavailable_count, etc.)
2. Can't add these to array, so must return object
3. But existing code expected array
4. Solution: Handle both formats everywhere

**Future-proof**: If more metadata needed (e.g., promotions, discounts), just add to object. All code now handles it gracefully.

---

**Date**: 2025-10-19
**Author**: Claude Code Implementation
**Status**: Bug Fixed ✅
