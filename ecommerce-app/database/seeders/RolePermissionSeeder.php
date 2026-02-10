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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Definir roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

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

