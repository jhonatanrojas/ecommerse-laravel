<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    private function upsertSection(string $type, array $attributes): HomeSection
    {
        $section = HomeSection::withTrashed()->updateOrCreate(
            ['type' => $type],
            $attributes
        );

        if ($section->trashed()) {
            $section->restore();
        }

        return $section;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Hero Section
        $this->upsertSection('hero', [
            'title' => 'Hero Principal',
            'is_active' => true,
            'display_order' => 1,
            'configuration' => [
                'title' => 'Bienvenido a Nuestra Tienda',
                'subtitle' => 'Descubre los mejores productos al mejor precio',
                'background_image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920&h=600&fit=crop',
                'background_video' => null,
                'overlay_opacity' => 0.5,
                'cta_buttons' => [
                    [
                        'text' => 'Comprar Ahora',
                        'url' => '/products',
                        'style' => 'primary'
                    ],
                    [
                        'text' => 'Ver Más',
                        'url' => '/about',
                        'style' => 'secondary'
                    ]
                ]
            ]
        ]);

        // 2. Featured Products Section
        $featuredProductsSection = $this->upsertSection('featured_products', [
            'title' => 'Productos Destacados',
            'is_active' => true,
            'display_order' => 2,
            'configuration' => [
                'heading' => 'Productos Destacados',
                'subheading' => 'Los mejores productos seleccionados para ti',
                'limit' => 8,
                'layout' => 'grid',
                'columns' => 4,
                'show_price' => true,
                'show_rating' => true,
                'view_all' => [
                    'enabled' => true,
                    'label' => 'Ver todos',
                    'url' => '/products',
                ],
            ]
        ]);

        // Reset section items to avoid duplicates on reseed
        $featuredProductsSection->items()->delete();

        // Add featured products if they exist
        $products = Product::where('is_featured', true)->take(8)->get();
        foreach ($products as $index => $product) {
            HomeSectionItem::create([
                'home_section_id' => $featuredProductsSection->id,
                'itemable_type' => Product::class,
                'itemable_id' => $product->id,
                'display_order' => $index,
            ]);
        }

        // 3. Featured Categories Section
        $featuredCategoriesSection = $this->upsertSection('featured_categories', [
            'title' => 'Categorías Destacadas',
            'is_active' => true,
            'display_order' => 3,
            'configuration' => [
                'heading' => 'Explora Nuestras Categorías',
                'subheading' => 'Encuentra lo que buscas',
                'limit' => 6,
                'layout' => 'grid',
                'columns' => 3,
                'show_product_count' => true,
                'view_all' => [
                    'enabled' => true,
                    'label' => 'Ver todas',
                    'url' => '/categories',
                ],
            ]
        ]);

        // Reset section items to avoid duplicates on reseed
        $featuredCategoriesSection->items()->delete();

        // Add featured categories if they exist
        $categories = Category::take(6)->get();
        foreach ($categories as $index => $category) {
            HomeSectionItem::create([
                'home_section_id' => $featuredCategoriesSection->id,
                'itemable_type' => Category::class,
                'itemable_id' => $category->id,
                'display_order' => $index,
            ]);
        }

        // 4. Promo Banners Section
        $this->upsertSection('banners', [
            'title' => 'Banners Promocionales',
            'is_active' => true,
            'display_order' => 4,
            'configuration' => [
                'layout' => 'slider',
                'autoplay' => true,
                'autoplay_speed' => 5000,
                'banners' => [
                    [
                        'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&h=400&fit=crop',
                        'title' => 'Oferta Especial',
                        'subtitle' => 'Hasta 50% de descuento',
                        'link' => '/sale',
                        'button_text' => 'Ver Ofertas'
                    ],
                    [
                        'image' => 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=1200&h=400&fit=crop',
                        'title' => 'Nueva Colección',
                        'subtitle' => 'Descubre las últimas tendencias',
                        'link' => '/new-arrivals',
                        'button_text' => 'Explorar'
                    ],
                    [
                        'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&h=400&fit=crop',
                        'title' => 'Envío Gratis',
                        'subtitle' => 'En compras mayores a $50',
                        'link' => '/shipping',
                        'button_text' => 'Más Información'
                    ]
                ]
            ]
        ]);

        // 5. Testimonials Section
        $this->upsertSection('testimonials', [
            'title' => 'Testimonios',
            'is_active' => true,
            'display_order' => 5,
            'configuration' => [
                'heading' => 'Lo Que Dicen Nuestros Clientes',
                'layout' => 'carousel',
                'show_rating' => true,
                'show_avatar' => true,
                'testimonials' => [
                    [
                        'name' => 'María García',
                        'avatar' => 'https://ui-avatars.com/api/?name=Maria+Garcia&background=4F46E5&color=fff',
                        'rating' => 5,
                        'text' => 'Excelente servicio y productos de calidad. Muy recomendado!',
                        'date' => '2024-01-15'
                    ],
                    [
                        'name' => 'Juan Pérez',
                        'avatar' => 'https://ui-avatars.com/api/?name=Juan+Perez&background=10B981&color=fff',
                        'rating' => 5,
                        'text' => 'Rápida entrega y atención al cliente excepcional.',
                        'date' => '2024-01-20'
                    ],
                    [
                        'name' => 'Ana Martínez',
                        'avatar' => 'https://ui-avatars.com/api/?name=Ana+Martinez&background=F59E0B&color=fff',
                        'rating' => 4,
                        'text' => 'Gran variedad de productos y precios competitivos.',
                        'date' => '2024-02-01'
                    ]
                ]
            ]
        ]);

        // 6. Custom HTML Block Section (Inactive by default)
        $this->upsertSection('html_block', [
            'title' => 'Bloque HTML Personalizado',
            'is_active' => false,
            'display_order' => 6,
            'configuration' => [
                'html_content' => '<div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-8 rounded-lg text-center">
                    <h2 class="text-3xl font-bold mb-4">¡Suscríbete a Nuestro Newsletter!</h2>
                    <p class="mb-6">Recibe ofertas exclusivas y novedades directamente en tu correo</p>
                    <form class="flex gap-2 max-w-md mx-auto">
                        <input type="email" placeholder="Tu correo electrónico" class="flex-1 px-4 py-2 rounded text-gray-900" />
                        <button type="submit" class="bg-white text-purple-600 px-6 py-2 rounded font-semibold hover:bg-gray-100">
                            Suscribirse
                        </button>
                    </form>
                </div>',
                'css_classes' => 'my-8 container mx-auto'
            ]
        ]);
    }
}
