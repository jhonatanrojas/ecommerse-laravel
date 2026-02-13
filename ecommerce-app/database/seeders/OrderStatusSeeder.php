<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pendiente',
                'slug' => 'pending',
                'color' => '#F59E0B',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Procesando',
                'slug' => 'processing',
                'color' => '#3B82F6',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Enviado',
                'slug' => 'shipped',
                'color' => '#8B5CF6',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Entregado',
                'slug' => 'delivered',
                'color' => '#10B981',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Cancelado',
                'slug' => 'cancelled',
                'color' => '#EF4444',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Reembolsado',
                'slug' => 'refunded',
                'color' => '#EC4899',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Fallido',
                'slug' => 'failed',
                'color' => '#DC2626',
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}
