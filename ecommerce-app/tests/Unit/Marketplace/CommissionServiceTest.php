<?php

namespace Tests\Unit\Marketplace;

use App\Models\Category;
use App\Services\Marketplace\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_calculates_using_global_commission_when_no_vendor_or_category_override(): void
    {
        $this->seedMarketplaceSettings(['marketplace_commission_rate' => 10]);

        $vendor = $this->createVendor('approved', ['commission_rate' => null]);
        $product = $this->createMarketplaceProduct($vendor);
        $order = $this->createOrder();
        $item = $this->createOrderItem($order, $product, quantity: 2, vendorId: $vendor->id, price: 50);

        $calc = app(CommissionService::class)->calculateForItem($item->fresh('product.category'), $vendor);

        $this->assertSame(10.0, $calc['rate']);
        $this->assertSame(100.0, $calc['subtotal']);
        $this->assertSame(10.0, $calc['commission_amount']);
        $this->assertSame(90.0, $calc['vendor_earnings']);
    }

    public function test_calculates_using_vendor_commission_when_category_commission_absent(): void
    {
        $this->seedMarketplaceSettings(['marketplace_commission_rate' => 5]);

        $vendor = $this->createVendor('approved', ['commission_rate' => 18]);
        $product = $this->createMarketplaceProduct($vendor);
        $order = $this->createOrder();
        $item = $this->createOrderItem($order, $product, quantity: 1, vendorId: $vendor->id, price: 200);

        $calc = app(CommissionService::class)->calculateForItem($item->fresh('product.category'), $vendor);

        $this->assertSame(18.0, $calc['rate']);
        $this->assertSame(36.0, $calc['commission_amount']);
        $this->assertSame(164.0, $calc['vendor_earnings']);
    }

    public function test_category_commission_has_priority_over_vendor_and_global(): void
    {
        $this->seedMarketplaceSettings(['marketplace_commission_rate' => 7]);

        $vendor = $this->createVendor('approved', ['commission_rate' => 18]);
        $category = Category::factory()->active()->create(['commission_rate' => 25]);
        $product = $this->createMarketplaceProduct($vendor, ['category_id' => $category->id]);
        $order = $this->createOrder();
        $item = $this->createOrderItem($order, $product, quantity: 1, vendorId: $vendor->id, price: 300);

        $calc = app(CommissionService::class)->calculateForItem($item->fresh('product.category'), $vendor);

        $this->assertSame(25.0, $calc['rate']);
        $this->assertSame(75.0, $calc['commission_amount']);
        $this->assertSame(225.0, $calc['vendor_earnings']);
    }
}
