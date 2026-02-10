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
        Schema::create('reviews', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Keys
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // Si se elimina un pedido, la review puede permanecer

            // Campos específicos de la tabla reviews
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false)->index();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->integer('helpful_count')->default(0);

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)

            // Índices adicionales
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
