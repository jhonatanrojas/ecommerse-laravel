<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'session_id' => null,
            'coupon_code' => null,
            'discount_amount' => 0,
            'expires_at' => null,
        ];
    }

    /**
     * Indicate that the cart belongs to an authenticated user.
     */
    public function forUser(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user?->id ?? User::factory()->create()->id,
            'session_id' => null,
            'expires_at' => null,
        ]);
    }

    /**
     * Indicate that the cart is for a guest user.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => Str::random(40),
            'expires_at' => now()->addDays(30),
        ]);
    }

    /**
     * Indicate that the cart has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the cart has a coupon applied.
     */
    public function withCoupon(string $couponCode, float $discountAmount): static
    {
        return $this->state(fn (array $attributes) => [
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmount,
        ]);
    }
}
