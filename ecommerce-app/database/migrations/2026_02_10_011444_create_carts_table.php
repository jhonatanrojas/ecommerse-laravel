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
        Schema::create('carts', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Key a users
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Campos específicos de la tabla carts
            $table->string('session_id')->nullable()->index(); // Para carritos de invitados
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->timestamp('expires_at')->nullable();

            // 2. Campos Auditables Obligatorios (sin softDeletes)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
