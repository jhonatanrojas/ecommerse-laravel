<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'business_name' => fake()->company(),
            'document' => 'J-' . fake()->numerify('########') . '-' . fake()->numberBetween(0, 9),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->optional()->address(),
            'status' => 'pending',
            'commission_rate' => fake()->optional(0.6)->randomFloat(2, 5, 20),
            'payout_method' => [
                'provider' => 'manual',
                'account' => fake()->iban(),
                'beneficiary' => fake()->name(),
            ],
            'payout_cycle' => fake()->randomElement(['weekly', 'monthly', 'manual']),
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function rejected(?string $reason = null): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'approved_at' => null,
            'rejection_reason' => $reason ?? 'Documentación incompleta',
        ]);
    }

    public function suspended(?string $reason = null): static
    {
        return $this->state(fn () => [
            'status' => 'suspended',
            'approved_at' => null,
            'rejection_reason' => $reason ?? 'Actividad inusual detectada',
        ]);
    }
}
