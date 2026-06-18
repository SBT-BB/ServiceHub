<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = config('PermissionModule.modules');
        $rolesConfig = config('PermissionModule.roles');

        // Create Permissions
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission);
            }
        }

        // Create Roles and Assign Permissions
        foreach ($rolesConfig as $roleName => $assignedModules) {
            $role = Role::findOrCreate($roleName);

            $permissionsToAssign = [];
            foreach ($assignedModules as $moduleName) {
                if (isset($modules[$moduleName])) {
                    $permissionsToAssign = array_merge($permissionsToAssign, $modules[$moduleName]);
                }
            }

            $role->syncPermissions($permissionsToAssign);
        }

        // Create a default Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('Admin');
    }
}
