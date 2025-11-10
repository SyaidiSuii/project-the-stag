<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions by grouping them based on features
        $permissionsByGroup = [
            'Dashboard' => [
                'view-dashboard',
            ],
            'User Management' => [
                'view-users', 'create-users', 'edit-users', 'delete-users',
            ],
            'Role Management' => [
                'view-roles', 'create-roles', 'edit-roles', 'delete-roles', 'assign-roles',
            ],
            'Permission Management' => [
                'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions', 'assign-permissions',
            ],
            'Settings' => [
                'view-settings', 'manage-settings',
            ],
            'Homepage Content' => [
                'view-homepage-content', 'manage-homepage-content',
            ],
            'Kitchen Management' => [
                'view-kitchen-dashboard', 'view-kds', 'view-kitchen-orders', 'view-kitchen-analytics',
                'manage-kitchen-stations', 'manage-station-types',
            ],
            'Table & Layout' => [
                'view-tables', 'create-tables', 'edit-tables', 'delete-tables',
                'view-table-reservations', 'create-table-reservations', 'edit-table-reservations', 'delete-table-reservations',
                'view-table-layout-config', 'create-table-layout-config', 'edit-table-layout-config', 'delete-table-layout-config',
                'view-table-qrcodes', 'create-table-qrcodes', 'edit-table-qrcodes', 'delete-table-qrcodes',
            ],
            'Menu Management' => [
                'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
                'view-menu-items', 'create-menu-items', 'edit-menu-items', 'delete-menu-items',
                'view-menu-customizations', 'create-menu-customizations', 'edit-menu-customizations', 'delete-menu-customizations',
            ],
            'Order Management' => [
                'view-orders', 'create-orders', 'edit-orders', 'delete-orders', 'update-order-status',
                'view-quick-reorders', 'create-quick-reorders', 'edit-quick-reorders', 'delete-quick-reorders',
            ],
            'Promotions' => [
                'view-promotions', 'create-promotions', 'edit-promotions', 'delete-promotions',
            ],
            'Loyalty & Rewards' => [
                'view-rewards-dashboard',
                'manage-reward-rules',
                'manage-voucher-templates',
                'manage-voucher-collections',
                'manage-loyalty-tiers',
                'manage-achievements',
                'manage-bonus-challenges',
                'manage-loyalty-settings',
                'view-redemptions',
                'manage-redemptions',
                'view-loyalty-members',
                'manage-loyalty-members',
            ],
            'Analytics & Reports' => [
                'view-reports', 'generate-reports',
                'view-sale-analytics', 'generate-sale-analytics',
            ],
            'FCM Notifications' => [
                'view-fcm-stats', 'send-fcm-notifications',
            ],
            'General' => [
                'user.menu', // For non-admin staff side menu
                'view-profile', 'edit-profile', 'delete-profile',
            ]
        ];

        // Create all permissions
        foreach ($permissionsByGroup as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }
        }

        $this->command->info('✅ All permissions created successfully.');

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $kitchenStaffRole = Role::firstOrCreate(['name' => 'kitchen_staff']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $userRole = Role::firstOrCreate(['name' => 'user']); // General authenticated user

        // Assign all permissions to Super Admin and Admin
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);
        $adminRole->syncPermissions($allPermissions);

        $this->command->info('✅ Super Admin and Admin roles synced with all permissions.');

        // Assign specific permissions to Manager
        $managerPermissions = array_merge(
            $permissionsByGroup['Dashboard'],
            $permissionsByGroup['User Management'],
            $permissionsByGroup['Table & Layout'],
            $permissionsByGroup['Menu Management'],
            $permissionsByGroup['Order Management'],
            $permissionsByGroup['Promotions'],
            $permissionsByGroup['Analytics & Reports'],
            $permissionsByGroup['General']
        );
        $managerRole->syncPermissions($managerPermissions);
        $this->command->info('✅ Manager role synced with relevant permissions.');

        // Assign permissions to Kitchen Staff
        $kitchenStaffRole->syncPermissions([
            'view-kds', 'view-kitchen-orders', 'update-order-status'
        ]);
        $this->command->info('✅ Kitchen Staff role synced with relevant permissions.');

        // Assign specific permissions to Cashier
        $cashierPermissions = array_merge(
            $permissionsByGroup['Dashboard'],
            $permissionsByGroup['Order Management'],
            ['view-tables', 'view-table-reservations'], // Specific Table & Layout permissions
            ['view-categories', 'view-menu-items', 'view-menu-customizations'], // Specific Menu Management permissions
            ['view-rewards-dashboard', 'view-redemptions', 'manage-redemptions', 'view-loyalty-members'], // Specific Loyalty & Rewards permissions
            $permissionsByGroup['General']
        );
        $cashierRole->syncPermissions($cashierPermissions);
        $this->command->info('✅ Cashier role synced with relevant permissions.');


        // Assign permissions to Customer
        $customerRole->syncPermissions([
            'view-profile', 'edit-profile', 'delete-profile'
        ]);
        $this->command->info('✅ Customer role synced with relevant permissions.');

        // Assign permissions to general User
        $userRole->syncPermissions([
            'user.menu',
            'view-profile',
            'edit-profile'
        ]);
        $this->command->info('✅ General User role synced with relevant permissions.');


        // Create or find the default admin user
        $adminEmail = env('DEFAULT_ADMIN_EMAIL', 'admin@thestag.my');
        $adminPass  = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123!');

        try {
            $adminUser = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make($adminPass),
                    'email_verified_at' => now(),
                ]
            );

            // Assign the super-admin role to the default admin user
            if (!$adminUser->hasRole('super-admin')) {
                $adminUser->assignRole('super-admin');
            }

            $this->command->info("✅ Roles, permissions & admin user are set up.");
            $this->command->warn("🔑 Admin login: {$adminEmail} | {$adminPass}");

        } catch (\Exception $e) {
            Log::error("Failed to create or assign admin user: " . $e->getMessage());
            $this->command->error("❌ Failed to create or assign admin user. Check logs for details.");
        }
    }
}