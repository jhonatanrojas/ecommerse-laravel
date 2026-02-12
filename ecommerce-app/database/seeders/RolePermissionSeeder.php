<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir permisos
        $permissions = [
            'manage users',
            'manage products',
            'manage orders',
            'manage coupons',
            'view reports',
        ];

        $guard = 'admin';

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        // Definir roles (solo guard admin; clientes no usan estos roles)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => $guard]);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => $guard]);

        // Asignar todos los permisos al rol admin
        $adminRole->syncPermissions(Permission::all());

        // Asignar permisos parciales al rol manager
        $managerPermissions = [
            'manage products',
            'manage orders',
            'manage coupons',
            'view reports',
        ];

        $managerRole->syncPermissions($managerPermissions);

        // El rol customer no recibe permisos explícitos aquí
        $customerRole->syncPermissions([]);
    }
}

