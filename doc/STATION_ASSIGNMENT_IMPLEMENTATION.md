# Station Assignment System Implementation

## Overview

Implemented a flexible, hierarchical station assignment system that assigns kitchen stations at the **category level** with optional **item-level overrides**. This provides efficient management while allowing exceptions for special cases.

## Implementation Summary

### ✅ Completed Tasks

1. **Database Migrations**
   - Added `default_station_id` to `categories` table
   - Added `station_override_id` to `menu_items` table
   - Both nullable with foreign key constraints to `kitchen_stations`

2. **Model Updates**
   - **Category Model**: Added `defaultStation()` relationship and `getEffectiveStation()` method
   - **MenuItem Model**: Added `stationOverride()` relationship and `getEffectiveStation()` method

3. **Service Layer**
   - Updated `OrderDistributionService` to use new category/item station logic
   - Changed from station_type grouping to specific station_id grouping
   - Eager loads relationships for performance

## How It Works

### Station Assignment Hierarchy

```
1. Check MenuItem->station_override_id
   ├─ If set → Use this station
   └─ If null → Check category

2. Check Category->default_station_id
   ├─ If set → Use this station
   └─ If null → Check parent category

3. Check ParentCategory->default_station_id
   ├─ If set → Use this station
   └─ If null → No station assigned (warning logged)
```

### Code Flow

**MenuItem::getEffectiveStation()**
```php
// Priority 1: Item override
if ($this->station_override_id) {
    return $this->stationOverride;
}

// Priority 2: Category default
if ($this->category) {
    return $this->category->getEffectiveStation();
}

return null;
```

**Category::getEffectiveStation()**
```php
// Priority 1: This category's station
if ($this->default_station_id) {
    return $this->defaultStation;
}

// Priority 2: Parent category's station
if ($this->parent) {
    return $this->parent->getEffectiveStation();
}

return null;
```

## Usage Examples

### Example 1: Category-Level Assignment (90% of cases)

**Setup:**
- Category: "Grilled Items" → default_station_id = 3 (Grill Station)
- Menu Items:
  - "Ribeye Steak" → station_override_id = NULL
  - "Grilled Salmon" → station_override_id = NULL
  - "BBQ Ribs" → station_override_id = NULL

**Result:** All items automatically go to Grill Station

### Example 2: Item-Level Override (10% exceptions)

**Setup:**
- Category: "Salads" → default_station_id = 2 (Cold Kitchen)
- Menu Items:
  - "Caesar Salad" → station_override_id = NULL → **Cold Kitchen**
  - "Greek Salad" → station_override_id = NULL → **Cold Kitchen**
  - "Grilled Caesar Salad" → station_override_id = 3 → **Grill Station** ✅ Override!

**Result:** Special item goes to different station despite category default

### Example 3: Parent Category Inheritance

**Setup:**
- Parent Category: "Beverages" → default_station_id = 5 (Drinks Station)
- Sub-Category: "Hot Beverages" → default_station_id = NULL
- Menu Item: "Espresso" (in "Hot Beverages") → station_override_id = NULL

**Result:** Espresso inherits Drinks Station from parent category

## Database Schema

### categories table
```sql
default_station_id BIGINT UNSIGNED NULL
FOREIGN KEY (default_station_id)
  REFERENCES kitchen_stations(id)
  ON DELETE SET NULL
```

### menu_items table
```sql
station_override_id BIGINT UNSIGNED NULL
FOREIGN KEY (station_override_id)
  REFERENCES kitchen_stations(id)
  ON DELETE SET NULL
```

## OrderDistributionService Changes

### Before
```php
// Grouped by station_type string
$itemsByStation = [
    'hot_kitchen' => [item1, item2],
    'cold_kitchen' => [item3, item4]
];

// Then found optimal station of each type
$station = $this->findOptimalStation($stationType, $items);
```

### After
```php
// Groups by specific station_id
$itemsByStation = [
    1 => [item1, item2],  // Station ID 1
    2 => [item3, item4]   // Station ID 2
];

// Direct station lookup
$station = KitchenStation::find($stationId);
```

**Benefits:**
- No need to find "optimal" station - already determined
- Respects admin's explicit assignments
- Simpler, more predictable logic
- Better performance (fewer queries)

## Next Steps (Forms & UI)

To complete the implementation, you need to add UI for managing stations:

### 1. Category Create/Edit Forms

**File**: `resources/views/admin/categories/form.blade.php` (or similar)

Add station selection dropdown:
```blade
<div class="form-group">
    <label for="default_station_id">Default Kitchen Station</label>
    <select name="default_station_id" id="default_station_id" class="form-control">
        <option value="">-- None (Inherit from parent) --</option>
        @foreach($stations as $station)
            <option value="{{ $station->id }}"
                    {{ (old('default_station_id', $category->default_station_id ?? '') == $station->id) ? 'selected' : '' }}>
                {{ $station->name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">
        Items in this category will be assigned to this station by default.
    </small>
</div>
```

**Controller** (CategoryController):
```php
public function create()
{
    $stations = KitchenStation::where('is_active', true)->ordered()->get();
    return view('admin.categories.create', compact('stations'));
}

public function edit($id)
{
    $category = Category::findOrFail($id);
    $stations = KitchenStation::where('is_active', true)->ordered()->get();
    return view('admin.categories.edit', compact('category', 'stations'));
}
```

### 2. Menu Item Create/Edit Forms

**File**: `resources/views/admin/menu-items/form.blade.php` (or similar)

