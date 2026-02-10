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
        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id(); // Primary key auto-incremental

            // Foreign Keys
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');

            // Campos específicos de la tabla coupon_user
            $table->timestamp('used_at')->nullable();

            // Campos Auditables Obligatorios (sin softDeletes)
            $table->timestamps(); // created_at, updated_at

            // Índices
            $table->unique(['coupon_id', 'user_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_user');
    }
};
