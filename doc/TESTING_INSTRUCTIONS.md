# Testing Instructions for Voucher/Rewards Flow

## 🔐 Test User Account
- **Email**: afiffhan@gmail.com
- **Status**: Has 3 test rewards ready

## 🧪 Testing Steps

### 1. Login
Login with the test account or your own customer account.

### 2. Navigate to Cart
- Click **Customer → Menu** from sidebar
- Click **cart icon** in sidebar

### 3. Apply Voucher/Reward
- Click **"Select Voucher"** button in cart
- You'll see rewards in "My Rewards" section:
  - **RM 10% Discount** (voucher type)
  - **Free Drink** (product type)
- Click **"Claim"** button

### 4. Expected Result
- ✅ Voucher: Discount applied to cart total, modal closes, page refreshes
- ✅ Free Item: Item added to cart, cart badge updates

## 🐛 If Rewards Still Don't Show

### Check Browser Console
1. Open Developer Tools (F12)
2. Go to Console tab
3. Look for errors when clicking "Select Voucher"

### Check Network Tab
1. Open Developer Tools (F12)
2. Go to Network tab
3. Filter by "XHR" or "Fetch"
4. Click "Select Voucher"
5. Look for API call to `/customer/cart/available-vouchers`
6. Check if it returns rewards in the response

### API Response Should Look Like:
```json
{
  "success": true,
  "vouchers": [],
  "rewards": [
    {
      "id": "reward_123",
      "type": "reward",
      "name": "RM 10% Discount",
      "discount_type": "percentage",
      ...
    }
  ]
}
```

## ✅ Issues Fixed
- ✅ CSRF token helper function added
- ✅ Missing functions restored (applyRewardToCartFromModal, etc.)
- ✅ Rewards pre-created for testing
- ✅ All fetch calls use proper CSRF token validation

## 🎯 Current Status
**READY FOR TESTING** - All fixes applied and test rewards created.
