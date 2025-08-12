<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'admin';

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Admins management
            'admins.view', 'admins.create', 'admins.edit', 'admins.delete',

            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',

            // Devices
            'devices.view', 'devices.create', 'devices.edit', 'devices.delete',
            'devices.revoke-token', 'devices.bulk-delete',

            // Badges
            'badges.view', 'badges.create', 'badges.edit', 'badges.delete',
            'badges.toggle-status', 'badges.awarded-users',

            // Leaderboards
            'leaderboards.view', 'leaderboards.create', 'leaderboards.edit', 'leaderboards.delete',
            'leaderboards.bulk-delete', 'leaderboards.recalculate',

            // Notifications
            'notifications.view', 'notifications.delete', 'notifications.mark', 'notifications.bulk-delete',

            // Quran content
            'chapters.view', 'chapters.create', 'chapters.edit', 'chapters.delete',
            'juzs.view', 'juzs.create', 'juzs.edit', 'juzs.delete',
            'verses.view', 'verses.create', 'verses.edit', 'verses.delete',
            'words.view', 'words.create', 'words.edit', 'words.delete',
            'tafsirs.view', 'tafsirs.create', 'tafsirs.edit', 'tafsirs.delete',
            'reciters.view', 'reciters.create', 'reciters.edit', 'reciters.delete',
            'recitations.view', 'recitations.create', 'recitations.edit', 'recitations.delete',

            // Audit Logs
            'audit-logs.view', 'audit-logs.create', 'audit-logs.edit', 'audit-logs.delete',

            // Roles & Permissions
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guard,
            ]);
        }

        // Super Admin role with all permissions
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => $guard,
        ]);
        $superAdmin->syncPermissions(Permission::where('guard_name', $guard)->get());

        // Manager role with limited permissions (example)
        $manager = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => $guard,
        ]);
        $manager->syncPermissions(Permission::whereIn('name', [
            'admins.view',
            'roles.view',
            'permissions.view',
        ])->where('guard_name', $guard)->get());
    }
}


