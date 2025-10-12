# Panduan Spatie Permission untuk Laravel

## Installation dan Setup

Spatie Permission telah diinstall dan dikonfigurasi dalam projek ini. Berikut adalah langkah-langkah yang telah dilakukan:

1. **Package Installation**: `spatie/laravel-permission` telah diinstall via Composer
2. **Migration**: Permission tables telah dibuat 
3. **User Model**: HasRoles trait telah ditambah ke User model
4. **Seeder**: RoleAndPermissionSeeder telah dibuat dengan roles dan permissions basic

## Default Roles dan Permissions

### Roles:
- **admin**: Full access ke semua permissions
- **staff**: Limited access (view users, manage customers, view dashboard)
- **customer**: Basic access (view dashboard sahaja)

### Permissions:
- `view-users`, `create-users`, `edit-users`, `delete-users`
- `view-roles`, `create-roles`, `edit-roles`, `delete-roles`
- `view-permissions`, `assign-permissions`
- `manage-staff`, `manage-customers`
- `view-dashboard`

## Basic Usage

### 1. Check User Permissions

```php
// Check if user has permission
if (auth()->user()->can('edit-users')) {
    // User can edit users
}

// Check if user has role
if (auth()->user()->hasRole('admin')) {
    // User is admin
}

// Check multiple permissions
if (auth()->user()->hasAnyPermission(['edit-users', 'delete-users'])) {
    // User has at least one of these permissions
}
```

### 2. Assign Roles dan Permissions

```php
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$user = User::find(1);

// Assign role to user
$user->assignRole('admin');
$user->assignRole(['admin', 'staff']); // Multiple roles

// Remove role from user
$user->removeRole('admin');

// Sync roles (remove all current roles and assign new ones)
$user->syncRoles(['admin', 'staff']);

// Assign permission directly to user
$user->givePermissionTo('edit-users');

// Remove permission from user
$user->revokePermissionTo('edit-users');
```

### 3. Middleware Usage

Dalam routes atau controllers, anda boleh gunakan middleware:

```php
// Dalam routes/web.php
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin', 'AdminController@index');
});

Route::group(['middleware' => ['auth', 'permission:edit-users']], function () {
    Route::get('/users/edit', 'UserController@edit');
});

// Dalam Controller constructor
public function __construct()
{
    $this->middleware('permission:view-users', ['only' => ['index', 'show']]);
    $this->middleware('permission:create-users', ['only' => ['create', 'store']]);
}
```

### 4. Blade Templates

Dalam Blade views:

```blade
@role('admin')
    <p>You are an admin!</p>
@endrole

@hasrole('admin')
    <p>You are an admin!</p>
@endhasrole

@can('edit-users')
    <a href="/users/edit">Edit Users</a>
@endcan

@hasanyrole('admin|staff')
    <p>You are either admin or staff</p>
@endhasanyrole

@hasallroles('admin|staff')
    <p>You have both admin and staff roles</p>
@endhasallroles
```

## Advanced Usage

### 1. Create Custom Roles dan Permissions

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create new permission
$permission = Permission::create(['name' => 'manage-products']);

// Create new role
$role = Role::create(['name' => 'manager']);

// Assign permission to role
$role->givePermissionTo('manage-products');

// Or assign multiple permissions
$role->givePermissionTo(['manage-products', 'view-sales']);
```

### 2. Check Permissions dalam Controller

```php
public function edit(User $user)
{
    // Method 1: Using can() helper
    if (!auth()->user()->can('edit-users')) {
        abort(403, 'Unauthorized');
    }
    
    // Method 2: Using authorize() method
    $this->authorize('edit-users');
    
    // Method 3: Using Gate
    if (Gate::denies('edit-users')) {
        abort(403, 'Unauthorized');
    }
    
    return view('users.edit', compact('user'));
}
```

### 3. Super Admin Role

Untuk membuat super admin yang mempunyai semua permissions:

```php
// Dalam AppServiceProvider boot() method
use Illuminate\Support\Facades\Gate;

Gate::before(function ($user, $ability) {
    return $user->hasRole('super-admin') ? true : null;
});
```

## Testing Permissions

Untuk test permissions dalam aplikasi anda:

```php
// Login sebagai admin user yang telah dibuat
$admin = User::where('email', 'admin@example.com')->first();
Auth::login($admin);

// Test permissions
dd([
    'can_view_users' => auth()->user()->can('view-users'),
    'has_admin_role' => auth()->user()->hasRole('admin'),
    'all_permissions' => auth()->user()->getAllPermissions()->pluck('name'),
    'all_roles' => auth()->user()->getRoleNames(),
]);
```

## Cache Management

Spatie Permission menggunakan cache untuk performance. Untuk clear cache:

```bash
php artisan permission:cache-reset
```

## Configuration

Configuration file berada di `config/permission.php`. Anda boleh customize:
- Table names
- Model classes  
- Cache settings
- Middleware aliases

## Migration dari Custom Role System

Jika anda mempunyai custom role system sebelum ini, anda perlu:

1. Update semua controller yang menggunakan custom role methods
2. Update Blade views yang check custom roles
3. Migrate data dari custom role tables ke Spatie permission tables

## Troubleshooting

1. **Permission denied errors**: Pastikan user mempunyai role/permission yang betul
2. **Cache issues**: Run `php artisan permission:cache-reset`
3. **Migration conflicts**: Pastikan tidak ada conflict dengan custom role tables

## Useful Artisan Commands

```bash
# Clear permission cache
php artisan permission:cache-reset

# Show all permissions
php artisan permission:show

# Create permission (custom command - you can create this)
php artisan make:permission "manage-products"
```
