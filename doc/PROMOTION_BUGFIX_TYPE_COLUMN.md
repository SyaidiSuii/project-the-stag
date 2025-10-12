# Bug Fix: Column 'type' Not Found Error

**Date:** October 12, 2025
**Status:** ✅ **FIXED**

---

## Error Description

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'type' in 'where clause'
Location: D:\ProgramsFiles\laragon\www\the_stag\app\Http\Controllers\Admin\PromotionController.php:49
```

---

## Root Cause Analysis

### Database Column Name
The promotions table uses `promotion_type` as the column name (defined in migration):

```php
// database/migrations/2025_10_12_003554_add_promotion_type_fields_to_promotions_table.php
$table->enum('promotion_type', [
    'combo_deal',
    'item_discount',
    'buy_x_free_y',
    'promo_code',
    'seasonal',
    'bundle'
])->default('promo_code')->after('name');
```

### Controller Query Issue
The AdminPromotionController was querying using `type` instead of `promotion_type`:

```php
// BEFORE (INCORRECT)
if ($request->has('type') && $request->type) {
    $query->where('type', $request->type);  // ❌ Column 'type' doesn't exist
}
```

### View Access Pattern
Views were accessing the promotion type as `$promo->type` which didn't match the database column `promotion_type`.

---

## Solution Implemented

### Fix 1: Update Controller Query
**File:** `app/Http/Controllers/Admin/PromotionController.php` (Line 35)

```php
// AFTER (CORRECT)
if ($request->has('type') && $request->type) {
    $query->where('promotion_type', $request->type);  // ✅ Correct column name
}
```

### Fix 2: Add Model Accessor
**File:** `app/Models/Promotion.php` (Lines 409-417)

Added a `type` accessor to allow views to use clean `$promo->type` syntax:

```php
/**
 * Get type accessor (alias for promotion_type)
 * This allows using $promotion->type in views for cleaner code
 */
public function getTypeAttribute(): ?string
{
    return $this->promotion_type;
}
```

This accessor provides:
- **Convenience**: Views can use `$promo->type` instead of `$promo->promotion_type`
- **Consistency**: All code now references "type" uniformly
- **Backward Compatibility**: Old code using `promotion_type` still works

---

## Files Modified

1. **app/Http/Controllers/Admin/PromotionController.php**
   - Line 35: Changed `where('type', ...)` to `where('promotion_type', ...)`

2. **app/Models/Promotion.php**
   - Lines 409-417: Added `getTypeAttribute()` accessor method

---

## Testing Verification

### Before Fix
- ❌ Visiting `/admin/promotions` caused SQL error
- ❌ Filtering by type threw "Column 'type' not found" error
- ❌ Stats cards couldn't count promotions by type

### After Fix
- ✅ Admin promotions index loads successfully
- ✅ Type filter tabs work correctly
- ✅ Stats cards show correct counts
- ✅ Views can use `$promo->type` cleanly

---

## Impact Analysis

### Affected Features
- ✅ Admin promotions index with type filtering
- ✅ Stats cards showing type-specific counts
- ✅ All views displaying promotion type badges

### No Impact On
- Customer promotion views (already using correct accessor)
- Promotion creation/editing
- Database queries in other controllers

---

## Prevention Measures

### Convention Established
Going forward, for promotion type access:
- **Database column**: Always `promotion_type`
- **Model accessor**: `$promotion->type` (via `getTypeAttribute()`)
- **Controller queries**: Always query `promotion_type` column
- **View display**: Can use `$promotion->type` for cleaner syntax

### Model Conventions
The Promotion model now follows this pattern:
```php
// In database
protected $fillable = ['promotion_type', ...];

// In views (via accessor)
{{ $promotion->type }}  // Returns $promotion->promotion_type

// In queries
Promotion::where('promotion_type', 'combo_deal')->get();
```

---

## Related Code References

### Model Scope Using Correct Column
The model already had a correct scope method:

```php
// app/Models/Promotion.php (Line 148-151)
public function scopeOfType($query, string $type)
{
    return $query->where('promotion_type', $type);
}
```

**Usage Example:**
```php
// These are now equivalent:
Promotion::ofType('combo_deal')->get();
Promotion::where('promotion_type', 'combo_deal')->get();
```

---

## Lessons Learned

1. **Column naming consistency**: When adding new fields via migration, ensure controller and model methods use the correct column name
2. **Accessor benefits**: Model accessors provide clean API for views while maintaining database integrity
3. **Migration review**: Always verify migration column names match controller queries before deployment

---

**Status:** ✅ **RESOLVED**
**Verified By:** Testing admin promotions index and type filtering
**No Further Action Required**
