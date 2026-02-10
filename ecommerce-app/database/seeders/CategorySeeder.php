<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorías predefinidas
        $categories = [
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Electrónica',
                'slug' => 'electronica',
                'description' => 'Productos electrónicos y tecnología',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=800&h=600&fit=crop',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Ropa y Moda',
                'slug' => 'ropa-y-moda',
                'description' => 'Prendas de vestir y accesorios de moda',
                'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800&h=600&fit=crop',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Hogar y Jardín',
                'slug' => 'hogar-y-jardin',
                'description' => 'Artículos para el hogar y jardinería',
                'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?w=800&h=600&fit=crop',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Deportes',
                'slug' => 'deportes',
                'description' => 'Equipamiento y ropa deportiva',
                'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800&h=600&fit=crop',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Libros',
                'slug' => 'libros',
                'description' => 'Libros físicos y digitales',
                'image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=800&h=600&fit=crop',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Juguetes',
                'slug' => 'juguetes',
                'description' => 'Juguetes para todas las edades',
                'image' => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&h=600&fit=crop',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Belleza y Cuidado Personal',
                'slug' => 'belleza-y-cuidado-personal',
                'description' => 'Productos de belleza y cuidado personal',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=600&fit=crop',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Alimentos y Bebidas',
                'slug' => 'alimentos-y-bebidas',
                'description' => 'Productos alimenticios y bebidas',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&h=600&fit=crop',
                'order' => 8,
                'is_active' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Crear categorías adicionales aleatorias
        Category::factory()->count(12)->create();
    }
}
