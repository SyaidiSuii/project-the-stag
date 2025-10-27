# Hybrid ID System Implementation - The Stag SmartDine

## Overview
Successfully implemented a **hybrid ID system** that uses:
- **Integer PKs/FKs** for internal relationships (performance)
- **Formatted display IDs** for user-facing purposes (user-friendly)

This follows Laravel best practices and ensures optimal database performance while providing professional, readable IDs to users.

---

## Implementation Summary

### ✅ Completed Tasks

#### 1. Database Migrations (3 new migrations)

**Migration 1: `2025_10_23_211224_add_user_id_to_users_table.php`**
- Added `user_id` VARCHAR(20) UNIQUE NULLABLE
- Added index for fast lookups
- Location: [database/migrations/2025_10_23_211224_add_user_id_to_users_table.php](database/migrations/2025_10_23_211224_add_user_id_to_users_table.php)

**Migration 2: `2025_10_23_212859_add_staff_id_to_staff_profiles_table.php`**
- Added `staff_id` VARCHAR(30) UNIQUE NULLABLE
- Added `ic_number` VARCHAR(14) NULLABLE
- Added index for fast lookups
- Location: [database/migrations/2025_10_23_212859_add_staff_id_to_staff_profiles_table.php](database/migrations/2025_10_23_212859_add_staff_id_to_staff_profiles_table.php)

**Migration 3: `2025_10_23_213508_add_customer_id_to_customer_profiles_table.php`**
- Added `customer_id` VARCHAR(20) UNIQUE NULLABLE
- Added index for fast lookups
- Location: [database/migrations/2025_10_23_213508_add_customer_id_to_customer_profiles_table.php](database/migrations/2025_10_23_213508_add_customer_id_to_customer_profiles_table.php)

#### 2. ID Generator Service

**File:** [app/Services/IdGeneratorService.php](app/Services/IdGeneratorService.php)

**Features:**
- `generateUserId()` - Format: `USR-25-0001`
- `generateStaffId($position, $icNumber)` - Format: `STG-WTR-25-45-01`
- `generateCustomerId()` - Format: `CST-25-4821`
- Position code mapping (Waiter → WTR, Chef → CHF, Cashier → CSH, etc.)
- Collision detection with retry logic
- Year-aware sequential numbering
- Validation methods for each ID format

**Position Codes Supported:**
```php
'waiter' => 'WTR'
'chef' => 'CHF'
'cashier' => 'CSH'
'manager' => 'MGR'
'supervisor' => 'SPV'
'kitchen staff' => 'KTC'
'bartender' => 'BAR'
'hostess/host' => 'HST'
'delivery' => 'DLV'
'cleaner' => 'CLN'
```

#### 3. Model Updates

**User Model** ([app/Models/User.php](app/Models/User.php)):
- Added `user_id` to fillable array
- Added `boot()` method with auto-generation on creation
- Added `getDisplayIdAttribute()` accessor
- Added `$appends = ['display_id']` for API responses
- Imported `IdGeneratorService`

**StaffProfile Model** ([app/Models/StaffProfile.php](app/Models/StaffProfile.php)):
- Added `staff_id` and `ic_number` to fillable array
- Fixed `role_id` (was incorrectly `roles_id`)
- Added `boot()` method with auto-generation on creation
- Added `getDisplayIdAttribute()` accessor
- Added `getPositionCode()` helper method
- Added `$appends = ['display_id']` for API responses
- Imported `IdGeneratorService`

**CustomerProfile Model** ([app/Models/CustomerProfile.php](app/Models/CustomerProfile.php)):
- Added `customer_id` to fillable array
- Added `boot()` method with auto-generation on creation
- Added `getDisplayIdAttribute()` accessor
- Added `$appends = ['display_id']` for API responses
- Imported `IdGeneratorService`

---

## ID Format Specifications

### User ID
**Format:** `USR-[YEAR]-[SEQUENCE]`
- Example: `USR-25-0001`, `USR-25-0002`
- Year: 2-digit current year
- Sequence: 4-digit padded, resets annually
- Unique: Globally unique in `users` table

### Staff ID
**Format:** `STG-[POSITION_CODE]-[YEAR]-[IC_LAST_2]-[SEQUENCE]`
- Example: `STG-WTR-25-45-01` (Waiter, 2025, IC ending 45, sequence 01)
- Position Code: 3-letter code based on position
- Year: 2-digit current year
- IC Last 2: Last 2 digits of IC number
- Sequence: 2-digit padded, increments per position/IC/year combination
- Unique: Globally unique in `staff_profiles` table

