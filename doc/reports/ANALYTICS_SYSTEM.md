# Comprehensive Analytics & Reporting System

## 📊 Overview

Sistem analytics yang telah diupdated untuk menghubungkan **SEMUA** komponen aplikasi ke dalam reporting system yang comprehensive. Sekarang, semua data dari orders, menu items, table bookings, QR sessions, promotions, dan rewards **TERHUBUNG** dan ditrack secara automatik.

---

## ✅ MASALAH YANG TELAH DISELESAIKAN

### **SEBELUM (Masalah)**
❌ Order paid tidak muncul di sales report
❌ Dua table analytics berbeza (`SaleAnalytics` & `DailySalesSummary`)
❌ ReportController baca dari table kosong
❌ Tiada tracking untuk QR orders, bookings, promotions
❌ Menu popularity tidak ditrack
❌ Customer retention tidak diukur

### **SELEPAS (Diselesaikan)** ✅
✅ **Unified analytics** - satu source of truth
✅ Order paid **AUTO-TRACKED** ke reports
✅ QR orders **DIHUBUNGKAN** dengan revenue
✅ Table bookings **DITRACK** untuk utilization
✅ Promotions effectiveness **DIUKUR**
✅ Rewards redemption **DIMONITOR**
✅ Menu popularity **AUTO-CALCULATED** daily
✅ Customer retention metrics **TERSEDIA**

---

## 🏗️ Architecture Overview

### **Data Flow**
```
Orders (paid) ──┐
Menu Items ─────┼──► analytics:generate ──► SaleAnalytics ──► ReportController ──► Dashboard
Bookings ───────┤    (Daily @ 1:00 AM)       (Single Table)      (Comprehensive)     (Visualized)
QR Sessions ────┤
Promotions ─────┤
Rewards ────────┘
```

### **Core Components**

1. **`GenerateAnalyticsReport` Command** ([app/Console/Commands/GenerateAnalyticsReport.php](app/Console/Commands/GenerateAnalyticsReport.php))
   - Runs daily at 1:00 AM
   - Collects data dari **SEMUA** paid orders
   - Calculates comprehensive metrics
   - Stores ke `sale_analytics` table

2. **`SaleAnalytics` Model** ([app/Models/SaleAnalytics.php](app/Models/SaleAnalytics.php))
   - **Enhanced** dengan new fields
   - Single source of truth
   - Comprehensive data storage

3. **`SalesAnalyticsService`** ([app/Services/SalesAnalyticsService.php](app/Services/SalesAnalyticsService.php))
   - Business logic untuk analytics
   - New methods: `getComprehensiveAnalytics()`, `getOrderTypeBreakdown()`, etc.

4. **`ReportController`** ([app/Http/Controllers/Admin/ReportController.php](app/Http/Controllers/Admin/ReportController.php))
   - Updated untuk display comprehensive data
   - Ready untuk new visualizations

---

## 📋 Analytics Data Tracked

### **Revenue & Orders** 💰
- Total sales (paid orders only)
- Total orders
- Average order value
- Revenue by order type (dine-in, takeaway, delivery, QR)

### **Menu Items** 🍽️
- Top 10 popular items (by quantity sold)
- Sales by category
- Active items count

### **Customers** 👥
- Unique customers
- New customers
- Returning customers
- Retention rate

### **QR & Table Analytics** 📱
- QR session count
- QR order count
- QR revenue
- Table booking count
- Table utilization rate

### **Promotions & Rewards** 🎁
- Promotion usage count
- Total discounts given
- Promotion effectiveness (ROI)
- Rewards redeemed count

### **Operational Metrics** ⏱️
- Peak hours analysis
- Average preparation time
- Order distribution by hour

### **Future Ready** 🔮
- COGS (Cost of Goods Sold) - ready field
- Gross profit - ready field
- Profit margin - ready field

---

## 🔧 Database Schema

### **Enhanced `sale_analytics` Table**
```sql
-- Core Sales Metrics
date                        DATE UNIQUE
total_sales                 DECIMAL(12,2)
total_orders               INT
average_order_value        DECIMAL(10,2)

-- Order Types
dine_in_orders             INT
takeaway_orders            INT
delivery_orders            INT
qr_orders                  INT
mobile_orders              INT

-- Revenue by Type
total_revenue_dine_in      DECIMAL(10,2)
total_revenue_takeaway     DECIMAL(10,2)
total_revenue_delivery     DECIMAL(10,2)

-- Customer Metrics
unique_customers           INT
new_customers             INT
returning_customers        INT

-- Menu & Operational
popular_items             JSON
peak_hours                JSON
average_preparation_time   DECIMAL(5,2)

-- NEW: QR & Table Analytics
qr_session_count          INT
qr_revenue                DECIMAL(10,2)
table_booking_count       INT
table_utilization_rate    DECIMAL(5,2)

-- NEW: Promotions & Rewards
promotion_usage_count     INT
promotion_discount_total  DECIMAL(10,2)
rewards_redeemed_count    INT

-- NEW: Cost & Profit (Future Use)
cogs_total                DECIMAL(10,2) NULL
gross_profit              DECIMAL(10,2) NULL
profit_margin             DECIMAL(5,2) NULL
```

