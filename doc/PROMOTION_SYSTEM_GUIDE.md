# 🎉 Promotion System - Complete Implementation Guide

**Project**: The Stag SmartDine Restaurant Management System
**Feature**: Comprehensive Promotion System (6 Types)
**Status**: ✅ **Backend 100% Complete** | ⏳ Frontend Pending
**Date**: 2025-10-12

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [What's Been Implemented](#whats-been-implemented)
3. [How to Use (Testing)](#how-to-use-testing)
4. [API Reference](#api-reference)
5. [Database Schema](#database-schema)
6. [Examples & Use Cases](#examples--use-cases)
7. [Next Steps (Views)](#next-steps-views)
8. [Integration Guide](#integration-guide)

---

## 🎯 System Overview

### Purpose
Transform dari simple "promo code" system kepada full-featured restaurant promotion system dengan 6 jenis promotion yang berbeza, sama seperti KFC & McDonald's.

### Key Features
- ✅ 6 promotion types (Promo Code, Combo Deal, Item Discount, Buy X Free Y, Bundle, Seasonal)
- ✅ Time-based restrictions (specific days, hours)
- ✅ Usage limits (total & per-customer)
- ✅ Automatic discount calculation
- ✅ Usage tracking & analytics
- ✅ Featured promotions
- ✅ Best promotion finder

---

## ✅ What's Been Implemented

### 1. Database Layer (5 Migrations)

#### Promotions Table (Extended)
```sql
-- New columns added:
promotion_type          -- 6 types supported
promo_config           -- JSON for type-specific data
badge_text             -- Display badge ("HOT DEAL!", "NEW!")
banner_color           -- Hex color for UI
max_discount_amount    -- Cap discount
terms_conditions       -- T&C text
applicable_days        -- JSON array for day restrictions
applicable_start_time  -- Time range start
applicable_end_time    -- Time range end
is_featured            -- Featured flag
display_order          -- Sort order
usage_limit_per_customer -- Per-user limit
total_usage_limit      -- Total limit
current_usage_count    -- Usage counter
```

#### New Tables
- `promotion_items` - Links promotions to menu items (for combos)
- `promotion_categories` - Links promotions to categories (for discounts)
- `promotion_usage_logs` - Complete usage tracking & analytics

#### OrderItems Table (Extended)
```sql
promotion_id           -- Link to promotion
is_combo_item         -- Combo flag
combo_group_id        -- Groups items in same combo
original_price        -- Price before discount
discount_amount       -- Discount applied
promotion_snapshot    -- JSON snapshot for history
```

---

### 2. Models Layer

#### Promotion Model (400+ lines)
**Location**: `app/Models/Promotion.php`

**Constants**:
```php
TYPE_COMBO_DEAL      // Combo meals
TYPE_ITEM_DISCOUNT   // Item/category discounts
TYPE_BUY_X_FREE_Y    // Buy X get Y free
TYPE_PROMO_CODE      // Voucher codes
TYPE_SEASONAL        // Seasonal offers
TYPE_BUNDLE          // Multi-item bundles
```

**Key Methods**:
```php
// Validation
$promotion->isValid()                    // Check if currently valid
$promotion->canBeUsedBy($userId)         // Check user can use
$promotion->getRemainingUses()           // Remaining uses

// Discount Calculation
$promotion->calculateDiscount($amount)   // Calculate discount

// Usage Tracking
$promotion->logUsage(...)                // Log promotion usage

// Accessors
$promotion->discount_text                // "10% OFF"
$promotion->type_label                   // "Promo Code"
$promotion->days_text                    // "Weekends only"
$promotion->usage_percentage             // Usage %
```

#### PromotionUsageLog Model
**Location**: `app/Models/PromotionUsageLog.php`

Tracks every promotion usage with:
- User, Order, Promotion details
- Discount amount, subtotal, total
- Session ID, IP address (for fraud detection)
- Timestamp

---

### 3. Service Layer

#### PromotionService (500+ lines)
**Location**: `app/Services/Promotions/PromotionService.php`

**Main Methods**:

```php
// Get Promotions
getActivePromotions(?User $user)              // All active promos
getFeaturedPromotions(?User $user)            // Featured only
getPromotionsByType(string $type, ?User $user) // Filter by type
getApplicablePromotions(array $cart, ?User)   // Applicable to cart

// Validate & Calculate
validatePromoCode(string $code, array $cart, ?User) // Validate code
calculatePromotionDiscount(Promotion, array $cart)  // Calculate discount
getBestPromotion(array $cart, ?User)                // Find best deal

// Apply & Track
applyPromotionToOrder(Promotion, $orderId, ...)     // Apply & log usage
getPromotionStats(Promotion $promotion)             // Get analytics

// Helpers
generateComboGroupId()                              // Generate unique ID
```

**Cart Format**:
```php
$cartItems = [
    1 => ['item' => MenuItem, 'quantity' => 2, 'price' => 25.00],
    2 => ['item' => MenuItem, 'quantity' => 1, 'price' => 15.00],
];
```

---

### 4. Controllers

#### Admin PromotionController
**Location**: `app/Http/Controllers/Admin/PromotionController.php`

**Methods**:
```php
index(Request $request)        // List with type/status filters
create(Request $request)       // Create form (dynamic by type)
store(Request $request)        // Save with type-specific logic
edit(Promotion $promotion)     // Edit form
update(Request, Promotion)     // Update
destroy(Promotion $promotion)  // Delete
toggleStatus(Promotion)        // Toggle active status
stats(Promotion)               // View statistics
duplicate(Promotion)           // Duplicate promo
```

#### Customer PromotionController
**Location**: `app/Http/Controllers/Customer/PromotionController.php`

**Methods**:
```php
index()                        // Browse all promotions (categorized)
show($id)                      // View promotion details
byType($type)                  // Browse by type
applyPromoCode(Request)        // Apply promo code to cart
removePromoCode()              // Remove promo code
getBestPromotion(Request)      // Get best deal for cart
activeHappyHours()             // Get active happy hours
```

---

## 🧪 How to Use (Testing)

### Via Tinker (For Testing Backend)

#### 1. Create Test Promotion
```php
php artisan tinker

// Create promo code
$promo = App\Models\Promotion::create([
    'name' => 'Test 15% Off',
    'promotion_type' => 'promo_code',
    'promo_code' => 'TEST15',
    'discount_type' => 'percentage',
    'discount_value' => 15,
    'minimum_order_value' => 50.00,
    'start_date' => now(),
    'end_date' => now()->addMonths(1),
    'is_active' => true,
    'promo_config' => [],
]);
```

#### 2. Test PromotionService
```php
$service = app(\App\Services\Promotions\PromotionService::class);

// Get active promotions
$active = $service->getActivePromotions();
echo "Active promotions: " . $active->count();

// Validate promo code
$cart = [1 => ['price' => 30, 'quantity' => 2]]; // RM60 cart
$promo = $service->validatePromoCode('TEST15', $cart);

if ($promo) {
    $discount = $service->calculatePromotionDiscount($promo, $cart);
    echo "Discount: RM" . $discount['discount']; // RM9.00 (15% of 60)
}
```

#### 3. Test Model Methods
```php
$promo = App\Models\Promotion::where('promo_code', 'TEST15')->first();

// Test validation
$promo->isValid();                    // true
$promo->canBeUsedBy(1);               // true (user ID 1)

// Test calculation
$promo->calculateDiscount(100);       // 15.00 (15% of 100)

// Test accessors
$promo->discount_text;                // "15% OFF"
$promo->type_label;                   // "Promo Code"
```

---

## 📡 API Reference

### Customer API Endpoints

#### Apply Promo Code
```http
POST /customer/promotions/apply-promo
Content-Type: application/json

{
  "promo_code": "WELCOME10",
  "cart_items": {
    "1": {"price": 25, "quantity": 2}
  }
}
```

**Response**:
```json
{
  "success": true,
  "message": "Promo code applied successfully!",
  "discount": 5.00,
  "promotion": {
    "name": "Welcome 10% Off",
    "discount_text": "10% OFF",
    "terms": "..."
  }
}
```

#### Get Best Promotion
```http
POST /customer/promotions/best-promotion
Content-Type: application/json

{
  "cart_items": {
    "1": {"price": 25, "quantity": 2}
  }
}
```

**Response**:
```json
{
  "success": true,
  "promotion": {
    "id": 1,
    "name": "Welcome 10% OFF",
    "type": "Promo Code",
    "discount": 5.00,
    "discount_text": "10% OFF"
  }
}
```

---

## 🗄️ Database Schema

### Promotions Table Structure
```sql
CREATE TABLE promotions (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  promotion_type ENUM('combo_deal','item_discount','buy_x_free_y','promo_code','seasonal','bundle'),
  promo_config JSON,
  image_path VARCHAR(255),
  badge_text VARCHAR(50),
  banner_color VARCHAR(7),
  promo_code VARCHAR(50) UNIQUE,
  discount_type ENUM('percentage','fixed'),
  discount_value DECIMAL(10,2),
  max_discount_amount DECIMAL(10,2),
  minimum_order_value DECIMAL(10,2),
  terms_conditions TEXT,
  start_date DATE,
  end_date DATE,
  applicable_days JSON,
  applicable_start_time TIME,
  applicable_end_time TIME,
  is_active BOOLEAN DEFAULT TRUE,
  is_featured BOOLEAN DEFAULT FALSE,
  display_order INT DEFAULT 0,
  usage_limit_per_customer INT,
  total_usage_limit INT,
  current_usage_count INT DEFAULT 0,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP
);
```

### Relationships
```
promotions
  ├─→ promotion_items (many-to-many with menu_items)
  ├─→ promotion_categories (many-to-many with categories)
  └─→ promotion_usage_logs (one-to-many)

order_items
  └─→ promotions (many-to-one)
```

---

## 💡 Examples & Use Cases

### Example 1: Promo Code (WELCOME10)
```php
// Configuration
[
  'name' => 'Welcome 10% Off',
  'promotion_type' => 'promo_code',
  'promo_code' => 'WELCOME10',
  'discount_type' => 'percentage',
  'discount_value' => 10,
  'minimum_order_value' => 30.00,
  'max_discount_amount' => 20.00,
]

// How it works:
Cart RM50 → 10% discount → RM5 off → Final: RM45
Cart RM150 → 10% (=RM15) → Capped at RM20 → Final: RM135
Cart RM20 → Below RM30 minimum → INVALID
```

### Example 2: Combo Deal (Lunch Special)
```php
// Configuration
[
  'name' => 'Lunch Special',
  'promotion_type' => 'combo_deal',
  'applicable_days' => ['monday','tuesday','wednesday','thursday','friday'],
  'applicable_start_time' => '11:00:00',
  'applicable_end_time' => '15:00:00',
  'promo_config' => [
    'combo_price' => 25.00,
    'original_price' => 35.00,
  ],
  // Menu items attached via pivot:
  // - Main dish (qty: 1, required: true)
  // - Side dish (qty: 1, required: true)
  // - Drink (qty: 1, required: true)
]

// How it works:
Normal price: RM35 (individual items)
Combo price: RM25
Savings: RM10
Available: Monday-Friday, 11AM-3PM only
```

### Example 3: Buy 1 Free 1 (Coffee)
```php
// Configuration
[
  'name' => 'Buy 1 Coffee Get 1 Free',
  'promotion_type' => 'buy_x_free_y',
  'promo_config' => [
    'buy_quantity' => 1,
    'buy_item_ids' => [10], // Coffee menu item ID
    'free_quantity' => 1,
    'free_item_ids' => [10], // Same item
    'max_free_items' => 5,
  ]
]

// How it works:
Customer buys 1 coffee → Gets 1 free
Customer buys 3 coffee → Gets 3 free (max 5)
Discount = Price of free items
```

### Example 4: Weekend Discount (20% Off)
```php
// Configuration
[
  'name' => '20% Off All Burgers',
  'promotion_type' => 'item_discount',
  'discount_type' => 'percentage',
  'discount_value' => 20,
  'applicable_days' => ['saturday','sunday'],
  // Categories attached: Burgers category
]

// How it works:
Applies 20% discount to all burger items
Only on weekends
Automatic - no code needed
```

---

## 📈 Next Steps (Views Implementation)

### Priority 1: Admin Views (Most Urgent)

#### Files to Create:
1. `resources/views/admin/promotions/index.blade.php` (UPDATE)
   - Add type filter tabs
   - Add stats column
   - Add duplicate button

2. `resources/views/admin/promotions/create.blade.php` (UPDATE)
   - Dynamic form based on type
   - JavaScript for type switching
   - Item picker for combos

3. `resources/views/admin/promotions/stats.blade.php` (NEW)
   - Usage statistics
   - Analytics charts
   - Export data button

#### Key UI Elements Needed:
- Type selector dropdown
- Menu item picker (for combos)
- Category picker (for discounts)
- Day selector checkboxes
- Time range inputs
- Usage limit inputs

### Priority 2: Customer Views

#### Files to Update:
1. `resources/views/customer/promotions/index.blade.php`
   - Categorize by 6 types
   - Featured section
   - Filter by type tabs

2. `resources/views/customer/promotions/show.blade.php`
   - Type-specific display
   - Terms & conditions
   - Apply button (for combos)

3. NEW: `resources/views/customer/promotions/by-type.blade.php`
   - Browse by specific type

### Priority 3: Cart Integration

#### Updates Needed:
1. `resources/views/customer/cart/index.blade.php`
   - Promo code input field
   - Applied promo display
   - Discount breakdown
   - "Find Best Deal" button

2. JavaScript:
   - AJAX promo code validation
   - Real-time discount calculation
   - Auto-apply best promotion

---

## 🔌 Integration Guide

### Step 1: Cart System Integration

#### In CartController:
```php
public function getCartWithPromotions()
{
    $cart = session('cart', []);
    $appliedPromo = session('applied_promo');

    if ($appliedPromo) {
        $discount = $appliedPromo['discount'];
        $subtotal = $this->calculateSubtotal($cart);
        $total = $subtotal - $discount;

        return compact('cart', 'appliedPromo', 'discount', 'subtotal', 'total');
    }

    return compact('cart');
}
```

#### In Checkout:
```php
// When creating order
if (session('applied_promo')) {
    $promo = session('applied_promo');
    $promotion = Promotion::find($promo['id']);

    // Log usage
    $promotionService->applyPromotionToOrder(
        $promotion,
        $order->id,
        $user->id,
        $promo['discount'],
        $subtotal,
        $total
    );

    // Store in order
    $order->update([
        'promotion_id' => $promotion->id,
        'discount_amount' => $promo['discount']
    ]);
}
```

### Step 2: Menu Item Badge Display

#### In Menu Views:
```blade
@foreach($menuItems as $item)
    @php
        $hasPromo = $item->activePromotions()->exists();
    @endphp

    <div class="menu-item">
        @if($hasPromo)
            <span class="badge badge-sale">ON SALE</span>
        @endif

        <h3>{{ $item->name }}</h3>
        <p>{{ $item->formatted_price }}</p>
    </div>
@endforeach
```

---

## ✅ Testing Checklist

Before going live, test:

- [ ] Create promo code → works
- [ ] Apply promo code to cart → discount calculated correctly
- [ ] Promo code below minimum → rejected
- [ ] Promo code max discount cap → applied correctly
- [ ] Create combo deal → items linked
- [ ] Time-based promo → only works during specified hours
- [ ] Day-based promo → only works on specified days
- [ ] Usage limit per customer → enforced
- [ ] Total usage limit → enforced
- [ ] Featured promotions → displayed correctly
- [ ] Promotion duplication → works
- [ ] Promotion stats → accurate
- [ ] Usage logging → recorded correctly

---

## 🎯 Summary

### ✅ Completed (70%):
- Database structure (5 migrations)
- Models with full business logic
- Service layer (all 6 promotion types)
- Admin & Customer controllers
- Routes updated
- Backend testing passed

### ⏳ Pending (30%):
- Admin views (create/edit forms)
- Customer views (promotion pages)
- Cart integration UI
- JavaScript for dynamic forms
- End-to-end testing

---

## 📞 Support & Documentation

### Test Reports:
- `PROMOTION_SYSTEM_TEST_RESULTS.md` - Complete test results

### Key Files:
- Models: `app/Models/Promotion.php`, `PromotionUsageLog.php`
- Service: `app/Services/Promotions/PromotionService.php`
- Controllers: `app/Http/Controllers/Admin/PromotionController.php`
- Migrations: `database/migrations/2025_10_12_*`

### Database:
```bash
# Check promotions
php artisan tinker
App\Models\Promotion::count()
App\Models\Promotion::all()

# Run seeder
php artisan db:seed --class=PromotionTestSeeder
```

---

**Implementation Date**: 2025-10-12
**Status**: Backend Complete ✅
**Ready for**: Views & Frontend Integration
**Next Action**: Create admin/customer views
