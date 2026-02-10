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
        Schema::create('coupons', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Campos específicos de la tabla coupons
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('value', 10, 2); // Valor del descuento

            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();

            $table->integer('usage_limit')->nullable(); // Límite total de usos
            $table->integer('used_count')->default(0);
            $table->integer('usage_limit_per_user')->nullable(); // Límite de usos por usuario

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();

            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)

            // Auditoría completa (created_by, updated_by, deleted_by)
            $table->foreignUuid('created_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
