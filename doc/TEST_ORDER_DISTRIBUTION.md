# 🧪 Testing Order Distribution - Quick Guide

## Test 1: Manual Order Creation (Admin Panel)

### Steps:
1. Login as Admin: `https://the_stag.test/login`
2. Go to: `https://the_stag.test/admin/order/create`
3. Create order with:
   - Customer: Select any user
   - Order Type: Dine In
   - Status: **Confirmed** (this triggers distribution)
   - Add Items:
     * 1x Food item (e.g., Nasi Goreng)
     * 1x Drink item (e.g., Iced Tea)

4. Submit the order

### Verify Distribution:

**Check Database:**
```sql
-- Check station assignments created
SELECT
    sa.id,
    o.id as order_id,
    ks.name as station,
    mi.name as item,
    sa.status
FROM station_assignments sa
JOIN orders o ON sa.order_id = o.id
JOIN kitchen_stations ks ON sa.station_id = ks.id
JOIN order_items oi ON sa.order_item_id = oi.id
JOIN menu_items mi ON oi.menu_item_id = mi.id
WHERE o.id = [YOUR_ORDER_ID]
ORDER BY sa.id DESC;

-- Check kitchen loads updated
SELECT
    kl.id,
    o.id as order_id,
    ks.name as station,
    kl.load_points,
    kl.status
FROM kitchen_loads kl
JOIN orders o ON kl.order_id = o.id
JOIN kitchen_stations ks ON kl.station_id = ks.id
WHERE o.id = [YOUR_ORDER_ID];

-- Check station current_load increased
SELECT name, current_load, max_capacity
FROM kitchen_stations
WHERE is_active = 1;
```

**Check KDS:**
1. Login as Chef Ali: `chef.ali@thestag.com`
2. Should see the food item
3. Logout

4. Login as Barista John: `barista.john@thestag.com`
5. Should see the drink item

### Expected Results:
✅ `station_assignments` table has 2 rows (1 for food, 1 for drink)
✅ `kitchen_loads` table has 2 rows
✅ Hot Kitchen `current_load` = 1
✅ Drinks Station `current_load` = 1
✅ Chef Ali sees food item on his KDS
✅ Barista John sees drink item on his KDS

---

## Test 2: Customer Order via Website

### Steps:
1. Logout from admin
2. Browse to: `https://the_stag.test/customer/menu`
3. Add items to cart (mix of food + drinks)
4. Checkout and place order
5. Check if order auto-distributes

### Verify:
Same database queries as Test 1

---

## Test 3: QR Code Order

### Steps:
1. Generate QR code for a table
2. Scan QR code
3. Place order via QR menu
4. Verify distribution

---

## Troubleshooting

### Issue: No station_assignments created
**Possible causes:**
- Order status not set to "confirmed"
- Menu items don't have `station_type` set
- OrderDistributionService has error

**Check:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Or check if service exists
php artisan tinker
>>> app(\App\Services\Kitchen\OrderDistributionService::class)
```

### Issue: Items not showing on chef KDS
**Check:**
- User has `assigned_station_id` set
- User has `kitchen_staff` role
- Station is active
- Order status is not 'completed' or 'cancelled'

---

## Quick Database Checks

```bash
# Check recent orders
mysql -u root the_stag -e "
SELECT
    o.id,
    o.order_status,
    COUNT(sa.id) as assignments,
    COUNT(kl.id) as loads
FROM orders o
LEFT JOIN station_assignments sa ON o.id = sa.order_id
LEFT JOIN kitchen_loads kl ON o.id = kl.order_id
WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY o.id
ORDER BY o.id DESC
LIMIT 5;
"

# Check station loads
mysql -u root the_stag -e "
SELECT
    ks.name,
    ks.current_load,
    ks.max_capacity,
    ROUND((ks.current_load / ks.max_capacity) * 100, 0) as load_pct
FROM kitchen_stations ks
WHERE ks.is_active = 1;
"
```

---

## Success Criteria

The system is working correctly if:

1. ✅ When order status changes to "confirmed"
   - `distributeOrder()` is called automatically
   - Station assignments are created for each item
   - Kitchen loads are created
   - Station `current_load` increments

2. ✅ Each chef sees only their items
   - Chef at Hot Kitchen sees hot food
   - Chef at Drinks sees beverages
   - No cross-visibility

3. ✅ When order is completed
   - Station `current_load` decrements
   - Kitchen loads marked as completed
   - Space freed for new orders

4. ✅ Manager dashboard shows
   - Accurate load percentages
   - Active orders per station
   - Real-time updates

---

## Next Steps After Testing

If tests pass:
✅ System is working!
✅ Move to analytics & reporting
✅ Add real-time notifications
✅ Fine-tune load factors

If tests fail:
❌ Check logs for errors
❌ Verify service implementations
❌ Debug distribution logic
❌ Check database constraints
