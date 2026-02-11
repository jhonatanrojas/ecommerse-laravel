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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Foreign key a users (relación 1:1)
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade');

            // Información adicional del cliente
            $table->string('phone')->nullable();
            $table->string('document')->nullable();
            $table->date('birthdate')->nullable();

            // Foreign keys a addresses para direcciones por defecto
            $table->foreignId('default_shipping_address_id')
                ->nullable()
                ->constrained('addresses')
                ->onDelete('set null');

            $table->foreignId('default_billing_address_id')
                ->nullable()
                ->constrained('addresses')
                ->onDelete('set null');

            $table->timestamps();

            // Índices adicionales para optimizar consultas
            $table->index('user_id');
            $table->index('default_shipping_address_id');
            $table->index('default_billing_address_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
