<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'manage users',
            'manage items',
            'manage sales',
            'manage payments'
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        // 2. Create Roles and Assign Permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $operatorRole = Role::findOrCreate('operator');
        $operatorRole->givePermissionTo([
            'manage items',
            'manage sales',
            'manage payments'
        ]);

        // 3. Assign admin role to default admin user
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }

        // 4. Assign operator role to default operator user
        $operatorUser = User::where('email', 'operator@mail.com')->first();
        if ($operatorUser) {
            $operatorUser->assignRole($operatorRole);
        }
    }
}
