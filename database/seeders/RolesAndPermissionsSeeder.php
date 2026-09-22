<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.manage',
            'branches.view',
            'branches.manage',
            'categories.manage',
            'products.manage',
            'inventory.view',
            'inventory.manage',
            'orders.view',
            'orders.manage',
            'reports.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $rolePermissions = [
            'superadmin' => $permissions,
            'administrador-general' => $permissions,
            'gerente-sucursal' => [
                'branches.view',
                'categories.manage',
                'products.manage',
                'inventory.view',
                'inventory.manage',
                'orders.view',
                'orders.manage',
                'reports.view',
            ],
            'cajero' => ['orders.view', 'orders.manage'],
            'operador-pedidos' => ['orders.view', 'orders.manage'],
            'encargado-inventario' => ['inventory.view', 'inventory.manage'],
            'cliente' => [],
        ];

        foreach ($rolePermissions as $roleName => $assignedPermissions) {
            Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ])->syncPermissions($assignedPermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
