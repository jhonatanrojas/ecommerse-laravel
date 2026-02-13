<?php

namespace Tests\Feature\Marketplace;

use App\Models\User;
use App\Models\VendorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class AdminMarketplaceModerationTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_admin_can_approve_reject_and_suspend_vendor(): void
    {
        $this->seedMarketplaceSettings();

        $admin = User::factory()->create(['email_verified_at' => now()]);
        $vendor = $this->createVendor('pending');

        $this->actingAs($admin, 'admin');

        $this->patch(route('admin.vendors.status.update', $vendor), [
            'status' => 'approved',
        ])->assertRedirect();
        $this->assertSame('approved', $vendor->fresh()->status);

        $this->patch(route('admin.vendors.status.update', $vendor), [
            'status' => 'rejected',
            'rejection_reason' => 'Invalid docs',
        ])->assertRedirect();
        $this->assertSame('rejected', $vendor->fresh()->status);

        $this->patch(route('admin.vendors.status.update', $vendor), [
            'status' => 'suspended',
            'rejection_reason' => 'Policy violation',
        ])->assertRedirect();
        $this->assertSame('suspended', $vendor->fresh()->status);
    }

    public function test_admin_can_moderate_vendor_products_and_history_is_saved(): void
    {
        $this->seedMarketplaceSettings();

        $admin = User::factory()->create(['email_verified_at' => now()]);
        $vendor = $this->createVendor('approved');
        $product = $this->createMarketplaceProduct($vendor, [], ['is_approved' => false, 'approved_at' => null]);
        $vendorProduct = VendorProduct::query()->where('vendor_id', $vendor->id)->where('product_id', $product->id)->firstOrFail();

        $this->actingAs($admin, 'admin');

        $this->patch(route('admin.vendor-products.moderate', $vendorProduct), [
            'action' => 'approve',
            'moderation_notes' => 'Looks good',
        ])->assertRedirect();

        $approved = $vendorProduct->fresh();
        $this->assertTrue($approved->is_approved);
        $this->assertTrue($approved->is_active);
        $this->assertNotEmpty($approved->moderation_history);

        $this->patch(route('admin.vendor-products.moderate', $vendorProduct), [
            'action' => 'reject',
            'moderation_notes' => 'Need better images',
        ])->assertRedirect();

        $rejected = $vendorProduct->fresh();
        $this->assertFalse($rejected->is_approved);
        $this->assertFalse($rejected->is_active);
        $this->assertCount(2, $rejected->moderation_history);
    }
}
