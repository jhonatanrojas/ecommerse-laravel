<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'product_id' => Product::factory(),
            'image_path' => fake()->imageUrl(800, 800, 'products'),
            'thumbnail_path' => fake()->optional(0.6)->imageUrl(320, 320, 'products'),
            'alt_text' => fake()->optional(0.8)->sentence(4),
            'is_primary' => false,
            'order' => fake()->numberBetween(0, 5),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => [
            'is_primary' => true,
            'order' => 0,
        ]);
    }
}

