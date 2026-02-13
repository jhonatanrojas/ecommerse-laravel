<?php

namespace Database\Seeders;

use App\Models\ShippingStatus;
use Illuminate\Database\Seeder;

class ShippingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pendiente de Envío',
                'slug' => 'pending_shipment',
                'color' => '#F59E0B',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Preparando',
                'slug' => 'preparing',
                'color' => '#3B82F6',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enviado',
                'slug' => 'shipped',
                'color' => '#8B5CF6',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'En Tránsito',
                'slug' => 'in_transit',
                'color' => '#06B6D4',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Entregado',
                'slug' => 'delivered',
                'color' => '#10B981',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Devuelto',
                'slug' => 'returned',
                'color' => '#F97316',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Cancelado',
                'slug' => 'cancelled',
                'color' => '#EF4444',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 7,
            ],
        ];

        foreach ($statuses as $status) {
            ShippingStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}
