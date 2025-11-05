<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Role Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'assign-roles',

            // Permission Management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            'assign-permissions',

            // Dashboard
            'view-dashboard',

            // Tables
            'view-tables',
            'create-tables',
            'edit-tables',
            'delete-tables',

            // Table Reservations
            'view-table-reservations',
            'create-table-reservations',
            'edit-table-reservations',
            'delete-table-reservations',

            // Table Layout Config
            'view-table-layout-config',
            'create-table-layout-config',
            'edit-table-layout-config',
            'delete-table-layout-config',

            // Menu Items
            'view-menu-items',
            'create-menu-items',
            'edit-menu-items',
            'delete-menu-items',

            // Orders
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',

            // Quick Reorders
            'view-quick-reorders',
            'create-quick-reorders',
            'edit-quick-reorders',
            'delete-quick-reorders',


            // Sale Analytics
            'view-sale-analytics',
            'create-sale-analytics',
            'edit-sale-analytics',
            'delete-sale-analytics',

            // Table QR Codes
            'view-table-qrcodes',
            'create-table-qrcodes',
            'edit-table-qrcodes',
            'delete-table-qrcodes',

            // Menu Customizations
            'view-menu-customizations',
            'create-menu-customizations',
            'edit-menu-customizations',
            'delete-menu-customizations',


            // Categories (missing - added based on CategoryController routes)
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // Rewards (missing - added based on RewardsController routes)
            'view-rewards',
            'create-rewards',
            'edit-rewards',
            'delete-rewards',

            // User menu permission (missing - used in web.php middleware)
            'user.menu',

            // Profile management (missing - used for profile routes)
            'view-profile',
            'edit-profile',
            'delete-profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Super Admin gets all permissions (also has Gate::before in AppServiceProvider)
        $superAdminRole->syncPermissions(Permission::all());

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Manager gets management permissions
        $managerRole->syncPermissions([
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-dashboard',
            'view-tables',
            'create-tables',
            'edit-tables',
            'view-table-reservations',
            'create-table-reservations',
            'edit-table-reservations',
            'view-categories',
            'create-categories',
            'edit-categories',
            'view-menu-items',
            'create-menu-items',
            'edit-menu-items',
            'view-orders',
            'create-orders',
            'edit-orders',
            'view-sale-analytics',
            'view-rewards',
            'create-rewards',
            'edit-rewards'
        ]);

        // Customer gets ordering permissions
        $customerRole->syncPermissions([
            'view-menu-items',
            'create-orders',
            'view-orders',
            'create-table-reservations',
            'view-table-reservations'
        ]);

        // User gets basic permissions
        $userRole->syncPermissions([
            'user.menu',
            'view-profile',
            'edit-profile'
        ]);

        $adminEmail = env('DEFAULT_ADMIN_EMAIL', 'admin@example.com');
        $adminPass  = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123!');

        // Create a default admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin User',
                'password' => bcrypt($adminPass),
                'email_verified_at' => now(),
            ]
        );

        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $this->command->info("✅ Roles, permissions & admin user ready!");
        $this->command->warn("🔑 Admin login: {$adminEmail} | {$adminPass}");
    }
}