### Customer ID
**Format:** `CST-[YEAR]-[RANDOM]`
- Example: `CST-25-4821`, `CST-25-6923`
- Year: 2-digit current year
- Random: 4-digit random number (0000-9999)
- Unique: Globally unique in `customer_profiles` table

---

## Testing Results

### Test 1: User ID Generation
```
User ID: USR-25-0001
User ID: USR-25-0005
User ID: USR-25-0006
```
✅ **Result:** Sequential numbering working correctly

### Test 2: Staff ID Generation
```
Position: Chef, IC: 980707065477
Staff ID: STG-CHF-25-77-01

Position: Waiter, IC: 010203040045
Staff ID: STG-WTR-25-45-01

Position: Waiter, IC: 020304050045 (same IC ending)
Staff ID: STG-WTR-25-45-02 (correctly incremented)

Position: Cashier, IC: 030501087362
Staff ID: STG-CSH-25-62-01
```
✅ **Result:** Position codes, IC extraction, and auto-increment all working perfectly

### Test 3: Customer ID Generation
```
Customer ID: CST-25-6923
Customer ID: CST-25-1842
```
✅ **Result:** Random generation with uniqueness check working

### Test 4: API Response with Display IDs
```json
{
    "id": 12,
    "user_id": "USR-25-0005",
    "display_id": "USR-25-0005",
    "staff_profile": {
        "id": 1,
        "staff_id": "STG-CHF-25-77-01",
        "display_id": "STG-CHF-25-77-01"
    }
}
```
✅ **Result:** Both `user_id`/`staff_id`/`customer_id` and `display_id` accessor working in API responses

---

## Database Structure

### Before (Performance-Optimized)
```
users (id: integer PK) ←─┐
                          │
staff_profiles            │ (Fast integer FK join)
(id: integer PK, user_id: integer FK) ──┘
```

### After (Hybrid: Performance + User-Friendly)
```
users
├── id: integer PK (for relationships)
└── user_id: string UNIQUE (for display)

staff_profiles
├── id: integer PK (for relationships)
├── user_id: integer FK → users.id (fast join)
├── staff_id: string UNIQUE (for display)
└── ic_number: string (for ID generation)

customer_profiles
├── id: integer PK (for relationships)
├── user_id: integer FK → users.id (fast join)
└── customer_id: string UNIQUE (for display)
```

**Benefits:**
- Integer PKs/FKs = Fast queries & joins
- String display IDs = Professional, user-friendly
- Indexed for fast lookups on both ID types
- Laravel Eloquent works natively with integer PKs

---

## Usage Examples

### Creating Users with Auto-Generated IDs

```php
// Create user - ID auto-generated
$user = User::create([
    'name' => 'Ahmad',
    'email' => 'ahmad@thestag.my',
    'password' => bcrypt('password'),
]);
// $user->id = 1 (internal)
// $user->user_id = 'USR-25-0001' (display)
// $user->display_id = 'USR-25-0001' (accessor)
```

### Creating Staff with Auto-Generated IDs

```php
// Create staff - ID auto-generated based on position & IC
$staff = StaffProfile::create([
    'user_id' => $user->id,
    'role_id' => 4,
    'position' => 'waiter',
    'ic_number' => '010203040045',
    'hire_date' => now(),
    'salary' => 2500,
]);
// $staff->id = 1 (internal)
// $staff->staff_id = 'STG-WTR-25-45-01' (display)
// $staff->display_id = 'STG-WTR-25-45-01' (accessor)
```

### Creating Customers with Auto-Generated IDs

```php
// Create customer - ID auto-generated
$customer = CustomerProfile::create([
    'user_id' => $user->id,
    'name' => 'Siti',
    'address' => 'Kuala Lumpur',
]);
// $customer->id = 1 (internal)
// $customer->customer_id = 'CST-25-4821' (display)
// $customer->display_id = 'CST-25-4821' (accessor)
```

### Searching by Display ID

```php
// Find user by formatted ID
$user = User::where('user_id', 'USR-25-0001')->first();

// Find staff by formatted ID
$staff = StaffProfile::where('staff_id', 'STG-WTR-25-45-01')->first();

// Find customer by formatted ID
$customer = CustomerProfile::where('customer_id', 'CST-25-4821')->first();
```