---

## 🚀 Usage

### **Manual Analytics Generation**
```bash
# Generate untuk semalam (default)
php artisan analytics:generate

# Generate untuk specific date
php artisan analytics:generate --date=2025-10-15

# Generate untuk beberapa hari
php artisan analytics:generate --date=2025-10-10
php artisan analytics:generate --date=2025-10-11
php artisan analytics:generate --date=2025-10-12
```

### **Automatic Scheduling**
Analytics AUTO-GENERATE setiap hari pada 1:00 AM via scheduler:
```php
// app/Console/Kernel.php
$schedule->command('analytics:generate')->dailyAt('01:00');
```

### **Accessing Analytics in Code**

#### Get Sales Summary
```php
$service = app(SalesAnalyticsService::class);
$summary = $service->getSalesSummary(30); // Last 30 days
```

#### Get Comprehensive Analytics
```php
$analytics = $service->getComprehensiveAnalytics(
    Carbon::now()->startOfMonth(),
    Carbon::now()->endOfMonth()
);

// Returns:
// - total_revenue
// - total_orders
// - avg_order_value
// - unique_customers
// - new_customers
// - returning_customers
// - qr_orders, qr_revenue
// - table_bookings
// - promotions_used
// - promotion_discounts
// - rewards_redeemed
```

#### Get Order Type Breakdown
```php
$breakdown = $service->getOrderTypeBreakdown(30);

// Returns:
// - labels: ['Dine In', 'Takeaway', 'Delivery', 'QR Table']
// - data: [100, 50, 30, 20]
// - revenue: [5000, 2000, 1500, 1000]
```

#### Get QR vs Web Orders
```php
$comparison = $service->getQrVsWebOrders(30);

// Returns:
// - labels: ['Web Orders', 'QR Orders']
// - data: [150, 50]
// - percentage: ['web' => 75, 'qr' => 25]
```

#### Get Promotion Effectiveness
```php
$stats = $service->getPromotionEffectiveness(30);

// Returns:
// - total_usage
// - total_discounts
// - revenue_impact_percentage
// - avg_discount_per_use
```

#### Get Customer Retention
```php
$retention = $service->getCustomerRetention(30);

// Returns:
// - new_customers
// - returning_customers
// - retention_rate (percentage)
```

---

## 📈 Report Dashboard Access

### **Admin Dashboard**
Navigate to: `/admin/reports`

Data yang ditunjukkan:
- Revenue trends (30 days chart)
- Top 10 selling products
- Sales by category
- Order type breakdown
- QR vs Web orders comparison
- Promotion effectiveness
- Customer retention metrics
- Table & QR analytics

---

## 🔄 Data Flow Details

### **When Order is Paid**
1. Payment successful → `payment_status` = 'paid'
2. Order status → 'completed' or 'served'
3. Daily analytics command collects this order
4. Analytics stored in `sale_analytics`
5. Dashboard displays updated metrics

### **What Gets Tracked**
✅ **Orders**: Only PAID orders (`payment_status = 'paid'` AND `order_status IN ['completed', 'served']`)
✅ **Revenue**: Sum of `total_amount` from paid orders
✅ **Menu Items**: Aggregated from `order_items` table
✅ **Customers**: Tracked via `user_id` (new vs returning)
✅ **QR Sessions**: From `table_qrcodes` table
✅ **Bookings**: From `table_reservations` table
✅ **Promotions**: From `promotion_usage_logs` table
✅ **Rewards**: From `customer_rewards` table

---

## 🎯 Integration Points

### **All Systems Now Connected:**

#### 1. **Order System** → Reports
- [app/Models/Order.php](app/Models/Order.php)
- Tracked: Revenue, order count, order types
- Trigger: `payment_status = 'paid'`

#### 2. **Menu Items** → Reports
- [app/Models/MenuItem.php](app/Models/MenuItem.php)
- Tracked: Popularity (top sellers), category performance
- Source: `order_items` table

#### 3. **Table Bookings** → Reports
- [app/Models/TableReservation.php](app/Models/TableReservation.php)
- Tracked: Booking count, utilization rate
- Source: Bookings with status `confirmed/seated/completed`

#### 4. **QR System** → Reports
- [app/Models/TableQrcode.php](app/Models/TableQrcode.php)
- Tracked: QR sessions, QR orders, QR revenue
- Source: QR sessions and orders with `order_type = 'qr_table'`

#### 5. **Promotions** → Reports
- [app/Models/Promotion.php](app/Models/Promotion.php)
- Tracked: Usage count, discount amount, effectiveness
- Source: `promotion_usage_logs` table

#### 6. **Rewards** → Reports
- [app/Models/Reward.php](app/Models/Reward.php)
- Tracked: Redemption count
- Source: `customer_rewards` where `status = 'redeemed'`

