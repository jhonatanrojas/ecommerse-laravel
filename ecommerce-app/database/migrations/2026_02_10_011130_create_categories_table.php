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
        Schema::create('categories', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Campos específicos de la tabla categories
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete(); // Self-reference
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)

            // Auditoría completa (created_by, updated_by, deleted_by)
            $table->foreignUuid('created_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();

            // Índices adicionales
            $table->index('parent_id');
            $table->index('slug');
            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
