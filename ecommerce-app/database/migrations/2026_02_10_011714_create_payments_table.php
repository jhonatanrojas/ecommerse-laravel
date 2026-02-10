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
        Schema::create('payments', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Key a orders
            $table->foreignId('order_id')->constrained()->onDelete('restrict'); // No eliminar pedido si tiene pagos

            // Campos específicos de la tabla payments
            $table->string('payment_method');
            $table->string('transaction_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('pending')->index();
            $table->json('gateway_response')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('refund_date')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();

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
        Schema::dropIfExists('payments');
    }
};
