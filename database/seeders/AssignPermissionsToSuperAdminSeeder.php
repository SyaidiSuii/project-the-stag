<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignPermissionsToSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create super-admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Get all permissions
        $allPermissions = Permission::all();

        // Assign all permissions to super-admin
        $superAdminRole->syncPermissions($allPermissions);

        $this->command->info("✓ Assigned {$allPermissions->count()} permissions to super-admin role.");
        $this->command->info("Note: Super-admin also has automatic access via Gate::before in AppServiceProvider.");
    }
}
