<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
        });

        DB::table('shipping_statuses')->insert([
            [
                'name' => 'Pendiente de Envío',
                'slug' => 'pending_shipment',
                'color' => '#F59E0B',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Preparando',
                'slug' => 'preparing',
                'color' => '#3B82F6',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enviado',
                'slug' => 'shipped',
                'color' => '#8B5CF6',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En Tránsito',
                'slug' => 'in_transit',
                'color' => '#06B6D4',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Entregado',
                'slug' => 'delivered',
                'color' => '#10B981',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Devuelto',
                'slug' => 'returned',
                'color' => '#F97316',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cancelado',
                'slug' => 'cancelled',
                'color' => '#EF4444',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_status_id')
                ->nullable()
                ->after('order_status_id')
                ->constrained('shipping_statuses')
                ->restrictOnDelete();
        });

        // Asignar el estatus por defecto a todas las órdenes existentes
        $defaultStatusId = DB::table('shipping_statuses')->where('is_default', true)->value('id');
        
        if ($defaultStatusId) {
            DB::table('orders')->update(['shipping_status_id' => $defaultStatusId]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_status_id');
        });

        Schema::dropIfExists('shipping_statuses');
    }
};
