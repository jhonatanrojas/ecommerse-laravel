<?php

namespace Tests\Feature\Marketplace;

use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\VendorOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class VendorOrderAndShippingAccessTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_vendor_can_only_view_own_vendor_orders(): void
    {
        $this->seedMarketplaceSettings();

        $vendorA = $this->createVendor('approved');
        $vendorB = $this->createVendor('approved');

        $orderA = $this->createOrder();
        $orderB = $this->createOrder();

        $vendorOrderA = VendorOrder::factory()->create(['vendor_id' => $vendorA->id, 'order_id' => $orderA->id]);
        $vendorOrderB = VendorOrder::factory()->create(['vendor_id' => $vendorB->id, 'order_id' => $orderB->id]);

        $this->actingAs($vendorA->user, 'vendor');

        $this->get("/vendor/orders/{$vendorOrderA->id}")->assertOk();
        $this->get("/vendor/orders/{$vendorOrderB->id}")->assertForbidden();
    }

    public function test_vendor_can_update_shipping_status_with_valid_values(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');
        $order = $this->createOrder();
        $vendorOrder = VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $order->id,
        ]);

        $this->actingAs($vendor->user, 'vendor');

        $this->patch("/vendor/orders/{$vendorOrder->id}/shipping", [
            'shipping_status' => 'shipped',
            'shipping_method' => 'express',
            'tracking_number' => 'TRACK-123',
        ])->assertRedirect();

        $fresh = $vendorOrder->fresh();
        $this->assertSame('shipped', $fresh->shipping_status);
        $this->assertSame('express', $fresh->shipping_method);
        $this->assertSame('TRACK-123', $fresh->tracking_number);
        $this->assertNotNull($fresh->shipped_at);
    }

    public function test_shipping_status_update_rejects_invalid_status(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');
        $vendorOrder = VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $this->createOrder()->id,
        ]);

        $this->actingAs($vendor->user, 'vendor');

        $this->from('/vendor/orders')->patch("/vendor/orders/{$vendorOrder->id}/shipping", [
            'shipping_status' => 'invalid_status',
        ])->assertRedirect('/vendor/orders')
            ->assertSessionHasErrors(['shipping_status']);
    }

    public function test_admin_can_intervene_and_update_order_shipping_status(): void
    {
        $this->seedMarketplaceSettings();

        $admin = User::factory()->create(['email_verified_at' => now()]);
        $order = $this->createOrder();
        $status = ShippingStatus::query()->where('slug', 'shipped')->firstOrFail();

        $this->actingAs($admin, 'admin');

        $this->patch("/admin/orders/{$order->uuid}/shipping-status", [
            'shipping_status_id' => $status->id,
        ])->assertRedirect();

        $this->assertSame($status->id, $order->fresh()->shipping_status_id);
    }
}
