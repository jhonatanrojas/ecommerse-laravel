<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsProductCatalogData;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    use BuildsProductCatalogData;

    public function test_products_index_returns_paginated_products(): void
    {
        $this->seedCatalogProducts(6);

        $response = $this->getJson('/api/products?per_page=2&page=1');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'stock',
                        'status',
                        'category',
                        'images',
                        'variants',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);

        $perPage = $response->json('meta.per_page');
        $total = $response->json('meta.total');

        if (is_array($perPage)) {
            $this->assertContains(2, $perPage);
        } else {
            $this->assertSame(2, $perPage);
        }

        if (is_array($total)) {
            $this->assertContains(6, $total);
        } else {
            $this->assertSame(6, $total);
        }
    }

    public function test_products_index_filter_by_search(): void
    {
        $this->seedCatalogProducts(6);

        $response = $this->getJson('/api/products?search=Keyboard');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertStringContainsString('Keyboard', $response->json('data.0.name'));
    }

    public function test_products_index_filter_by_category_id(): void
    {
        $includedCategory = Category::factory()->active()->create();
        $excludedCategory = Category::factory()->active()->create();

        Product::factory()->active()->withCategory($includedCategory->id)->count(2)->create();
        Product::factory()->active()->withCategory($excludedCategory->id)->count(3)->create();

        $response = $this->getJson('/api/products?category_id=' . $includedCategory->id);

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        $this->assertTrue(
            collect($response->json('data'))->every(
                fn (array $product): bool => data_get($product, 'category.id') === $includedCategory->id
            )
        );
    }

    public function test_products_index_filter_by_price_range(): void
    {
        $this->seedCatalogProducts(6);

        $response = $this->getJson('/api/products?min_price=100&max_price=500');

        $response->assertOk();
        $this->assertTrue(
            collect($response->json('data'))
                ->pluck('price')
                ->map(fn ($price) => (float) $price)
                ->every(fn (float $price): bool => $price >= 100 && $price <= 500)
        );
    }

    public function test_products_index_sorting_works_for_price_and_newest(): void
    {
        Product::factory()->active()->create([
            'name' => 'A',
            'slug' => 'a',
            'price' => 50,
            'created_at' => now()->subDays(3),
        ]);
        Product::factory()->active()->create([
            'name' => 'B',
            'slug' => 'b',
            'price' => 150,
            'created_at' => now()->subDays(2),
        ]);
        Product::factory()->active()->create([
            'name' => 'C',
            'slug' => 'c',
            'price' => 100,
            'created_at' => now()->subDay(),
        ]);

        $asc = $this->getJson('/api/products?sort=price_asc')->json('data');
        $desc = $this->getJson('/api/products?sort=price_desc')->json('data');
        $newest = $this->getJson('/api/products?sort=newest')->json('data');

        $this->assertSame([50.0, 100.0, 150.0], collect($asc)->pluck('price')->map(fn ($p) => (float) $p)->values()->all());
        $this->assertSame([150.0, 100.0, 50.0], collect($desc)->pluck('price')->map(fn ($p) => (float) $p)->values()->all());
        $this->assertSame(['c', 'b', 'a'], collect($newest)->pluck('slug')->values()->all());
    }

    public function test_products_show_returns_expected_product_by_slug(): void
    {
        $product = Product::factory()->active()->create([
            'name' => 'Gaming Laptop',
            'slug' => 'gaming-laptop',
            'description' => 'High performance laptop.',
        ]);

        $response = $this->getJson('/api/products/' . $product->slug);

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.slug', $product->slug)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'stock',
                    'status',
                    'category',
                    'images',
                    'variants',
                ],
            ]);
    }

    public function test_products_show_returns_404_when_slug_does_not_exist(): void
    {
        $this->getJson('/api/products/not-found-slug')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_products_related_returns_products_from_same_category_and_not_self(): void
    {
        $category = Category::factory()->active()->create();
        $otherCategory = Category::factory()->active()->create();

        $target = Product::factory()->active()->withCategory($category->id)->create([
            'name' => 'Target Product',
            'slug' => 'target-product',
        ]);
        Product::factory()->active()->withCategory($category->id)->count(3)->create();
        Product::factory()->active()->withCategory($otherCategory->id)->count(2)->create();

        $response = $this->getJson('/api/products/' . $target->slug . '/related');

        $response->assertOk()->assertJsonPath('success', true);
        $related = collect($response->json('data'));

        $this->assertGreaterThan(0, $related->count());
        $this->assertFalse($related->pluck('id')->contains($target->id));
        $this->assertTrue(
            $related->every(fn (array $item): bool => data_get($item, 'category.id') === $category->id)
        );
    }
}
