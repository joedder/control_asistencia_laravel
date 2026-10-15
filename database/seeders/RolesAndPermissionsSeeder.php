<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'admin user',
            'permission list', 'permission create', 'permission edit', 'permission delete',
            'role list', 'role create', 'role edit', 'role delete',
            'user list', 'user create', 'user edit', 'user delete',
            'menu list', 'menu create', 'menu edit', 'menu delete',
            'menu.item list', 'menu.item create', 'menu.item edit', 'menu.item delete',
            'teacher list', 'teacher create', 'teacher edit', 'teacher delete',
            'student list', 'student create', 'student edit', 'student delete',
            'category-group list', 'category-group create', 'category-group edit', 'category-group delete',
            'level list', 'level create', 'level edit', 'level delete',
            'group list', 'group create', 'group edit', 'group delete',
            'attending list', 'attending create', 'attending edit', 'attending delete',
            'attending export', 'attending export single',
            'history-movement list', 'history-movement create', 'history-movement edit', 'history-movement delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        Role::firstOrCreate([
            'name' => config('admin.roles.super_admin'),
            'guard_name' => 'web',
        ]);

        $administration = Role::firstOrCreate([
            'name' => 'administración',
            'guard_name' => 'web',
        ]);
        $administration->syncPermissions($permissions);

        $teacher = Role::firstOrCreate([
            'name' => 'docente',
            'guard_name' => 'web',
        ]);
        $teacher->syncPermissions([
            'admin user',
            'teacher list',
            'student list',
            'category-group list',
            'level list',
            'group list',
            'attending list',
            'attending create',
        ]);

        $this->replaceLegacyRole('admin', $administration);
        $this->replaceLegacyRole('writer', $teacher);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function replaceLegacyRole(string $oldName, Role $replacement): void
    {
        $legacyRole = Role::where('name', $oldName)->first();

        if (! $legacyRole) {
            return;
        }

        User::role($legacyRole->name)->get()->each(function (User $user) use ($legacyRole, $replacement) {
            $user->assignRole($replacement);
            $user->removeRole($legacyRole);
        });

        $legacyRole->delete();
    }
}
