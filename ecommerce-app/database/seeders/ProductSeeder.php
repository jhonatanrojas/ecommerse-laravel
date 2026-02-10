<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que existan categorías
        if (Category::count() === 0) {
            $this->command->warn('No hay categorías. Ejecuta CategorySeeder primero.');
            return;
        }

        // Productos predefinidos
        $products = [
            [
                'uuid' => (string) Str::uuid(),
                'category_id' => Category::where('slug', 'electronica')->first()?->id,
                'name' => 'Laptop HP Pavilion 15',
                'slug' => 'laptop-hp-pavilion-15',
                'sku' => 'LAP-HP-001',
                'description' => 'Laptop HP Pavilion 15 con procesador Intel Core i5, 8GB RAM, 256GB SSD. Perfecta para trabajo y entretenimiento.',
                'short_description' => 'Laptop HP con Intel i5, 8GB RAM, 256GB SSD',
                'price' => 699.99,
                'compare_price' => 899.99,
                'cost' => 450.00,
                'stock' => 15,
                'low_stock_threshold' => 5,
                'weight' => 1800,
                'dimensions' => ['length' => 36, 'width' => 24, 'height' => 2],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'category_id' => Category::where('slug', 'electronica')->first()?->id,
                'name' => 'Mouse Inalámbrico Logitech',
                'slug' => 'mouse-inalambrico-logitech',
                'sku' => 'MOU-LOG-001',
                'description' => 'Mouse inalámbrico ergonómico con sensor óptico de alta precisión. Batería de larga duración.',
                'short_description' => 'Mouse inalámbrico ergonómico Logitech',
                'price' => 29.99,
                'compare_price' => null,
                'cost' => 15.00,
                'stock' => 50,
                'low_stock_threshold' => 10,
                'weight' => 100,
                'dimensions' => ['length' => 12, 'width' => 7, 'height' => 4],
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'category_id' => Category::where('slug', 'ropa-y-moda')->first()?->id,
                'name' => 'Camiseta Básica Algodón',
                'slug' => 'camiseta-basica-algodon',
                'sku' => 'CAM-BAS-001',
                'description' => 'Camiseta básica de algodón 100%, disponible en varios colores. Cómoda y versátil.',
                'short_description' => 'Camiseta básica de algodón 100%',
                'price' => 19.99,
                'compare_price' => 24.99,
                'cost' => 8.00,
                'stock' => 100,
                'low_stock_threshold' => 20,
                'weight' => 200,
                'dimensions' => ['length' => 70, 'width' => 50, 'height' => 1],
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'category_id' => Category::where('slug', 'deportes')->first()?->id,
                'name' => 'Balón de Fútbol Profesional',
                'slug' => 'balon-futbol-profesional',
                'sku' => 'BAL-FUT-001',
                'description' => 'Balón de fútbol profesional tamaño 5, aprobado por FIFA. Material sintético de alta calidad.',
                'short_description' => 'Balón de fútbol profesional tamaño 5',
                'price' => 49.99,
                'compare_price' => null,
                'cost' => 25.00,
                'stock' => 30,
                'low_stock_threshold' => 10,
                'weight' => 450,
                'dimensions' => ['length' => 22, 'width' => 22, 'height' => 22],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'category_id' => Category::where('slug', 'libros')->first()?->id,
                'name' => 'Clean Code - Robert C. Martin',
                'slug' => 'clean-code-robert-martin',
                'sku' => 'LIB-CLN-001',
                'description' => 'Libro esencial sobre buenas prácticas de programación y código limpio. Imprescindible para desarrolladores.',
                'short_description' => 'Guía de buenas prácticas de programación',
                'price' => 39.99,
                'compare_price' => 49.99,
                'cost' => 20.00,
                'stock' => 25,
                'low_stock_threshold' => 5,
                'weight' => 600,
                'dimensions' => ['length' => 23, 'width' => 15, 'height' => 3],
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Agregar imágenes a cada producto
            $this->addProductImages($product);
        }

        // Crear productos aleatorios adicionales
        $randomProducts = Product::factory()->count(45)->create();
        foreach ($randomProducts as $product) {
            $this->addProductImages($product);
        }

        // Crear algunos productos destacados
        $featuredProducts = Product::factory()->count(5)->featured()->create();
        foreach ($featuredProducts as $product) {
            $this->addProductImages($product);
        }

        // Crear algunos productos con bajo stock
        Product::factory()->count(5)->lowStock()->create();

        // Crear algunos productos sin stock
        Product::factory()->count(3)->outOfStock()->create();

        $this->command->info('Productos creados exitosamente.');
    }
    
    /**
     * Add images to a product based on its category
     */
    private function addProductImages(Product $product): void
    {
        $categorySlug = $product->category?->slug ?? 'general';
        
        // Mapeo de categorías a imágenes de Unsplash
        $imagesByCategory = [
            'electronica' => [
                'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=800&h=800&fit=crop',
            ],
            'ropa-y-moda' => [
                'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=800&h=800&fit=crop',
            ],
            'hogar-y-jardin' => [
                'https://images.unsplash.com/photo-1484101403633-562f891dc89a?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&h=800&fit=crop',
            ],
            'deportes' => [
                'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=800&h=800&fit=crop',
            ],
            'libros' => [
                'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=800&h=800&fit=crop',
            ],
            'juguetes' => [
                'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1587912781766-c2f0c8e0b0f7?w=800&h=800&fit=crop',
            ],
            'belleza-y-cuidado-personal' => [
                'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&h=800&fit=crop',
            ],
            'general' => [
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&h=800&fit=crop',
                'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=800&h=800&fit=crop',
            ],
        ];
        
        // Obtener imágenes para la categoría o usar general como fallback
        $images = $imagesByCategory[$categorySlug] ?? $imagesByCategory['general'];
        
        // Seleccionar una imagen aleatoria
        $imageUrl = $images[array_rand($images)];
        
        // Crear el registro de imagen del producto
        ProductImage::create([
            'uuid' => (string) Str::uuid(),
            'product_id' => $product->id,
            'image_path' => $imageUrl, // Guardamos la URL directamente
            'thumbnail_path' => $imageUrl,
            'alt_text' => $product->name,
            'is_primary' => true,
            'order' => 0,
        ]);
    }
}
