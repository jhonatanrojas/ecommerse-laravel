<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'type' => fake()->randomElement([CouponType::Fixed, CouponType::Percentage]),
            'value' => fake()->randomFloat(2, 5, 50),
            'min_purchase_amount' => null,
            'max_discount_amount' => null,
            'usage_limit' => null,
            'used_count' => 0,
            'usage_limit_per_user' => null,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
            'description' => fake()->sentence(),
        ];
    }

    /**
     * Indicate that the coupon is a fixed discount.
     */
    public function fixed(float $value = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponType::Fixed,
            'value' => $value ?? fake()->randomFloat(2, 5, 50),
            'max_discount_amount' => null,
        ]);
    }

    /**
     * Indicate that the coupon is a percentage discount.
     */
    public function percentage(float $value = null, float $maxDiscount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponType::Percentage,
            'value' => $value ?? fake()->numberBetween(5, 50),
            'max_discount_amount' => $maxDiscount,
        ]);
    }

    /**
     * Indicate that the coupon is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the coupon has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the coupon has a usage limit.
     */
    public function withUsageLimit(int $limit, int $used = 0): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => $limit,
            'used_count' => $used,
        ]);
    }

    /**
     * Indicate that the coupon has a minimum purchase requirement.
     */
    public function withMinimumPurchase(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'min_purchase_amount' => $amount,
        ]);
    }
}
