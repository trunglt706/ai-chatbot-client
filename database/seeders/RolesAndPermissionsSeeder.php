<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
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
        $group_users = PermissionGroup::firstOrCreate(['name' => 'users']);
        Permission::firstOrCreate(['name' => 'view users'], ['group_id' => $group_users->id]);
        Permission::firstOrCreate(['name' => 'create users'], ['group_id' => $group_users->id]);
        Permission::firstOrCreate(['name' => 'edit users'], ['group_id' => $group_users->id]);
        Permission::firstOrCreate(['name' => 'delete users'], ['group_id' => $group_users->id]);

        // Permissions roles
        $group_roles = PermissionGroup::firstOrCreate(['name' => 'roles']);
        Permission::firstOrCreate(['name' => 'view roles'], ['group_id' => $group_roles->id]);
        Permission::firstOrCreate(['name' => 'create roles'], ['group_id' => $group_roles->id]);
        Permission::firstOrCreate(['name' => 'edit roles'], ['group_id' => $group_roles->id]);
        Permission::firstOrCreate(['name' => 'delete roles'], ['group_id' => $group_roles->id]);

        // Permissions modules
        $group_modules = PermissionGroup::firstOrCreate(['name' => 'modules']);
        Permission::firstOrCreate(['name' => 'view modules'], ['group_id' => $group_modules->id]);
        Permission::firstOrCreate(['name' => 'edit modules'], ['group_id' => $group_modules->id]);
        Permission::firstOrCreate(['name' => 'show modules'], ['group_id' => $group_modules->id]); // dành cho user không có quyền xem các module đã phát hành

        // Permissions request forms
        $group_forms = PermissionGroup::firstOrCreate(['name' => 'request forms']);
        Permission::firstOrCreate(['name' => 'view request forms'], ['group_id' => $group_forms->id]);
        Permission::firstOrCreate(['name' => 'create request forms'], ['group_id' => $group_forms->id]);
        Permission::firstOrCreate(['name' => 'edit request forms'], ['group_id' => $group_forms->id]);
        Permission::firstOrCreate(['name' => 'delete request forms'], ['group_id' => $group_forms->id]);

        // Permissions posts
        $group_posts = PermissionGroup::firstOrCreate(['name' => 'posts']);
        Permission::firstOrCreate(['name' => 'view posts'], ['group_id' => $group_posts->id]);
        Permission::firstOrCreate(['name' => 'create posts'], ['group_id' => $group_posts->id]);
        Permission::firstOrCreate(['name' => 'edit posts'], ['group_id' => $group_posts->id]);
        Permission::firstOrCreate(['name' => 'delete posts'], ['group_id' => $group_posts->id]);
        Permission::firstOrCreate(['name' => 'show posts'], ['group_id' => $group_posts->id]);

        // Permissions sponsors
        $group_sponsors = PermissionGroup::firstOrCreate(['name' => 'sponsors']);
        Permission::firstOrCreate(['name' => 'view sponsors'], ['group_id' => $group_sponsors->id]);
        Permission::firstOrCreate(['name' => 'create sponsors'], ['group_id' => $group_sponsors->id]);
        Permission::firstOrCreate(['name' => 'edit sponsors'], ['group_id' => $group_sponsors->id]);
        Permission::firstOrCreate(['name' => 'delete sponsors'], ['group_id' => $group_sponsors->id]);

        // Permissions system config
        $group_db = PermissionGroup::firstOrCreate(['name' => 'systems']);
        Permission::firstOrCreate(['name' => 'manage system'], ['group_id' => $group_db->id]);
        Permission::firstOrCreate(['name' => 'clear any table data'], ['group_id' => $group_db->id]);
        Permission::firstOrCreate(['name' => 'clear all system data'], ['group_id' => $group_db->id]);
        Permission::firstOrCreate(['name' => 'test services'], ['group_id' => $group_db->id]);

        // Permissions backup
        $group_backups = PermissionGroup::firstOrCreate(['name' => 'backups']);
        Permission::firstOrCreate(['name' => 'view backups'], ['group_id' => $group_backups->id]);
        Permission::firstOrCreate(['name' => 'delete backups'], ['group_id' => $group_backups->id]);
        Permission::firstOrCreate(['name' => 'create backups'], ['group_id' => $group_backups->id]);

        // telescope
        $group_logs = PermissionGroup::firstOrCreate(['name' => 'logs']);
        Permission::firstOrCreate(['name' => 'manage telescope'], ['group_id' => $group_logs->id]);
        // Permissions activity logs
        Permission::firstOrCreate(['name' => 'view activity logs'], ['group_id' => $group_logs->id]);
        // Permissions log storage
        Permission::firstOrCreate(['name' => 'view log storage'], ['group_id' => $group_logs->id]);
        Permission::firstOrCreate(['name' => 'report log storage'], ['group_id' => $group_logs->id]);

        // Tạo các Roles và gán Permissions
        $adminRole = Role::firstOrCreate(['name' => 'Account Admin', 'is_root' => true]);
        $adminRole->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'Account Demo']);

        // Gán Role cho người dùng (ví dụ tạo một user admin)
        $user = User::firstOrCreate(
            ['email' => 'lamthanhtrung706@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => '$2y$12$47XJCgXj8FWyeztI5c1UK.V34008bp8o513x7YlxFDL0DvuBGku3i',
                'status' => User::ACTIVE,
                'email_verified_at' => now(),
                'code' => 'trunglt706',
                'root' => true,
            ]
        );
        $user->assignRole('Account Admin');
    }
}
