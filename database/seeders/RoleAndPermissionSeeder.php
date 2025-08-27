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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Manager gets management permissions
        $managerRole->givePermissionTo([
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

        // User gets basic permissions
        $userRole->givePermissionTo([
            'view-dashboard',
            'view-projects'
        ]);

        // Create a default admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        $adminUser->assignRole('admin');

        $this->command->info('Roles and permissions created successfully!');
    }
}
