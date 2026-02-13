<?php

namespace Tests\Unit\Marketplace;

use App\Enums\PaymentStatus;
use App\Models\VendorOrder;
use App\Services\Marketplace\VendorPayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class VendorPayoutServiceTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_creates_pending_payout_only_from_paid_orders(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');

        $paidOrder = $this->createOrder(overrides: ['payment_status' => PaymentStatus::Paid]);
        $pendingOrder = $this->createOrder(overrides: ['payment_status' => PaymentStatus::Pending]);

        VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $paidOrder->id,
            'vendor_earnings' => 120,
            'payout_status' => 'pending',
        ]);

        VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $pendingOrder->id,
            'vendor_earnings' => 300,
            'payout_status' => 'pending',
        ]);

        $payout = app(VendorPayoutService::class)->createPendingPayout($vendor);

        $this->assertNotNull($payout);
        $this->assertSame('120.00', $payout->amount);
        $this->assertSame('pending', $payout->status);
    }

    public function test_process_payout_marks_vendor_orders_as_paid(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved', [
            'payout_method' => ['provider' => 'manual'],
        ]);

        $paidOrder = $this->createOrder(overrides: ['payment_status' => PaymentStatus::Paid]);

        VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $paidOrder->id,
            'vendor_earnings' => 200,
            'payout_status' => 'pending',
        ]);

        $service = app(VendorPayoutService::class);
        $payout = $service->createPendingPayout($vendor);

        $processed = $service->processPayout($payout);

        $this->assertSame('completed', $processed->status);
        $this->assertNotNull($processed->transaction_reference);
        $this->assertSame(
            1,
            VendorOrder::query()->where('vendor_id', $vendor->id)->where('payout_status', 'paid')->count()
        );
    }

    public function test_returns_null_when_vendor_has_no_available_earnings(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');

        $this->assertNull(app(VendorPayoutService::class)->createPendingPayout($vendor));
    }
}
