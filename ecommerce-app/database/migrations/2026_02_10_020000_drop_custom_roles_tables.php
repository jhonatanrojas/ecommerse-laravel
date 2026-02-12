<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Primero eliminar la tabla pivot que referencia a roles y users
        if (Schema::hasTable('role_user')) {
            // SQLite no soporta dropForeign; solo dropear la tabla directamente
            if ($driver !== 'sqlite') {
                Schema::table('role_user', function (Blueprint $table) {
                    if (Schema::hasColumn('role_user', 'user_id')) {
                        $table->dropForeign(['user_id']);
                    }
                    if (Schema::hasColumn('role_user', 'role_id')) {
                        $table->dropForeign(['role_id']);
                    }
                });
            }

            Schema::dropIfExists('role_user');
        }

        // Luego eliminar la tabla roles personalizada
        if (Schema::hasTable('roles')) {
            Schema::dropIfExists('roles');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se recrean las tablas personalizadas de roles.
        // Si fuera necesario restaurarlas, se deberían usar las migraciones originales.
    }
};

