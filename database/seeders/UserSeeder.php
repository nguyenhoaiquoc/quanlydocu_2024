<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo quyền
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'manage categories']);

        // Tạo role
        $userRole = Role::create(['name' => 'User']);
        $adminRole = Role::create(['name' => 'Admin']);
        $superAdminRole = Role::create(['name' => 'Super-Admin']);

        // Gán quyền cho vai trò
        $adminRole->givePermissionTo(['manage products', 'manage categories']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Tạo user và gán vai trò
        $user = User::create([
            'name' => 'Super-Admin User',
            'email' => 'superadmin@tdc.edu.vn',
               'phone' => '0909303735',
            'password' => Hash::make('123456789'),
        ]);
        $user->assignRole($superAdminRole);

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@tdc.edu.vn',
               'phone' => '0909303425',
            'password' => Hash::make('123456789'),
        ]);
        $user->assignRole($adminRole);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@tdc.edu.vn',
            'phone' => '0909303725',
            'password' => Hash::make('123456789'),
        ]);
        $user->assignRole($userRole);
    }
}
