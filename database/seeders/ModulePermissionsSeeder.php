<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ModulePermissionsSeeder extends Seeder
{
    /**
     * Seed permissions for the application's business modules.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'teacher list',
            'teacher create',
            'teacher edit',
            'teacher delete',
            'student list',
            'student create',
            'student edit',
            'student delete',
            'category-group list',
            'category-group create',
            'category-group edit',
            'category-group delete',
            'level list',
            'level create',
            'level edit',
            'level delete',
            'group list',
            'group create',
            'group edit',
            'group delete',
            'attending list',
            'attending create',
            'attending edit',
            'attending delete',
            'attending export',
            'attending export single',
            'history-movement list',
            'history-movement create',
            'history-movement edit',
            'history-movement delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->givePermissionTo($permissions);

        $writer = Role::firstOrCreate([
            'name' => 'writer',
            'guard_name' => 'web',
        ]);
        $writer->givePermissionTo(array_values(array_filter(
            $permissions,
            fn (string $permission) => str_ends_with($permission, ' list')
        )));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
