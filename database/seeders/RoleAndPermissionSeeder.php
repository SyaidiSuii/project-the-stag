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
            
            // Dashboard & Reports
            'view-dashboard',
            'view-reports',
            'manage-settings',
            
            // Project specific permissions
            'manage-projects',
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',

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
            'update-reservation-status',

            // Table Layout Config
            'view-table-layouts', 
            'create-table-layouts', 
            'edit-table-layouts', 
            'delete-table-layouts',
            'toggle-table-layouts', 
            'duplicate-table-layouts', 
            'view-table-layout-stats',

            // Menu Items
            'view-menu-items', 
            'create-menu-items', 
            'edit-menu-items', 
            'delete-menu-items',
            'toggle-menu-items', 
            'feature-menu-items', 
            'rate-menu-items',

            // Orders
            'view-orders', 
            'create-orders', 
            'edit-orders', 
            'delete-orders',
            'update-order-status', 
            'update-payment-status', 
            'cancel-orders', 
            'duplicate-orders',

            // Quick Reorders
            'view-quick-reorders', 
            'create-quick-reorders', 
            'edit-quick-reorders', 
            'delete-quick-reorders',
            'convert-quick-reorder', 
            'duplicate-quick-reorder', 
            'update-reorder-frequency', 
            'bulk-delete-quick-reorder',

            // Order Items
            'view-order-items', 
            'create-order-items', 
            'edit-order-items', 
            'delete-order-items',
            'update-order-item-status', 
            'bulk-update-order-item-status', 
            'calculate-order-total',

            // Order ETAs
            'view-order-etas', 
            'create-order-etas', 
            'edit-order-etas', 
            'delete-order-etas',
            'update-order-eta', 
            'mark-order-eta-completed', 
            'notify-order-eta-customer', 
            'view-delayed-orders',

            // Order Tracking
            'view-order-trackings', 
            'create-order-trackings', 
            'edit-order-trackings', 
            'delete-order-trackings',
            'update-order-tracking-status', 
            'view-order-history', 
            'view-active-orders', 
            'view-performance-stats',

            // Sale Analytics
            'view-sale-analytics', 
            'generate-sale-analytics', 
            'view-customer-analytics', 
            'view-trends',

            // Table Sessions
            'view-table-sessions', 
            'create-table-sessions', 
            'edit-table-sessions', 
            'delete-table-sessions',
            'complete-table-sessions', 
            'extend-table-sessions', 
            'regenerate-qr-table-session', 
            'expire-old-sessions',

            // Menu Customizations
            'menu-customizations.view', 
            'menu-customizations.create', 
            'menu-customizations.update', 
            'menu-customizations.delete',
            'menu-customizations.export', 
            'menu-customizations.statistics', 
            'menu-customizations.bulk-delete', 
            'menu-customizations.by-order-item',

            // Push Notifications
            'view-notifications', 
            'create-notifications', 
            'send-notifications', 
            'delete-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Manager gets management permissions
        $managerRole->syncPermissions([
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-dashboard',
            'view-reports',
            'manage-projects',
            'view-projects',
            'create-projects',
            'edit-projects'
        ]);

        // Customer gets ordering permissions
        $customerRole->syncPermissions([
            'view-menu-items',
            'create-orders',
            'view-orders',
            'view-table-sessions',
            'create-table-reservations',
            'view-table-reservations'
        ]);

        // User gets basic permissions
        $userRole->syncPermissions([
            'view-dashboard',
            'view-projects'
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
