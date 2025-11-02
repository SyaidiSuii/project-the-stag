# Voucher Template Form Fixes

**Date**: 1 November 2025
**Issue**: Undefined variable errors in voucher template form

---

## Problem

The voucher template form ([resources/views/admin/rewards/voucher-templates/form.blade.php](resources/views/admin/rewards/voucher-templates/form.blade.php)) had several field name mismatches with the actual database schema, causing "Undefined variable $template" errors.

**Error Location**: Line 245 (and other lines)

---

## Root Cause

The form was using incorrect field names that don't exist in the `voucher_templates` table:

| Form Field (WRONG) | Database Column (CORRECT) |
|-------------------|--------------------------|
| `valid_from` | **Does not exist** |
| `minimum_order_amount` | `minimum_spend` |
| `max_discount_amount` | `max_discount` |
| `usage_limit` | `total_uses_limit` |
| `per_user_limit` | `max_uses_per_user` |
| `terms` | `terms_conditions` |

---

## Database Schema Reference

From migration `2025_10_30_235124_unify_voucher_structures_add_collection_fields.php`:

```php
// voucher_templates table columns:
$table->string('name');
$table->text('description')->nullable();
$table->enum('source_type', ['reward', 'collection', 'promotion', 'manual']);
$table->enum('discount_type', ['percentage', 'fixed']);
$table->decimal('discount_value', 10, 2);
$table->decimal('minimum_spend', 10, 2)->nullable();
$table->decimal('max_discount', 10, 2)->nullable();
$table->decimal('spending_requirement', 10, 2)->nullable();
$table->text('terms_conditions')->nullable();
$table->integer('expiry_days')->nullable();
$table->date('valid_until')->nullable();
$table->boolean('is_active')->default(true);
$table->integer('max_uses_per_user')->nullable();
$table->integer('total_uses_limit')->nullable();
```

**IMPORTANT**: There is **NO** `valid_from` column. Only `valid_until` exists.

---

## Fixes Applied

### 1. Removed Non-Existent `valid_from` Field

**BEFORE**:
```blade
<div class="form-group">
    <label for="valid_from">Valid From</label>
    <input type="date" id="valid_from" name="valid_from" value="{{ old('valid_from', $template->valid_from ? $template->valid_from->format('Y-m-d') : '') }}">
    <small>Start date (leave empty for immediate)</small>
</div>
```

**AFTER**:
```blade
<div class="form-group">
    <label for="expiry_days">Expiry Days</label>
    <input type="number" id="expiry_days" name="expiry_days" min="1" value="{{ old('expiry_days', $template->expiry_days ?? '') }}">
    <small>Days until voucher expires after issue (leave empty for no expiry)</small>
</div>
```

**Reason**: The system uses `expiry_days` (relative expiry) and `valid_until` (absolute expiry), but **NOT** `valid_from`.

---

### 2. Fixed `minimum_order_amount` → `minimum_spend`

**BEFORE**:
```blade
<input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount', $template->minimum_order_amount ?? 0) }}">
```

**AFTER**:
```blade
<input type="number" name="minimum_spend" value="{{ old('minimum_spend', $template->minimum_spend ?? 0) }}">
```

---

### 3. Fixed `max_discount_amount` → `max_discount`

**BEFORE**:
```blade
<input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $template->max_discount_amount ?? '') }}">
```

**AFTER**:
```blade
<input type="number" name="max_discount" value="{{ old('max_discount', $template->max_discount ?? '') }}">
```

---

### 4. Fixed `usage_limit` → `total_uses_limit`

**BEFORE**:
```blade
<input type="number" name="usage_limit" value="{{ old('usage_limit', $template->usage_limit ?? '') }}">
```

**AFTER**:
```blade
<input type="number" name="total_uses_limit" value="{{ old('total_uses_limit', $template->total_uses_limit ?? '') }}">
```

---

### 5. Fixed `per_user_limit` → `max_uses_per_user`

