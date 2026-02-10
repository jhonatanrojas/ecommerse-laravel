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
        Schema::create('product_images', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Key a products
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Campos específicos de la tabla product_images
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);

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
        Schema::dropIfExists('product_images');
    }
};
