<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador inicial usando la capa de DB para no interferir con HasUuids
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Recuperar el modelo User correspondiente
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        // Asegurar que el rol admin exista (RolePermissionSeeder debería haberlo creado)
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}

