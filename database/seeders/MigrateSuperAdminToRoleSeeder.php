<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MigrateSuperAdminToRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Find all users with is_super_admin = true
        $superAdmins = User::where('is_super_admin', true)->get();

        foreach ($superAdmins as $admin) {
            // Assign super-admin role
            if (!$admin->hasRole('super-admin')) {
                $admin->assignRole('super-admin');
                $this->command->info("Assigned super-admin role to: {$admin->email}");
            }
        }

        $this->command->info("Migration complete. {$superAdmins->count()} super admin(s) migrated to role-based system.");
    }
}
