<?php

namespace Tests\Feature\Marketplace;

use App\Enums\PaymentStatus;
use App\Models\VendorOrder;
use App\Models\VendorPayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class VendorPayoutAccessTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_vendor_only_sees_own_payouts(): void
    {
        $this->seedMarketplaceSettings();

        $vendorA = $this->createVendor('approved');
        $vendorB = $this->createVendor('approved');

        VendorPayout::factory()->create(['vendor_id' => $vendorA->id, 'amount' => 100]);
        VendorPayout::factory()->create(['vendor_id' => $vendorB->id, 'amount' => 200]);

        $this->actingAs($vendorA->user, 'vendor');

        $response = $this->get('/vendor/payouts')->assertOk();

        $response->assertSee('100.00');
        $response->assertDontSee('200.00');
    }

    public function test_vendor_can_request_payout_with_available_earnings(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved', [
            'payout_method' => ['provider' => 'manual'],
        ]);

        $order = $this->createOrder(overrides: ['payment_status' => PaymentStatus::Paid]);

        VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $order->id,
            'vendor_earnings' => 150,
            'payout_status' => 'pending',
        ]);

        $this->actingAs($vendor->user, 'vendor');

        $this->post('/vendor/payouts/request', [
            'amount' => 100,
        ])->assertRedirect();

        $this->assertDatabaseHas('vendor_payouts', [
            'vendor_id' => $vendor->id,
            'amount' => 100,
            'status' => 'pending',
        ]);
    }
}