### API Responses

```php
// API endpoint
public function show($id)
{
    $user = User::with('staffProfile')->findOrFail($id);
    return response()->json($user);
    // Automatically includes 'display_id' in response
}
```

---

## Next Steps (Recommendations)

### 1. Update Views for Display IDs (Optional)

If you want to show formatted IDs in Blade views:

```blade
<!-- Admin User List -->
<td>{{ $user->user_id }}</td> <!-- USR-25-0001 -->
<td>{{ $user->display_id }}</td> <!-- Same value, via accessor -->

<!-- Staff Profile -->
<p>Staff ID: {{ $staff->staff_id }}</p> <!-- STG-WTR-25-45-01 -->

<!-- Customer Dashboard -->
<p>Customer ID: {{ $customer->customer_id }}</p> <!-- CST-25-4821 -->
```

**Files to Update:**
- `resources/views/admin/users/*.blade.php`
- `resources/views/admin/staff/*.blade.php`
- `resources/views/customer/**/*.blade.php`
- `resources/views/admin/reports/*.blade.php`

### 2. Update Controllers (If Needed)

Review controllers for any hardcoded ID references:
- Search functionality should accept formatted IDs
- Validation rules should allow formatted ID patterns
- Export/PDF generation should use formatted IDs

### 3. Update Seeders (Recommended)

If you have seeders, remove manual ID assignments:

```php
// OLD (manual assignment)
User::create(['user_id' => 'USR-001', ...]);

// NEW (auto-generation)
User::create(['name' => 'Test', 'email' => 'test@example.com', ...]);
// user_id generated automatically
```

### 4. Add Validation Rules (Optional)

Create validation rules for formatted IDs:

```php
// app/Rules/ValidUserId.php
public function passes($attribute, $value)
{
    $idGenerator = app(IdGeneratorService::class);
    return $idGenerator->validateUserId($value);
}
```

### 5. Testing (Recommended)

Create feature tests:

```php
public function test_user_id_auto_generation()
{
    $user = User::factory()->create();
    $this->assertMatchesRegularExpression('/^USR-\d{2}-\d{4}$/', $user->user_id);
}
```

---

## Benefits of This Implementation

✅ **Performance:** Integer PKs/FKs ensure fast database operations
✅ **User-Friendly:** Professional formatted IDs for display
✅ **Best Practice:** Industry-standard hybrid approach
✅ **Laravel-Native:** Works seamlessly with Eloquent ORM
✅ **Scalable:** No performance degradation with growth
✅ **Maintainable:** Easy to change display format without touching relationships
✅ **API-Ready:** Display IDs automatically included in JSON responses
✅ **Year-Aware:** IDs include year for easy annual tracking
✅ **Collision-Safe:** Retry logic prevents duplicate IDs

---

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback (if needed)
php artisan migrate:rollback --step=3

# Fresh migration with seed (development only)
php artisan migrate:fresh --seed
```

---

## Files Modified/Created

### Created Files:
1. `app/Services/IdGeneratorService.php` - ID generation service
2. `database/migrations/2025_10_23_211224_add_user_id_to_users_table.php`
3. `database/migrations/2025_10_23_212859_add_staff_id_to_staff_profiles_table.php`
4. `database/migrations/2025_10_23_213508_add_customer_id_to_customer_profiles_table.php`
5. `HYBRID_ID_SYSTEM_IMPLEMENTATION.md` - This documentation

### Modified Files:
1. `app/Models/User.php` - Added user_id generation
2. `app/Models/StaffProfile.php` - Added staff_id generation & fixed role_id
3. `app/Models/CustomerProfile.php` - Added customer_id generation

---

## Support & Maintenance

### Adding New Position Codes

Edit `IdGeneratorService::POSITION_CODES`:

```php
const POSITION_CODES = [
    // ... existing codes
    'new_position' => 'NPO', // Add new 3-letter code
];
```

### Changing ID Format

If you need to change the format in the future, only modify `IdGeneratorService` methods. The database structure and model relationships remain unchanged.

### Year Rollover

IDs automatically reset sequence numbers on January 1st each year. No manual intervention required.

---

**Implementation Date:** October 23, 2025
**Status:** ✅ Complete & Tested
**Developer Notes:** All core functionality implemented and verified. Optional view updates can be done incrementally as needed.
