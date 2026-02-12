<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class EnsureTiendaMenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $header = Menu::query()->where('location', 'header')->first();

        if (! $header) {
            return;
        }

        $item = MenuItem::query()
            ->where('menu_id', $header->id)
            ->where(function ($query) {
                $query->where('url', '/products')
                    ->orWhere('label', 'Productos')
                    ->orWhere('label', 'Tienda');
            })
            ->orderBy('order')
            ->first();

        if ($item) {
            $item->update([
                'label' => 'Tienda',
                'url' => '/products',
                'type' => 'internal',
            ]);

            return;
        }

        MenuItem::create([
            'menu_id' => $header->id,
            'label' => 'Tienda',
            'url' => '/products',
            'type' => 'internal',
            'order' => 2,
            'is_active' => true,
        ]);
    }
}

