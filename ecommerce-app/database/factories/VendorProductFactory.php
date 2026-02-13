<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorProductFactory extends Factory
{
    protected $model = VendorProduct::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'vendor_id' => Vendor::factory()->approved(),
            'product_id' => Product::factory(),
            'is_active' => true,
            'is_approved' => false,
            'moderation_notes' => null,
            'moderation_history' => [],
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
