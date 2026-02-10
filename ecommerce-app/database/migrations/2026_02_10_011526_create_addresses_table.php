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
        Schema::create('addresses', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Key a users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Campos específicos de la tabla addresses
            $table->enum('type', ['shipping', 'billing']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->boolean('is_default')->default(false);

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)

            // Índices adicionales
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
