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
        Schema::create('orders', function (Blueprint $table) {
            // 1. Estrategia Híbrida: ID Incremental + UUID Público
            $table->id(); // Primary key auto-incremental
            $table->uuid('uuid')->unique()->index(); // Identificador público único

            // Foreign Key a users
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // No eliminar usuario si tiene pedidos

            // Campos específicos de la tabla orders
            $table->string('order_number')->unique(); // ORD-00000001
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending')->index();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->index();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);

            $table->string('coupon_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('shipping_method')->nullable();

            // Foreign Keys a addresses
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->text('customer_notes')->nullable();

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // 2. Campos Auditables Obligatorios
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (recuperabilidad)

            // Auditoría completa (created_by, updated_by, deleted_by)
            $table->foreignUuid('created_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users', 'uuid')->nullOnDelete();

            // Índices adicionales
            $table->index('order_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
