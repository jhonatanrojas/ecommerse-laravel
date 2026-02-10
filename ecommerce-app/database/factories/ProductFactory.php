<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(rand(2, 4), true);
        $price = fake()->randomFloat(2, 10, 1000);
        $hasDiscount = fake()->boolean(30);
        
        return [
            'uuid' => (string) Str::uuid(),
            'category_id' => Category::inRandomOrder()->first()?->id,
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->bothify('???-####')),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(15),
            'price' => $price,
            'compare_price' => $hasDiscount ? $price * fake()->randomFloat(2, 1.1, 1.5) : null,
            'cost' => $price * fake()->randomFloat(2, 0.4, 0.7),
            'stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 10,
            'weight' => fake()->randomFloat(2, 100, 5000),
            'dimensions' => [
                'length' => fake()->randomFloat(2, 10, 100),
                'width' => fake()->randomFloat(2, 10, 100),
                'height' => fake()->randomFloat(2, 5, 50),
            ],
            'is_active' => fake()->boolean(85),
            'is_featured' => fake()->boolean(20),
            'meta_title' => ucfirst($name),
            'meta_description' => fake()->sentence(20),
            'meta_keywords' => implode(', ', fake()->words(5)),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_active' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 5),
            'low_stock_threshold' => 10,
        ]);
    }

    public function withCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }
}
