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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos al rol admin si existe
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('Permisos del módulo de usuarios creados exitosamente.');
    }
}
