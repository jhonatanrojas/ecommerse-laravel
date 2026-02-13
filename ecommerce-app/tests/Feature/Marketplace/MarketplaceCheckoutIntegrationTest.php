<?php

namespace Tests\Feature\Marketplace;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\VendorOrder;
use App\Services\Cart\CheckoutService;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class MarketplaceCheckoutIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_checkout_generates_vendor_orders_for_multiple_vendors(): void
    {
        $this->seedMarketplaceSettings(['marketplace_commission_rate' => 10]);

        $vendorA = $this->createVendor('approved', ['commission_rate' => 10]);
        $vendorB = $this->createVendor('approved', ['commission_rate' => 20]);

        $productA = $this->createMarketplaceProduct($vendorA, ['price' => 100]);
        $productB = $this->createMarketplaceProduct($vendorB, ['price' => 50]);

        $user = User::factory()->create();
        $shipping = Address::factory()->shipping()->create(['user_id' => $user->id]);
        $billing = Address::factory()->billing()->create(['user_id' => $user->id]);

        $cart = Cart::factory()->forUser($user)->create();

        CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_id' => $productA->id,
            'product_variant_id' => null,
            'quantity' => 1,
            'price' => 100,
        ]);

        CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_id' => $productB->id,
            'product_variant_id' => null,
            'quantity' => 2,
            'price' => 50,
        ]);

        $order = app(CheckoutService::class)->processCheckout($cart->fresh(), new CheckoutData(
            shippingAddressId: $shipping->id,
            billingAddressId: $billing->id,
            paymentMethod: 'cash',
            shippingMethod: 'standard',
            customerNotes: null,
        ));

        $vendorOrders = VendorOrder::query()->where('order_id', $order->id)->get()->keyBy('vendor_id');

        $this->assertCount(2, $vendorOrders);

        $this->assertSame('100.00', $vendorOrders[$vendorA->id]->subtotal);
        $this->assertSame('10.00', $vendorOrders[$vendorA->id]->commission_amount);
        $this->assertSame('90.00', $vendorOrders[$vendorA->id]->vendor_earnings);

        $this->assertSame('100.00', $vendorOrders[$vendorB->id]->subtotal);
        $this->assertSame('20.00', $vendorOrders[$vendorB->id]->commission_amount);
        $this->assertSame('80.00', $vendorOrders[$vendorB->id]->vendor_earnings);
    }
}
