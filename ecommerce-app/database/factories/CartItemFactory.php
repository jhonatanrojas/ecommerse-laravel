<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CartItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        
        return [
            'cart_id' => Cart::factory()->guest(),
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => fake()->numberBetween(1, 5),
            'price' => $product->price,
        ];
    }

    /**
     * Indicate that the cart item has a variant.
     */
    public function withVariant(): static
    {
        return $this->state(function (array $attributes) {
            $product = Product::factory()->create();
            $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
            
            return [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'price' => $variant->price,
            ];
        });
    }
}
