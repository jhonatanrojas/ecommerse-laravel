<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Producto Ejemplo 1',
                'description' => 'Descripción del producto de ejemplo 1',
                'price' => 29.99,
                'stock' => 100,
                'sku' => 'PROD-001',
                'is_active' => true,
            ],
            [
                'name' => 'Producto Ejemplo 2',
                'description' => 'Descripción del producto de ejemplo 2',
                'price' => 49.99,
                'stock' => 50,
                'sku' => 'PROD-002',
                'is_active' => true,
            ],
            [
                'name' => 'Producto Ejemplo 3',
                'description' => 'Descripción del producto de ejemplo 3',
                'price' => 19.99,
                'stock' => 200,
                'sku' => 'PROD-003',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
