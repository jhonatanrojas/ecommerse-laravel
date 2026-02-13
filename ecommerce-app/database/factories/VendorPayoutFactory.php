<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\VendorPayout;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorPayoutFactory extends Factory
{
    protected $model = VendorPayout::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory()->approved(),
            'amount' => fake()->randomFloat(2, 20, 2000),
            'payout_method' => [
                'provider' => 'manual',
                'account' => fake()->iban(),
                'beneficiary' => fake()->name(),
            ],
            'status' => 'pending',
            'provider' => 'manual',
            'transaction_reference' => null,
            'meta' => null,
            'processed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'transaction_reference' => 'MANUAL-' . now()->format('YmdHis'),
            'processed_at' => now(),
        ]);
    }
}
