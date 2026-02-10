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
        Schema::create('products', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Key a categories
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            // Campos específicos de la tabla products
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->decimal('weight', 8, 2)->nullable(); // en gramos
            $table->json('dimensions')->nullable(); // largo, ancho, alto
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)

            // Auditoría completa (created_by, updated_by, deleted_by)
            $table->foreignUuid('created_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();

            // Índices adicionales
            $table->index('slug');
            $table->index('sku');
            $table->index(['is_active', 'is_featured']);
            $table->index('stock');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
