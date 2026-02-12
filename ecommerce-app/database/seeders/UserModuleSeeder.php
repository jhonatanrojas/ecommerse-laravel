<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para el módulo de usuarios
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.toggle-status',
        ];

        $guard = 'admin';

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        // Asignar permisos al rol admin si existe (guard admin)
        $adminRole = Role::where(['name' => 'admin', 'guard_name' => $guard])->first();
        
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('Permisos del módulo de usuarios creados exitosamente.');
    }
}
