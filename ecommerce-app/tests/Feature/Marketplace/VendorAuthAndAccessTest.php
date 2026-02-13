<?php

namespace Tests\Feature\Marketplace;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class VendorAuthAndAccessTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_vendor_can_register_and_profile_is_created_as_pending_by_default(): void
    {
        $this->seedMarketplaceSettings(['auto_approve_vendors' => false]);

        $response = $this->post('/vendor/register', [
            'name' => 'Marketplace Seller',
            'email' => 'seller@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_name' => 'Seller Corp',
            'document' => 'J-11111111-1',
            'phone' => '+1 555 000 0001',
            'address' => '123 Main St',
            'payout_cycle' => 'manual',
        ]);

        $response->assertRedirect(route('vendor.login'));

        $this->assertDatabaseHas('vendors', [
            'business_name' => 'Seller Corp',
            'status' => 'pending',
            'email' => 'seller@example.com',
        ]);
    }

    public function test_vendor_login_redirects_to_pending_when_not_approved(): void
    {
        $this->seedMarketplaceSettings();
        $vendor = $this->createVendor('pending', [], [
            'email' => 'pending-vendor@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/vendor/login', [
            'email' => $vendor->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('vendor.pending'));
        $this->assertGuest('vendor');
    }

    public function test_approved_vendor_can_authenticate_with_vendor_guard_and_access_dashboard(): void
    {
        $this->seedMarketplaceSettings();
        $vendor = $this->createVendor('approved');

        $this->actingAs($vendor->user, 'vendor');

        $this->get('/vendor/dashboard')
            ->assertOk();

        $this->assertAuthenticatedAs($vendor->user, 'vendor');
    }

    public function test_guest_cannot_access_vendor_panel(): void
    {
        $this->seedMarketplaceSettings();

        $this->get('/vendor/products')
            ->assertRedirect(route('vendor.login'));
    }

    public function test_suspended_vendor_is_blocked_by_vendor_approved_middleware(): void
    {
        $this->seedMarketplaceSettings();
        $vendor = $this->createVendor('suspended');

        $this->actingAs($vendor->user, 'vendor');

        $this->get('/vendor/dashboard')
            ->assertRedirect(route('vendor.login'));

        $this->assertGuest('vendor');
    }

    public function test_vendor_can_update_profile_and_payout_method(): void
    {
        $this->seedMarketplaceSettings();
        $vendor = $this->createVendor('approved');

        $this->actingAs($vendor->user, 'vendor');

        $this->put('/vendor/profile', [
            'business_name' => 'New Business Name',
            'document' => 'J-22222222-2',
            'phone' => '+1 555 111 2222',
            'email' => 'new-vendor-mail@example.com',
            'address' => 'New Address',
            'payout_cycle' => 'monthly',
            'payout_method' => [
                'provider' => 'manual',
                'account' => 'NEW-ACCOUNT',
                'beneficiary' => 'New Beneficiary',
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'business_name' => 'New Business Name',
            'document' => 'J-22222222-2',
            'payout_cycle' => 'monthly',
        ]);
    }
}
