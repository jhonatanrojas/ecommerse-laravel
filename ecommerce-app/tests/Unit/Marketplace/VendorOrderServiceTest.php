<?php

namespace Tests\Unit\Marketplace;

use App\Models\VendorOrder;
use App\Services\Marketplace\VendorOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class VendorOrderServiceTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_generates_vendor_orders_grouped_by_vendor_with_correct_totals(): void
    {
        $this->seedMarketplaceSettings(['marketplace_commission_rate' => 10]);

        $vendorA = $this->createVendor('approved', ['commission_rate' => 10]);
        $vendorB = $this->createVendor('approved', ['commission_rate' => 20]);

        $productA = $this->createMarketplaceProduct($vendorA);
        $productB = $this->createMarketplaceProduct($vendorB);

        $order = $this->createOrder();

        $this->createOrderItem($order, $productA, quantity: 2, vendorId: $vendorA->id, price: 100); // subtotal 200
        $this->createOrderItem($order, $productB, quantity: 1, vendorId: $vendorB->id, price: 300); // subtotal 300

        app(VendorOrderService::class)->syncFromOrder($order->fresh('items.product.category'));

        $rows = VendorOrder::query()->where('order_id', $order->id)->get()->keyBy('vendor_id');

        $this->assertCount(2, $rows);

        $this->assertSame('200.00', $rows[$vendorA->id]->subtotal);
        $this->assertSame('20.00', $rows[$vendorA->id]->commission_amount);
        $this->assertSame('180.00', $rows[$vendorA->id]->vendor_earnings);

        $this->assertSame('300.00', $rows[$vendorB->id]->subtotal);
        $this->assertSame('60.00', $rows[$vendorB->id]->commission_amount);
        $this->assertSame('240.00', $rows[$vendorB->id]->vendor_earnings);
    }

    public function test_sync_is_idempotent_and_updates_existing_vendor_order(): void
    {
        $this->seedMarketplaceSettings(['marketplace_commission_rate' => 10]);

        $vendor = $this->createVendor('approved', ['commission_rate' => 10]);
        $product = $this->createMarketplaceProduct($vendor);
        $order = $this->createOrder();

        $this->createOrderItem($order, $product, quantity: 1, vendorId: $vendor->id, price: 100);

        $service = app(VendorOrderService::class);
        $service->syncFromOrder($order->fresh('items.product.category'));

        $this->createOrderItem($order->fresh(), $product, quantity: 1, vendorId: $vendor->id, price: 50);
        $service->syncFromOrder($order->fresh('items.product.category'));

        $vendorOrder = VendorOrder::query()->where('order_id', $order->id)->where('vendor_id', $vendor->id)->first();

        $this->assertNotNull($vendorOrder);
        $this->assertSame('150.00', $vendorOrder->subtotal);
        $this->assertSame(1, VendorOrder::query()->where('order_id', $order->id)->where('vendor_id', $vendor->id)->count());
    }
}
