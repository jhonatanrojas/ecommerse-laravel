<?php

namespace Tests\Feature\Marketplace;

use App\Enums\PaymentRecordStatus;
use App\Enums\PaymentStatus;
use App\Models\PaymentMethod;
use App\Models\VendorOrder;
use App\Models\VendorPayout;
use App\Services\Payments\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class MarketplacePaymentsIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    protected function setUp(): void
    {
        parent::setUp();

        PaymentMethod::query()->create([
            'name' => 'Cash',
            'slug' => 'cash',
            'driver_class' => \App\Services\Payments\Drivers\CashPaymentDriver::class,
            'is_active' => true,
            'config' => ['auto_approve' => true],
        ]);
    }

    public function test_completed_payment_updates_order_and_processes_vendor_payouts_when_auto_enabled(): void
    {
        $this->seedMarketplaceSettings(['enable_automatic_payouts' => true]);

        $vendor = $this->createVendor('approved', [
            'payout_method' => ['provider' => 'manual'],
        ]);

        $order = $this->createOrder(overrides: [
            'payment_status' => PaymentStatus::Pending,
            'total' => 100,
        ]);

        VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $order->id,
            'subtotal' => 100,
            'commission_amount' => 10,
            'vendor_earnings' => 90,
            'payout_status' => 'pending',
        ]);

        $paymentService = app(PaymentService::class);
        $payment = $paymentService->createPayment($order, 'cash');

        $paymentService->updateStatus($payment, PaymentRecordStatus::Completed->value, []);

        $this->assertSame(PaymentStatus::Paid->value, $order->fresh()->payment_status->value);
        $this->assertDatabaseHas('vendor_payouts', [
            'vendor_id' => $vendor->id,
            'status' => 'completed',
            'amount' => 90,
        ]);
        $this->assertDatabaseHas('vendor_orders', [
            'vendor_id' => $vendor->id,
            'order_id' => $order->id,
            'payout_status' => 'paid',
        ]);
    }

    public function test_failed_payment_does_not_trigger_vendor_payout_generation(): void
    {
        $this->seedMarketplaceSettings(['enable_automatic_payouts' => true]);

        $vendor = $this->createVendor('approved');
        $order = $this->createOrder(overrides: ['payment_status' => PaymentStatus::Pending, 'total' => 120]);

        VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $order->id,
            'vendor_earnings' => 100,
            'payout_status' => 'pending',
        ]);

        $paymentService = app(PaymentService::class);
        $payment = $paymentService->createPayment($order, 'cash');
        $paymentService->updateStatus($payment, PaymentRecordStatus::Failed->value, []);

        $this->assertSame(PaymentStatus::Failed->value, $order->fresh()->payment_status->value);
        $this->assertSame(0, VendorPayout::query()->count());
        $this->assertSame('pending', VendorOrder::query()->first()->payout_status);
    }

    public function test_refund_adjusts_vendor_earnings_proportionally(): void
    {
        $this->seedMarketplaceSettings(['enable_automatic_payouts' => false]);

        $vendor = $this->createVendor('approved');
        $order = $this->createOrder(overrides: ['payment_status' => PaymentStatus::Pending, 'total' => 100]);

        $vendorOrder = VendorOrder::factory()->create([
            'vendor_id' => $vendor->id,
            'order_id' => $order->id,
            'subtotal' => 100,
            'commission_amount' => 10,
            'vendor_earnings' => 90,
            'payout_status' => 'pending',
        ]);

        $paymentService = app(PaymentService::class);
        $payment = $paymentService->createPayment($order, 'cash');
        $paymentService->updateStatus($payment, PaymentRecordStatus::Completed->value, []);
        $paymentService->refund($payment->fresh(), 50);

        $updatedVendorOrder = $vendorOrder->fresh();

        $this->assertSame('50.00', $updatedVendorOrder->subtotal);
        $this->assertSame('5.00', $updatedVendorOrder->commission_amount);
        $this->assertSame('45.00', $updatedVendorOrder->vendor_earnings);
    }
}
