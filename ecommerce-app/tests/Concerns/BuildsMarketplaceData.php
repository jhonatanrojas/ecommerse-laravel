<?php

namespace Tests\Concerns;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Support\Str;

trait BuildsMarketplaceData
{
    protected function seedMarketplaceSettings(array $overrides = []): StoreSetting
    {
        return StoreSetting::query()->updateOrCreate(
            ['id' => 1],
            array_merge([
                'store_name' => 'Test Store',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'tax_rate' => 0,
                'marketplace_commission_rate' => 10,
                'allow_guest_checkout' => false,
                'auto_approve_vendors' => false,
                'auto_approve_vendor_products' => false,
                'enable_automatic_payouts' => false,
            ], $overrides)
        );
    }

    protected function createVendor(string $status = 'approved', array $vendorOverrides = [], array $userOverrides = []): Vendor
    {
        $user = User::factory()->create(array_merge([
            'email_verified_at' => now(),
        ], $userOverrides));

        return Vendor::factory()->create(array_merge([
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => $status,
            'approved_at' => $status === 'approved' ? now() : null,
        ], $vendorOverrides));
    }

    protected function createMarketplaceProduct(Vendor $vendor, array $productOverrides = [], array $vendorProductOverrides = []): Product
    {
        $category = Category::factory()->active()->create();

        $product = Product::factory()->active()->withCategory($category->id)->create($productOverrides);

        VendorProduct::factory()->create(array_merge([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
        ], $vendorProductOverrides));

        return $product;
    }

    protected function createOrder(?User $user = null, array $overrides = []): Order
    {
        $user = $user ?: User::factory()->create();
        $shipping = Address::factory()->shipping()->create(['user_id' => $user->id]);
        $billing = Address::factory()->billing()->create(['user_id' => $user->id]);

        return Order::query()->create(array_merge([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 0,
            'shipping_method' => 'standard',
            'shipping_address_id' => $shipping->id,
            'billing_address_id' => $billing->id,
        ], $overrides));
    }

    protected function createOrderItem(Order $order, Product $product, int $quantity = 1, ?int $vendorId = null, ?float $price = null): OrderItem
    {
        $price = $price ?? (float) $product->price;
        $subtotal = round($price * $quantity, 2);

        $item = OrderItem::query()->create([
            'order_id' => $order->id,
            'vendor_id' => $vendorId,
            'product_id' => $product->id,
            'product_variant_id' => null,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ]);

        $order->update([
            'subtotal' => (float) $order->subtotal + $subtotal,
            'total' => (float) $order->total + $subtotal,
        ]);

        return $item;
    }
}
