<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Header Menu
        $header = Menu::create([
            'key' => 'header',
            'name' => 'Menú Principal',
            'location' => 'header',
            'is_active' => true,
        ]);

        $this->createHeaderItems($header);

        // 2. Footer Menu
        $footer = Menu::create([
            'key' => 'footer',
            'name' => 'Pie de Página',
            'location' => 'footer',
            'is_active' => true,
        ]);

        $this->createFooterItems($footer);

        // 3. Mobile Menu
        $mobile = Menu::create([
            'key' => 'mobile',
            'name' => 'Menú Móvil',
            'location' => 'mobile',
            'is_active' => true,
        ]);

        $this->createMobileItems($mobile);
    }

    private function createHeaderItems(Menu $menu)
    {
        // Inicio
        MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Inicio',
            'url' => '/',
            'type' => 'internal',
            'order' => 1,
        ]);

        // Productos
        $products = MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Productos',
            'url' => '/products',
            'type' => 'internal',
            'order' => 2,
        ]);

        // Subitems Productos
        $subItems = [
            ['label' => 'Electrónicos', 'url' => '/category/electronics', 'order' => 1],
            ['label' => 'Ropa', 'url' => '/category/clothing', 'order' => 2],
            ['label' => 'Hogar', 'url' => '/category/home', 'order' => 3],
        ];

        foreach ($subItems as $item) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $products->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'type' => 'internal',
                'order' => $item['order'],
                'depth' => 1,
            ]);
        }

        // Ofertas
        MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Ofertas',
            'url' => '/deals',
            'type' => 'internal',
            'order' => 3,
            'badge_text' => 'Hot',
            'badge_color' => 'red',
        ]);

        // Contacto
        MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Contacto',
            'url' => '/contact',
            'type' => 'internal',
            'order' => 4,
        ]);
    }

    private function createFooterItems(Menu $menu)
    {
        // Información
        $info = MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Información',
            'url' => '#',
            'type' => 'custom',
            'order' => 1,
        ]);

        $infoItems = [
            ['label' => 'Acerca de', 'url' => '/about'],
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Testimonios', 'url' => '/testimonials'],
        ];

        foreach ($infoItems as $index => $item) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $info->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'type' => 'internal',
                'order' => $index + 1,
                'depth' => 1,
            ]);
        }

        // Servicio al Cliente
        $service = MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Servicio al Cliente',
            'url' => '#',
            'type' => 'custom',
            'order' => 2,
        ]);

        $serviceItems = [
            ['label' => 'Contacto', 'url' => '/contact'],
            ['label' => 'FAQ', 'url' => '/faq'],
            ['label' => 'Envíos', 'url' => '/shipping'],
            ['label' => 'Devoluciones', 'url' => '/returns'],
        ];

        foreach ($serviceItems as $index => $item) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $service->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'type' => 'internal',
                'order' => $index + 1,
                'depth' => 1,
            ]);
        }

        // Legal
        $legal = MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Legal',
            'url' => '#',
            'type' => 'custom',
            'order' => 3,
        ]);

        $legalItems = [
            ['label' => 'Términos y Condiciones', 'url' => '/terms'],
            ['label' => 'Política de Privacidad', 'url' => '/privacy'],
            ['label' => 'Cookies', 'url' => '/cookies'],
        ];

        foreach ($legalItems as $index => $item) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $legal->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'type' => 'internal',
                'order' => $index + 1,
                'depth' => 1,
            ]);
        }
    }

    private function createMobileItems(Menu $menu)
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/', 'icon' => 'heroicon-home'],
            ['label' => 'Categorías', 'url' => '/categories', 'icon' => 'heroicon-squares-2x2'],
            ['label' => 'Carrito', 'url' => '/cart', 'icon' => 'heroicon-shopping-cart'],
            ['label' => 'Perfil', 'url' => '/profile', 'icon' => 'heroicon-user'],
        ];

        foreach ($items as $index => $item) {
            MenuItem::create([
                'menu_id' => $menu->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'type' => 'internal',
                'icon' => $item['icon'],
                'order' => $index + 1,
            ]);
        }
    }
}
