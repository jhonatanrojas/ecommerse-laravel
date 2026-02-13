<?php

namespace Tests\Feature\Marketplace;

use App\Models\Category;
use App\Models\VendorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsMarketplaceData;
use Tests\TestCase;

class VendorProductManagementTest extends TestCase
{
    use RefreshDatabase;
    use BuildsMarketplaceData;

    public function test_vendor_can_create_product_and_vendor_relationship(): void
    {
        Storage::fake('public');
        $this->seedMarketplaceSettings(['auto_approve_vendor_products' => false]);

        $vendor = $this->createVendor('approved');
        $category = Category::factory()->active()->create();

        $this->actingAs($vendor->user, 'vendor');

        $response = $this->post('/vendor/products', [
            'category_id' => $category->id,
            'name' => 'Vendor Product Test',
            'sku' => 'VENDOR-SKU-001',
            'description' => 'Description',
            'price' => 120,
            'stock' => 10,
            'is_active' => true,
            'images' => [UploadedFile::fake()->image('product.jpg')],
        ]);

        $response->assertRedirect(route('vendor.products.index'));

        $this->assertDatabaseHas('products', ['name' => 'Vendor Product Test']);
        $this->assertDatabaseHas('vendor_products', [
            'vendor_id' => $vendor->id,
            'is_approved' => 0,
            'is_active' => 1,
        ]);
    }

    public function test_vendor_can_edit_and_product_returns_to_moderation(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');
        $product = $this->createMarketplaceProduct($vendor, ['name' => 'Original Name']);

        $this->actingAs($vendor->user, 'vendor');

        $response = $this->put("/vendor/products/{$product->id}", [
            'category_id' => $product->category_id,
            'name' => 'Updated Name',
            'sku' => $product->sku,
            'description' => 'Updated Description',
            'price' => 250,
            'stock' => 30,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('vendor.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
        ]);

        $this->assertDatabaseHas('vendor_products', [
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'is_approved' => 0,
        ]);
    }

    public function test_vendor_can_toggle_product_active_status(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');
        $product = $this->createMarketplaceProduct($vendor);
        $vendorProduct = VendorProduct::query()->where('vendor_id', $vendor->id)->where('product_id', $product->id)->firstOrFail();

        $this->actingAs($vendor->user, 'vendor');

        $this->patch("/vendor/products/{$product->id}/toggle")
            ->assertRedirect();

        $this->assertSame(!$vendorProduct->is_active, $vendorProduct->fresh()->is_active);
    }

    public function test_store_product_request_validates_price_stock_and_images_for_vendor_flow(): void
    {
        $this->seedMarketplaceSettings();

        $vendor = $this->createVendor('approved');
        $category = Category::factory()->active()->create();

        $this->actingAs($vendor->user, 'vendor');

        $response = $this->from('/vendor/products/create')->post('/vendor/products', [
            'category_id' => $category->id,
            'name' => 'Invalid Product',
            'sku' => 'INV-SKU-001',
            'price' => -10,
            'stock' => -1,
            'images' => [UploadedFile::fake()->create('malicious.pdf', 50, 'application/pdf')],
        ]);

        $response->assertRedirect('/vendor/products/create');
        $response->assertSessionHasErrors(['price', 'stock', 'images.0']);
    }
}
