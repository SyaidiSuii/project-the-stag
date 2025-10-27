<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class KitchenStaffRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create kitchen_staff role if it doesn't exist
        $kitchenStaffRole = Role::firstOrCreate(
            ['name' => 'kitchen_staff'],
            ['guard_name' => 'web']
        );

        // Define kitchen staff permissions
        $permissions = [
            'kitchen.view.own',         // View own station only
            'kitchen.order.update',     // Update order status
            'kitchen.help.request',     // Request help from manager
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Assign permissions to kitchen_staff role
        $kitchenStaffRole->syncPermissions($permissions);

        $this->command->info('✓ Kitchen staff role created with permissions:');
        foreach ($permissions as $permission) {
            $this->command->info("  - {$permission}");
        }

        // Also ensure admin has all kitchen permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'kitchen.view.all',         // View all stations
                'kitchen.view.own',         // View own station
                'kitchen.order.update',     // Update order status
                'kitchen.redistribute',     // Redistribute orders
                'kitchen.analytics',        // View analytics
                'kitchen.config',          // Configure settings
            ];

            foreach ($adminPermissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission],
                    ['guard_name' => 'web']
                );
            }

            $adminRole->givePermissionTo($adminPermissions);
            $this->command->info('✓ Admin role updated with all kitchen permissions');
        }

        // Same for manager role
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerPermissions = [
                'kitchen.view.all',
                'kitchen.view.own',
                'kitchen.order.update',
                'kitchen.redistribute',
                'kitchen.analytics',
            ];

            foreach ($managerPermissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission],
                    ['guard_name' => 'web']
                );
            }

            $managerRole->givePermissionTo($managerPermissions);
            $this->command->info('✓ Manager role updated with kitchen permissions');
        }
    }
}
