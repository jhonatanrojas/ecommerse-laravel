<?php

namespace Tests\Feature\Marketplace;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class MarketplaceApiTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_vendor_registration_endpoint_creates_vendor(): void
    {
        $this->seedMarketplaceSettings(['auto_approve_vendors' => false]);

        $response = $this->postJson('/api/marketplace/vendors/register', [
            'name' => 'API Vendor',
            'email' => 'api-vendor@example.com',
            'password' => 'password123',
            'business_name' => 'API Vendor Co',
            'document' => 'J-99999999-1',
            'phone' => '+1 555 999 0001',
            'address' => 'API Street',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('vendors', [
            'business_name' => 'API Vendor Co',
            'email' => 'api-vendor@example.com',
        ]);
    }

    public function test_public_vendor_profile_returns_only_approved_vendor(): void
    {
        $this->seedMarketplaceSettings();

        $approved = $this->createVendor('approved');
        $pending = $this->createVendor('pending');

        $this->getJson("/api/marketplace/vendors/{$approved->uuid}")
            ->assertOk()
            ->assertJsonPath('data.uuid', $approved->uuid);

        $this->getJson("/api/marketplace/vendors/{$pending->uuid}")
            ->assertNotFound();
    }

    public function test_marketplace_products_endpoint_supports_filters_by_vendor_and_search(): void
    {
        $this->seedMarketplaceSettings();

        $category = Category::factory()->active()->create();
        $vendorA = $this->createVendor('approved');
        $vendorB = $this->createVendor('approved');

        $productA = $this->createMarketplaceProduct($vendorA, [
            'name' => 'Gaming Keyboard Pro',
            'slug' => 'gaming-keyboard-pro',
            'category_id' => $category->id,
        ]);

        $this->createMarketplaceProduct($vendorB, [
            'name' => 'Office Chair',
            'slug' => 'office-chair',
        ]);

        $response = $this->getJson('/api/marketplace/products?' . http_build_query([
            'vendor_uuid' => $vendorA->uuid,
            'search' => 'Keyboard',
            'category_id' => $category->id,
        ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.filters.vendor_uuid', $vendorA->uuid)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $productA->id);
    }

    public function test_products_by_vendor_endpoint_returns_only_vendor_products(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');
        $this->createMarketplaceProduct($vendor, ['name' => 'Vendor Main Product']);

        $otherVendor = $this->createVendor('approved');
        $this->createMarketplaceProduct($otherVendor, ['name' => 'Other Vendor Product']);

        $response = $this->getJson("/api/marketplace/vendors/{$vendor->uuid}/products");

        $response->assertOk()->assertJsonPath('success', true);

        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('Vendor Main Product'));
        $this->assertFalse($names->contains('Other Vendor Product'));
    }
}
