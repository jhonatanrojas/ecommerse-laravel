<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Purple'];
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        
        return [
            'uuid' => (string) Str::uuid(),
            'product_id' => Product::factory(),
            'name' => fake()->randomElement($colors) . ' - ' . fake()->randomElement($sizes),
            'sku' => strtoupper(fake()->bothify('VAR-???-####')),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(0, 50),
            'attributes' => [
                'color' => fake()->randomElement($colors),
                'size' => fake()->randomElement($sizes),
            ],
        ];
    }
}