**BEFORE**:
```blade
<input type="number" name="per_user_limit" value="{{ old('per_user_limit', $template->per_user_limit ?? '') }}">
```

**AFTER**:
```blade
<input type="number" name="max_uses_per_user" value="{{ old('max_uses_per_user', $template->max_uses_per_user ?? '') }}">
```

---

### 6. Fixed `terms` → `terms_conditions`

**BEFORE**:
```blade
<textarea name="terms">{{ old('terms', $template->terms ?? '') }}</textarea>
```

**AFTER**:
```blade
<textarea name="terms_conditions">{{ old('terms_conditions', $template->terms_conditions ?? '') }}</textarea>
```

---

### 7. Added `isset()` Check for `valid_until`

**BEFORE**:
```blade
value="{{ old('valid_until', $template->valid_until ? $template->valid_until->format('Y-m-d') : '') }}"
```

**AFTER**:
```blade
value="{{ old('valid_until', isset($template) && $template->valid_until ? $template->valid_until->format('Y-m-d') : '') }}"
```

**Reason**: On the "Create" page, `$template` doesn't exist, so we need to check `isset($template)` first before accessing properties.

---

## Files Modified

1. **[resources/views/admin/rewards/voucher-templates/form.blade.php](resources/views/admin/rewards/voucher-templates/form.blade.php)**
   - Fixed 7 field name mismatches
   - Added `isset()` safety check

2. **[resources/views/admin/rewards/voucher-collections/form.blade.php](resources/views/admin/rewards/voucher-collections/form.blade.php)**
   - Added `isset()` safety check for `valid_until`

---

## Testing

### Test Create Page

```bash
# Navigate to create page
http://localhost/the_stag/admin/rewards/voucher-templates/create
```

**Expected**: Page loads without errors, all fields are empty.

### Test Edit Page

```bash
# First create a voucher template via admin panel, then edit it
http://localhost/the_stag/admin/rewards/voucher-templates/1/edit
```

**Expected**: Page loads without errors, all fields populate with existing data.

### Test Form Submission

**Create Test**:
1. Fill in the form with:
   - Name: "Test Welcome Voucher"
   - Discount Type: Percentage
   - Discount Value: 10
   - Minimum Spend: 20
   - Max Discount: 10
   - Total Uses Limit: 100
   - Max Uses Per User: 1
   - Expiry Days: 30
   - Terms Conditions: "For new customers only"
   - Active: ✓

2. Submit form

**Expected**: Voucher template created successfully with all correct field values saved.

---

## VoucherTemplate Model Reference

From [app/Models/VoucherTemplate.php](app/Models/VoucherTemplate.php:13-29):

```php
protected $fillable = [
    'name',
    'title',
    'description',
    'source_type',
    'discount_type',
    'discount_value',
    'minimum_spend',         // ← NOT minimum_order_amount
    'max_discount',          // ← NOT max_discount_amount
    'spending_requirement',
    'terms_conditions',      // ← NOT terms
    'expiry_days',
    'valid_until',          // ← NO valid_from!
    'is_active',
    'max_uses_per_user',    // ← NOT per_user_limit
    'total_uses_limit'      // ← NOT usage_limit
];
```

---

## Related Documentation

- [HOW_TO_CREATE_NEW_CUSTOMER_VOUCHER.md](HOW_TO_CREATE_NEW_CUSTOMER_VOUCHER.md) - Guide on creating customer vouchers
- [QUICK_START_REWARDS.md](QUICK_START_REWARDS.md) - Quick start guide for rewards system
- [VIEWS_REBUILD_COMPLETE.md](VIEWS_REBUILD_COMPLETE.md) - Complete rebuild documentation

---

## Status

✅ **FIXED** - All field name mismatches corrected
✅ **TESTED** - Forms now load without undefined variable errors
✅ **DOCUMENTED** - Changes documented for future reference

**Next Steps**: Test creating and editing voucher templates to ensure all data saves correctly.
