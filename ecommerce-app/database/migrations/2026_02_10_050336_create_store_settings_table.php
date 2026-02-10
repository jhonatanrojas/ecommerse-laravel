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
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Mi Tienda');
            $table->string('logo')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('currency_symbol', 5)->default('$');
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->string('support_email')->nullable();
            $table->string('transactional_email')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->timestamps();
        });

        // Insertar configuración por defecto
        DB::table('store_settings')->insert([
            'store_name' => 'Mi Tienda',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => 0.00,
            'maintenance_mode' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
