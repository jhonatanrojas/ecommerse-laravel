<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorOrderFactory extends Factory
{
    protected $model = VendorOrder::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        $shippingAddress = Address::factory()->shipping()->create(['user_id' => $user->id]);
        $billingAddress = Address::factory()->billing()->create(['user_id' => $user->id]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'subtotal' => 100,
            'discount' => 0,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 100,
            'shipping_method' => 'standard',
            'shipping_address_id' => $shippingAddress->id,
            'billing_address_id' => $billingAddress->id,
        ]);

        return [
            'vendor_id' => Vendor::factory()->approved(),
            'order_id' => $order->id,
            'subtotal' => fake()->randomFloat(2, 50, 1000),
            'commission_amount' => fake()->randomFloat(2, 2, 150),
            'vendor_earnings' => fake()->randomFloat(2, 20, 900),
            'payout_status' => 'pending',
            'shipping_status' => 'pending',
            'shipping_method' => 'standard',
            'tracking_number' => null,
            'shipped_at' => null,
            'delivered_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['payout_status' => 'paid']);
    }
}
