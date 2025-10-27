# Test Recommendation System

## Quick Test Guide

### Test 1: Smart Rules (No AI Required)

```bash
php artisan tinker
```

Then run:
```php
// Get the service
$service = app(\App\Services\RecommendationService::class);

// Test for user ID 1 (change to any user with orders)
$recommendations = $service->getRecommendations(1, 10);

// See the results
dd($recommendations);
```

**Expected:** Array of menu item IDs like `[12, 45, 23, 67, ...]`

---

### Test 2: Recommendations with Reasons

```php
$service = app(\App\Services\RecommendationService::class);

$recommendations = $service->getRecommendationsWithReasons(1, 5);

// Pretty print
foreach ($recommendations as $rec) {
    echo "Item #{$rec['menu_item_id']} - {$rec['reason']} (Score: {$rec['score']})\n";
}
```

**Expected Output:**
```
Item #45 - Frequently ordered with your favorites (Score: 0.9)
Item #23 - Perfect for lunch (Score: 0.7)
Item #67 - Complements your usual orders (Score: 0.6)
Item #12 - Trending this week (Score: 0.5)
Item #89 - Customer favorite (Score: 0.4)
```

---

### Test 3: AI Service (If Running)

**With AI service running:**
```php
config(['services.ai_recommender.enabled' => true]);
$recommendations = $service->getRecommendations(1, 10);

// Check logs to see which was used
// storage/logs/laravel.log will show:
// "AI recommendations successful" OR
// "Using smart rule-based recommendations"
```

**Without AI service:**
```php
config(['services.ai_recommender.enabled' => false]);
$recommendations = $service->getRecommendations(1, 10);

// Will always use smart rules
```

---

### Test 4: With Exclusions

```php
// Exclude certain items (e.g., already in cart)
$cartItems = [1, 2, 3];

$recommendations = $service->getRecommendations(
    userId: 1,
    limit: 10,
    excludeItems: $cartItems
);

// Items 1, 2, 3 will NOT be in recommendations
dd($recommendations);
```

---

### Test 5: See Actual Menu Items

```php
use App\Models\MenuItem;

$service = app(\App\Services\RecommendationService::class);
$recommendedIds = $service->getRecommendations(1, 10);

// Load the actual menu items
$items = MenuItem::whereIn('id', $recommendedIds)->get();

foreach ($items as $item) {
    echo "{$item->name} - RM{$item->price}\n";
}
```

**Expected Output:**
```
Beef Burger - RM15.90
French Fries - RM6.50
Coca-Cola - RM3.50
Ice Cream Sundae - RM8.00
...
```

---

### Test 6: Different Times of Day

```php
use Carbon\Carbon;

$service = app(\App\Services\RecommendationService::class);

// Test breakfast recommendations
Carbon::setTestNow(Carbon::parse('2025-10-25 08:00:00'));
$breakfast = $service->getRecommendationsWithReasons(1, 5);

// Test lunch recommendations
Carbon::setTestNow(Carbon::parse('2025-10-25 12:00:00'));
$lunch = $service->getRecommendationsWithReasons(1, 5);

// Test dinner recommendations
Carbon::setTestNow(Carbon::parse('2025-10-25 19:00:00'));
$dinner = $service->getRecommendationsWithReasons(1, 5);

// Reset time
Carbon::setTestNow();

// Compare results
dump('Breakfast:', $breakfast);
dump('Lunch:', $lunch);
dump('Dinner:', $dinner);
```

---

## Troubleshooting

### "Class SimpleRecommendationService not found"

Run:
```bash
composer dump-autoload
```

### "No recommendations returned"

Check if you have order data:
```php
$orderCount = \App\Models\Order::where('order_status', 'completed')->count();
echo "Total completed orders: {$orderCount}\n";

// Need at least 10-20 orders for good results
```

### "All recommendations are the same"

Your order data might be too similar. The system falls back to popular items when:
- User has no order history
- Not enough variety in orders
- All similar items

---

## What Success Looks Like

✅ **Smart Rules Working:**
```
[INFO] Using smart rule-based recommendations (user_id: 1)
```

✅ **AI Working:**
```
[INFO] AI recommendations successful (user_id: 1, count: 10)
```

✅ **Fallback Working:**
```
[WARNING] AI service unavailable, falling back to smart rules
[INFO] Using smart rule-based recommendations (user_id: 1)
```

---

## Ready to Use in Production

The system is **production-ready** right now because:

1. ✅ **Works without AI** - Smart rules always available
2. ✅ **No setup needed** - Uses existing Laravel/MySQL
3. ✅ **Fast** - All database queries are optimized
4. ✅ **Safe** - Falls back gracefully if anything fails
5. ✅ **Logged** - All actions tracked in Laravel logs

You can start using recommendations **immediately** while you set up the AI service!

---

## Next Steps

1. **Test smart rules work** (run tests above)
2. **Set up AI service** (if you want ML-powered recommendations)
3. **Add to your menu pages** (show recommendations to customers)
4. **Monitor logs** (see which tier is being used)
5. **Adjust rules** (customize for your restaurant)

Simple! 🚀
