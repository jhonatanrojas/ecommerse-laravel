<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $header = $this->upsertMenu('header', 'Menu Principal', 'header');
        $this->syncHeaderItems($header);

        $footer = $this->upsertMenu('footer', 'Pie de Pagina', 'footer');
        $this->syncFooterItems($footer);

        $mobile = $this->upsertMenu('mobile', 'Menu Movil', 'mobile');
        $this->syncMobileItems($mobile);
    }

    private function upsertMenu(string $key, string $name, string $location): Menu
    {
        $menu = Menu::withTrashed()->updateOrCreate(
            ['key' => $key],
            [
                'name' => $name,
                'location' => $location,
                'is_active' => true,
            ]
        );

        if ($menu->trashed()) {
            $menu->restore();
        }

        return $menu;
    }

    private function upsertItem(array $match, array $values): MenuItem
    {
        $item = MenuItem::withTrashed()->updateOrCreate($match, $values);

        if ($item->trashed()) {
            $item->restore();
        }

        return $item;
    }

    private function removeStaleItems(Menu $menu, array $keepIds): void
    {
        MenuItem::where('menu_id', $menu->id)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }

    private function syncHeaderItems(Menu $menu): void
    {
        $keep = [];

        $home = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Inicio'],
            [
                'url' => '/',
                'type' => 'internal',
                'order' => 1,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $home->id;

        $marketplace = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Marketplace'],
            [
                'url' => '/marketplace',
                'type' => 'internal',
                'order' => 2,
                'depth' => 0,
                'is_active' => true,
                'badge_text' => 'Nuevo',
                'badge_color' => '#2563EB',
            ]
        );
        $keep[] = $marketplace->id;

        $products = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Tienda'],
            [
                'url' => '/products',
                'type' => 'internal',
                'order' => 3,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $products->id;

        $productChildren = [
            ['label' => 'Electronica', 'url' => '/categories/electronica', 'order' => 1],
            ['label' => 'Ropa y Moda', 'url' => '/categories/ropa-y-moda', 'order' => 2],
            ['label' => 'Hogar y Jardin', 'url' => '/categories/hogar-y-jardin', 'order' => 3],
            ['label' => 'Ofertas', 'url' => '/deals', 'order' => 4],
        ];

        foreach ($productChildren as $child) {
            $item = $this->upsertItem(
                ['menu_id' => $menu->id, 'parent_id' => $products->id, 'label' => $child['label']],
                [
                    'url' => $child['url'],
                    'type' => 'internal',
                    'order' => $child['order'],
                    'depth' => 1,
                    'is_active' => true,
                ]
            );
            $keep[] = $item->id;
        }

        $sell = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Vender'],
            [
                'url' => '/marketplace/vendors/register',
                'type' => 'internal',
                'order' => 4,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $sell->id;

        $support = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Ayuda'],
            [
                'url' => '/contact',
                'type' => 'internal',
                'order' => 5,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $support->id;

        $this->removeStaleItems($menu, $keep);
    }

    private function syncFooterItems(Menu $menu): void
    {
        $keep = [];

        $about = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Nosotros'],
            [
                'url' => '#',
                'type' => 'custom',
                'order' => 1,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $about->id;

        $aboutChildren = [
            ['label' => 'Sobre nosotros', 'url' => '/pages/about', 'order' => 1],
            ['label' => 'Marketplace', 'url' => '/marketplace', 'order' => 2],
            ['label' => 'Vender en la plataforma', 'url' => '/marketplace/vendors/register', 'order' => 3],
        ];

        foreach ($aboutChildren as $child) {
            $item = $this->upsertItem(
                ['menu_id' => $menu->id, 'parent_id' => $about->id, 'label' => $child['label']],
                [
                    'url' => $child['url'],
                    'type' => 'internal',
                    'order' => $child['order'],
                    'depth' => 1,
                    'is_active' => true,
                ]
            );
            $keep[] = $item->id;
        }

        $support = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Soporte'],
            [
                'url' => '#',
                'type' => 'custom',
                'order' => 2,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $support->id;

        $supportChildren = [
            ['label' => 'Centro de ayuda', 'url' => '/contact', 'order' => 1],
            ['label' => 'Envios', 'url' => '/shipping', 'order' => 2],
            ['label' => 'Devoluciones', 'url' => '/returns', 'order' => 3],
            ['label' => 'Mis pedidos', 'url' => '/customer/orders', 'order' => 4],
        ];

        foreach ($supportChildren as $child) {
            $item = $this->upsertItem(
                ['menu_id' => $menu->id, 'parent_id' => $support->id, 'label' => $child['label']],
                [
                    'url' => $child['url'],
                    'type' => 'internal',
                    'order' => $child['order'],
                    'depth' => 1,
                    'is_active' => true,
                ]
            );
            $keep[] = $item->id;
        }

        $legal = $this->upsertItem(
            ['menu_id' => $menu->id, 'parent_id' => null, 'label' => 'Legal'],
            [
                'url' => '#',
                'type' => 'custom',
                'order' => 3,
                'depth' => 0,
                'is_active' => true,
            ]
        );
        $keep[] = $legal->id;

        $legalChildren = [
            ['label' => 'Terminos y Condiciones', 'url' => '/pages/terminos-y-condiciones', 'order' => 1],
            ['label' => 'Politica de Privacidad', 'url' => '/privacy', 'order' => 2],
            ['label' => 'Cookies', 'url' => '/cookies', 'order' => 3],
        ];

        foreach ($legalChildren as $child) {
            $item = $this->upsertItem(
                ['menu_id' => $menu->id, 'parent_id' => $legal->id, 'label' => $child['label']],
                [
                    'url' => $child['url'],
                    'type' => 'internal',
                    'order' => $child['order'],
                    'depth' => 1,
                    'is_active' => true,
                ]
            );
            $keep[] = $item->id;
        }

        $this->removeStaleItems($menu, $keep);
    }

    private function syncMobileItems(Menu $menu): void
    {
        $keep = [];

        $items = [
            ['label' => 'Inicio', 'url' => '/', 'icon' => 'heroicon-home', 'order' => 1],
            ['label' => 'Marketplace', 'url' => '/marketplace', 'icon' => 'heroicon-building-storefront', 'order' => 2],
            ['label' => 'Tienda', 'url' => '/products', 'icon' => 'heroicon-shopping-bag', 'order' => 3],
            ['label' => 'Categorias', 'url' => '/categories', 'icon' => 'heroicon-squares-2x2', 'order' => 4],
            ['label' => 'Mi cuenta', 'url' => '/customer', 'icon' => 'heroicon-user', 'order' => 5],
            ['label' => 'Carrito', 'url' => '/cart', 'icon' => 'heroicon-shopping-cart', 'order' => 6],
        ];

        foreach ($items as $item) {
            $record = $this->upsertItem(
                ['menu_id' => $menu->id, 'parent_id' => null, 'label' => $item['label']],
                [
                    'url' => $item['url'],
                    'type' => 'internal',
                    'icon' => $item['icon'],
                    'order' => $item['order'],
                    'depth' => 0,
                    'is_active' => true,
                ]
            );
            $keep[] = $record->id;
        }

        $this->removeStaleItems($menu, $keep);
    }
}