#### 7. **Stock Management** → Reports (Future)
- [app/Models/StockItem.php](app/Models/StockItem.php)
- Ready: COGS, profit margin calculations
- Fields available: `cogs_total`, `gross_profit`, `profit_margin`

---

## 🧪 Testing

### **Test Analytics Generation**
```bash
# Test dengan empty data
php artisan analytics:generate --date=2025-01-01

# Test dengan real data (if orders exist)
php artisan analytics:generate --date=2025-10-15
```

### **Verify Data**
```bash
# Check sale_analytics table
php artisan tinker
>>> App\Models\SaleAnalytics::latest()->first()
>>> App\Models\SaleAnalytics::whereDate('date', '2025-10-15')->first()
```

### **Expected Output**
```
🚀 Starting comprehensive analytics generation...
📅 Generating analytics for: 2025-10-15
📊 Processing 25 paid orders...
✅ Analytics generated successfully!

📈 ANALYTICS SUMMARY
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
💰 Total Sales: RM 5,250.00
📦 Total Orders: 25
📊 Average Order Value: RM 210.00
👥 Unique Customers: 18
🆕 New Customers: 5
🔁 Returning Customers: 13
📱 QR Orders: 8
📅 Table Bookings: 12
🎁 Promotions Used: 6
⭐ Rewards Redeemed: 3
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## 🛠️ Troubleshooting

### **Problem: No data in reports**
**Solution:**
1. Check jika ada paid orders:
   ```sql
   SELECT COUNT(*) FROM orders WHERE payment_status = 'paid';
   ```
2. Run manual analytics generation:
   ```bash
   php artisan analytics:generate --date=2025-10-15
   ```
3. Verify data stored:
   ```sql
   SELECT * FROM sale_analytics ORDER BY date DESC LIMIT 1;
   ```

### **Problem: Command not running**
**Solution:**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Re-register commands
php artisan optimize:clear

# Test manually
php artisan analytics:generate
```

### **Problem: Scheduler not working**
**Solution:**
1. Ensure cron job setup:
   ```cron
   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```
2. Test scheduler:
   ```bash
   php artisan schedule:list
   php artisan schedule:run
   ```

---

## 📝 Migration Notes

### **Breaking Changes**
- `DailySalesSummary` is now DEPRECATED (but kept for backward compatibility)
- `ReportController` now uses `SaleAnalytics` instead of `DailySalesSummary`
- `SalesAnalyticsService` methods updated to use `SaleAnalytics`

### **Backward Compatibility**
- Old command `app:generate-sales-summary` masih available (commented in Kernel)
- `DailySalesSummary` still gets populated (for legacy compatibility)
- No changes required to existing views (yet)

---

## 🚀 Next Steps (Future Enhancements)

### **Phase 2: Real-time Analytics**
- [ ] Event listeners untuk real-time updates
- [ ] Live dashboard with WebSockets
- [ ] Instant notifications on milestones

### **Phase 3: Advanced Visualizations**
- [ ] Update reports view dengan Chart.js
- [ ] Pie charts untuk order type distribution
- [ ] Line graphs untuk trends
- [ ] Bar charts untuk top products

### **Phase 4: Stock Integration**
- [ ] Link stock deductions dengan COGS
- [ ] Calculate profit margins
- [ ] Track wastage impact
- [ ] Auto-reorder cost analysis

### **Phase 5: Predictive Analytics**
- [ ] Sales forecasting (ML model)
- [ ] Stock demand prediction
- [ ] Peak hour optimization
- [ ] Customer lifetime value

### **Phase 6: Export & Reporting**
- [ ] PDF report generation
- [ ] Excel export untuk accounting
- [ ] Email scheduled reports
- [ ] Print-friendly layouts

---

## 📞 Support

Jika ada masalah atau soalan:
1. Check ANALYTICS_SYSTEM.md (this file)
2. Check logs: `storage/logs/laravel.log`
3. Run diagnostics: `php artisan analytics:generate --date=yesterday`
4. Check database: Verify `sale_analytics` table

---

## 🎉 Summary

### **What We've Achieved:**
✅ **UNIFIED** analytics system
✅ **AUTOMATED** daily data collection
✅ **COMPREHENSIVE** metrics tracking
✅ **CONNECTED** all major systems:
   - Orders → Revenue tracking
   - Menu Items → Popularity analysis
   - Bookings → Utilization metrics
   - QR System → Usage statistics
   - Promotions → Effectiveness measurement
   - Rewards → Redemption tracking

### **Key Benefits:**
1. **Single Source of Truth** - No more data confusion
2. **Automatic Updates** - Daily analytics generation
3. **Complete Visibility** - See ALL business metrics
4. **Data-Driven Decisions** - Comprehensive insights
5. **Future Ready** - Extensible for advanced features

### **Commands to Remember:**
```bash
# Generate analytics
php artisan analytics:generate

# Check scheduler
php artisan schedule:list

# Test reports
Visit: /admin/reports
```

---

**Last Updated:** October 17, 2025
**Version:** 2.0
**Status:** ✅ Production Ready
