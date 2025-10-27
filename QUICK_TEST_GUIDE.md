# 🧪 Quick Test Guide - Order Distribution

## What I Just Fixed:

1. ✅ **Fixed order creation flow** - Distribution now happens AFTER order items are created
2. ✅ **Added "Confirmed" status** to order form dropdown
3. ✅ **Auto-distribution** now triggers for `pending`, `confirmed`, or `preparing` status
4. ✅ **Better error handling** - Won't break if distribution fails

---

## 🚀 TEST NOW - Step by Step

### **Step 1: Create a Test Order**

1. **Open browser:** `http://the_stag.test/admin/order/create`

2. **Fill in the form:**
   ```
   Customer: Select any customer
   Total Amount: 50.00
   Order Type: Dine In
   Order Source: Counter
   Table: Select any table
   Order Status: Pending (New Order)  ← Default works!
   Payment Status: Unpaid
   ```

3. **Add Order Items:** (Scroll down to "Order Items" section)
   - Click "Add Item"
   - Select a Food item (e.g., Nasi Goreng)
   - Quantity: 1
   - Click "Add Item" again
   - Select a Drink item (e.g., Iced Tea)
   - Quantity: 1

4. **Submit** → Click "Create Order"

---

### **Step 2: Verify Distribution Worked**

#### **Check 1: View Laravel Logs**
```bash
# Open terminal and run:
cd /c/madd/laragon/www/the_stag
tail -20 storage/logs/laravel.log
```

**Look for:**
```
Order distributed to kitchen stations
order_id: [NUMBER]
items_count: 2
```

#### **Check 2: Database Verification**
```bash
# Run this command:
mysql -u root the_stag -e "
SELECT
    o.id as order_id,
    o.order_status,
    COUNT(DISTINCT sa.id) as station_assignments,
    COUNT(DISTINCT kl.id) as kitchen_loads,
    GROUP_CONCAT(DISTINCT ks.name) as stations
FROM orders o
LEFT JOIN station_assignments sa ON o.id = sa.order_id
LEFT JOIN kitchen_loads kl ON o.id = kl.order_id
LEFT JOIN kitchen_stations ks ON sa.station_id = ks.id
WHERE o.id = (SELECT MAX(id) FROM orders)
GROUP BY o.id;
"
```

**Expected Output:**
```
order_id | order_status | station_assignments | kitchen_loads | stations
---------|--------------|-------------------|---------------|----------------------------------
123      | pending      | 2                 | 2             | Hot Cooking Station,Beverages & Drinks
```

#### **Check 3: See It on KDS**

**View Hot Kitchen Orders:**
1. Login as: `chef.ali@thestag.com` / `password`
2. URL: `http://the_stag.test/kds`
3. You should see the food item!

**View Drinks Station Orders:**
1. Logout
2. Login as: `barista.john@thestag.com` / `password`
3. URL: `http://the_stag.test/kds`
4. You should see the drink item!

---

## ✅ Success Criteria

Your system is working if:

- [x] Order created successfully
- [x] Log shows "Order distributed to kitchen stations"
- [x] Database shows 2 station_assignments
- [x] Database shows 2 kitchen_loads
- [x] Hot Cooking Station `current_load` increased by 1
- [x] Beverages & Drinks `current_load` increased by 1
- [x] Chef Ali sees food item on his KDS
- [x] Barista John sees drink item on his KDS

---

## 🎯 What "Distribution" Means

**Distribution = Automatic Assignment of Order Items to Kitchen Stations**

### Before Distribution:
```
Order #123
├─ Nasi Goreng (Food)
└─ Iced Tea (Drink)
```

### After Distribution:
```
Order #123
├─ Nasi Goreng → HOT COOKING STATION → Chef Ali
└─ Iced Tea → BEVERAGES & DRINKS → Barista John
```

**How it works:**
1. System checks each menu item's `station_type`
2. Finds all active stations of that type
3. Calculates which station has lowest load
4. Assigns item to that station
5. Creates `station_assignment` record
6. Creates `kitchen_load` record
7. Increments station's `current_load`
8. Chef sees it on their KDS immediately!

---

## 🐛 Troubleshooting

### Issue: No distribution happening

**Check 1: Menu items have station_type?**
```sql
SELECT id, name, station_type, kitchen_load_factor
FROM menu_items
WHERE deleted_at IS NULL;
```

If `station_type` is NULL → Menu item not assigned to a station!

**Fix:**
```sql
UPDATE menu_items
SET station_type = 'hot_kitchen', kitchen_load_factor = 1.5
WHERE name = 'Nasi Goreng';
```

### Issue: Error in logs

**Check Laravel log:**
```bash
tail -50 storage/logs/laravel.log
```

Look for error message and share with me!

### Issue: Items showing on wrong chef's KDS

**Check user's assigned_station_id:**
```sql
SELECT name, email, assigned_station_id
FROM users
WHERE email = 'chef.ali@thestag.com';
```

**Check station_assignments:**
```sql
SELECT
    o.id,
    mi.name as item,
    ks.name as station,
    sa.status
FROM station_assignments sa
JOIN orders o ON sa.order_id = o.id
JOIN order_items oi ON sa.order_item_id = oi.id
JOIN menu_items mi ON oi.menu_item_id = mi.id
JOIN kitchen_stations ks ON sa.station_id = ks.id
ORDER BY sa.id DESC
LIMIT 10;
```

---

## 📊 Monitor Manager Dashboard

After creating orders, check:

**URL:** `http://the_stag.test/admin/kitchen`

You should see:
- Station load percentages updated
- Active orders per station
- Real-time monitoring

---

## 🎉 What Happens Next?

Once distribution is working:

1. **Complete Order Flow:**
   - Chef marks "Preparing" → Load stays
   - Chef marks "Ready" → Load stays
   - Chef marks "Completed" → Load releases (decreases)

2. **Load Balancing:**
   - If Hot Kitchen has 8 orders (80% load)
   - And you have 2 Hot Kitchen stations
   - Next order goes to the less-loaded one automatically!

3. **Manager Visibility:**
   - Dashboard shows which stations are busy
   - Alerts when station reaches 80%+ capacity
   - Can manually redistribute if needed

---

**Ready to test? Follow Step 1 and let me know what happens!** 🚀
