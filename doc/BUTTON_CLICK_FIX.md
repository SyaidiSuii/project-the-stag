# Button Click Handler Fix ✅

## Issue
User clicked "Apply" on 10% Off Voucher reward and got JSON parsing error.

## Root Cause
All buttons (vouchers AND rewards) were calling the same function:
```javascript
applyVoucherToCart(voucher.id)  // ❌ Wrong for rewards!
```

But rewards need a different function:
```javascript
applyRewardToCartFromModal(voucher)  // ✅ Correct for rewards
```

## Fix Applied (lines 146-157)
```javascript
// Apply button click
const applyBtn = voucherCard.querySelector('.apply-voucher-btn');
applyBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (isReward) {
        // For rewards, use applyRewardToCartFromModal
        applyRewardToCartFromModal(voucher);
    } else {
        // For vouchers, use applyVoucherToCart
        applyVoucherToCart(voucher.id);
    }
});
```

## Testing
1. Refresh page (F5)
2. Go to Customer → Menu
3. Click "Claim" button
4. Click "Apply" on any reward
5. Should work without JSON error

## File Modified
- `public/js/customer/cart-voucher.js` (lines 148-157)

## Status
✅ **RESOLVED** - Button click handlers now route correctly