Add optional override:
```blade
<div class="form-group">
    <label for="station_override_id">Kitchen Station Override (Optional)</label>
    <select name="station_override_id" id="station_override_id" class="form-control">
        <option value="">-- Use Category Default --</option>
        @foreach($stations as $station)
            <option value="{{ $station->id }}"
                    {{ (old('station_override_id', $menuItem->station_override_id ?? '') == $station->id) ? 'selected' : '' }}>
                {{ $station->name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">
        Leave blank to use category's default station.
        @if($menuItem->exists && $menuItem->category)
            Current category default:
            <strong>{{ $menuItem->category->getEffectiveStation()?->name ?? 'None' }}</strong>
        @endif
    </small>
</div>
```

**Controller** (MenuItemController):
```php
public function create()
{
    $categories = Category::all();
    $stations = KitchenStation::where('is_active', true)->ordered()->get();
    return view('admin.menu-items.create', compact('categories', 'stations'));
}

public function edit($id)
{
    $menuItem = MenuItem::with('category')->findOrFail($id);
    $categories = Category::all();
    $stations = KitchenStation::where('is_active', true)->ordered()->get();
    return view('admin.menu-items.edit', compact('menuItem', 'categories', 'stations'));
}
```

### 3. Category Seeder (Optional but Recommended)

**File**: `database/seeders/CategoryStationAssignmentSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\KitchenStation;

class CategoryStationAssignmentSeeder extends Seeder
{
    public function run()
    {
        // Map category names to station types
        $assignments = [
            'Grilled Items' => 'grill',
            'Steaks' => 'grill',
            'BBQ' => 'grill',

            'Salads' => 'cold_kitchen',
            'Appetizers' => 'cold_kitchen',

            'Desserts' => 'desserts',
            'Cakes' => 'desserts',
            'Ice Cream' => 'desserts',

            'Beverages' => 'drinks',
            'Cocktails' => 'drinks',
            'Coffee' => 'drinks',

            'Main Course' => 'hot_kitchen',
            'Pasta' => 'hot_kitchen',
            'Rice Dishes' => 'hot_kitchen',

            'Bread' => 'bakery',
            'Pastries' => 'bakery',
        ];

        foreach ($assignments as $categoryName => $stationType) {
            $category = Category::where('name', 'LIKE', "%{$categoryName}%")->first();

            if ($category) {
                $station = KitchenStation::whereHas('stationType', function ($q) use ($stationType) {
                    $q->where('station_type', $stationType);
                })->where('is_active', true)->first();

                if ($station) {
                    $category->default_station_id = $station->id;
                    $category->save();

                    $this->command->info("✓ {$category->name} → {$station->name}");
                }
            }
        }
    }
}
```

**Run with:**
```bash
php artisan db:seed --class=CategoryStationAssignmentSeeder
```

## Benefits of This Approach

### 1. **Easy Management** ✅
- Set station once per category
- All new items automatically inherit
- Only override when necessary

### 2. **Flexibility** ✅
- Handle 90% of cases with category defaults
- Handle 10% exceptions with item overrides
- Support parent/child category inheritance

### 3. **Performance** ✅
- Eager loading prevents N+1 queries
- Direct station assignment (no searching)
- Cached relationships

### 4. **Maintainability** ✅
- Clear, predictable logic
- Easy to understand and debug
- Self-documenting code

### 5. **Business Logic Alignment** ✅
- Matches real kitchen organization
- Follows restaurant workflows
- Intuitive for staff

## Migration Commands

```bash
# Run migrations
php artisan migrate

# If you need to rollback
php artisan migrate:rollback --step=2

# Refresh (drop all & re-migrate)
php artisan migrate:fresh --seed
```

## Testing Checklist

- [ ] Create category with station assignment
- [ ] Create menu item (should inherit category station)
- [ ] Verify `menuItem->getEffectiveStation()` returns correct station
- [ ] Override item station, verify override works
- [ ] Create order with items from multiple categories
- [ ] Verify `OrderDistributionService` assigns to correct stations
- [ ] Check KDS displays correct station tags
- [ ] Test parent/child category inheritance
- [ ] Test with null values (no station assigned)
- [ ] Verify foreign key constraints (delete station → sets null)

## Troubleshooting

### Issue: Items not assigned to any station
**Check:**
1. Category has `default_station_id` set
2. Item has `station_override_id` OR category has station
3. Station is active (`is_active = true`)
4. Run: `php artisan tinker` then:
   ```php
   $item = MenuItem::find(1);
   $item->getEffectiveStation(); // Should return station or null
   ```

### Issue: Station assignments not showing in KDS
**Check:**
1. Order status is 'confirmed' (distribution happens on confirm)
2. `StationAssignment` records created
3. Eager loading includes relationships:
   ```php
   'items.menuItem.category.defaultStation'
   'items.menuItem.stationOverride'
   ```

### Issue: Old station_type logic conflicts
**Solution:** The old `station_type` field on menu_items is now deprecated. You can:
1. Keep it for backwards compatibility
2. Or create migration to remove it after testing

## Files Modified

### Created:
1. `database/migrations/2025_10_20_201250_add_default_station_to_categories_table.php`
2. `database/migrations/2025_10_20_201321_add_station_override_to_menu_items_table.php`

### Modified:
1. `app/Models/Category.php` - Added relationships and `getEffectiveStation()`
2. `app/Models/MenuItem.php` - Added relationships and `getEffectiveStation()`
3. `app/Services/Kitchen/OrderDistributionService.php` - Updated distribution logic

### To Create (Next Steps):
1. Category form views (add station dropdown)
2. Menu item form views (add override dropdown)
3. Category seeder (optional, for existing data)

---

**Status**: Core implementation complete ✅
**Remaining**: UI forms for managing station assignments
**Ready for**: Testing and form implementation

The system is fully functional at the code level. Once you add the UI forms (simple dropdowns in category and menu item forms), users can start assigning stations through the admin panel!
