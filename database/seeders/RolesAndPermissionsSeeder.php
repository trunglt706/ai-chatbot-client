<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions users
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);

        // Permissions roles
        Permission::firstOrCreate(['name' => 'view roles']);
        Permission::firstOrCreate(['name' => 'create roles']);
        Permission::firstOrCreate(['name' => 'edit roles']);
        Permission::firstOrCreate(['name' => 'delete roles']);

        // Permissions modules
        Permission::firstOrCreate(['name' => 'view modules']);
        Permission::firstOrCreate(['name' => 'edit modules']);
        Permission::firstOrCreate(['name' => 'show modules']); // dành cho user không có quyền xem các module đã phát hành

        // Permissions activity logs
        Permission::firstOrCreate(['name' => 'view activity logs']);

        // Permissions log storage
        Permission::firstOrCreate(['name' => 'view log storage']);

        // Permissions request forms
        Permission::firstOrCreate(['name' => 'view request forms']);
        Permission::firstOrCreate(['name' => 'create request forms']);
        Permission::firstOrCreate(['name' => 'edit request forms']);
        Permission::firstOrCreate(['name' => 'delete request forms']);

        // Permissions posts
        Permission::firstOrCreate(['name' => 'view posts']);
        Permission::firstOrCreate(['name' => 'create posts']);
        Permission::firstOrCreate(['name' => 'edit posts']);
        Permission::firstOrCreate(['name' => 'delete posts']);
        Permission::firstOrCreate(['name' => 'show posts']);

        // Permissions sponsors
        Permission::firstOrCreate(['name' => 'view sponsors']);
        Permission::firstOrCreate(['name' => 'create sponsors']);
        Permission::firstOrCreate(['name' => 'edit sponsors']);
        Permission::firstOrCreate(['name' => 'delete sponsors']);

        // Permissions system config
        Permission::firstOrCreate(['name' => 'manage system']);
        Permission::firstOrCreate(['name' => 'clear any table data']);
        Permission::firstOrCreate(['name' => 'clear all system data']);
        Permission::firstOrCreate(['name' => 'test services']);

        // telescope
        Permission::firstOrCreate(['name' => 'manage telescope']);

        // Tạo các Roles và gán Permissions
        $adminRole = Role::firstOrCreate(['name' => 'Account Admin']);
        $adminRole->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'Account Demo']);

        // Gán Role cho người dùng (ví dụ tạo một user admin)
        $user = User::firstOrCreate(
            ['email' => 'lamthanhtrung706@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('trunglt706@#'),
                'status' => User::ACTIVE,
                'email_verified_at' => now(),
                'code' => 'trunglt706',
                'root' => true,
            ]
        );
        $user->assignRole('Account Admin');
    }
}
