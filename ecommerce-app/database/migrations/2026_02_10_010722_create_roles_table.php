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
        Schema::create('roles', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Campos específicos de la tabla roles
            $table->string('name')->unique(); // admin, customer
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
