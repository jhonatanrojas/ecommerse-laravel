<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ProductService::class);
    }

    public function test_get_products_applies_filters_and_returns_paginated_result(): void
    {
        $categoryOne = Category::factory()->active()->create();
        $categoryTwo = Category::factory()->active()->create();

        Product::factory()->active()->create([
            'name' => 'Laptop Pro',
            'slug' => 'laptop-pro',
            'category_id' => $categoryOne->id,
            'price' => 1200,
        ]);
        Product::factory()->active()->create([
            'name' => 'Laptop Basic',
            'slug' => 'laptop-basic',
            'category_id' => $categoryOne->id,
            'price' => 650,
        ]);
        Product::factory()->active()->create([
            'name' => 'Phone Max',
            'slug' => 'phone-max',
            'category_id' => $categoryTwo->id,
            'price' => 450,
        ]);

        $result = $this->service->getProducts([
            'search' => 'Laptop',
            'category_id' => $categoryOne->id,
            'min_price' => 600,
            'max_price' => 1300,
            'sort' => 'price_desc',
            'per_page' => 10,
            'page' => 1,
        ]);

        $items = $result->items();

        $this->assertCount(2, $items);
        $this->assertSame('laptop-pro', $items[0]->slug);
        $this->assertSame('laptop-basic', $items[1]->slug);
        $this->assertTrue($items[0]->relationLoaded('category'));
        $this->assertTrue($items[0]->relationLoaded('images'));
        $this->assertTrue($items[0]->relationLoaded('variants'));
    }

    public function test_get_product_by_slug_returns_product_with_required_relationships(): void
    {
        $category = Category::factory()->active()->create();
        $product = Product::factory()->active()->withCategory($category->id)->create([
            'slug' => 'ultra-monitor',
        ]);
        ProductImage::factory()->primary()->create(['product_id' => $product->id]);
        ProductVariant::factory()->create(['product_id' => $product->id]);

        $result = $this->service->getProductBySlug('ultra-monitor');

        $this->assertSame($product->id, $result->id);
        $this->assertTrue($result->relationLoaded('category'));
        $this->assertTrue($result->relationLoaded('images'));
        $this->assertTrue($result->relationLoaded('variants'));
    }

    public function test_get_product_by_slug_throws_exception_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->getProductBySlug('slug-que-no-existe');
    }

    public function test_get_related_products_returns_same_category_without_current_product(): void
    {
        $category = Category::factory()->active()->create();
        $otherCategory = Category::factory()->active()->create();

        $target = Product::factory()->active()->withCategory($category->id)->create([
            'slug' => 'target-related',
        ]);
        Product::factory()->active()->withCategory($category->id)->count(4)->create();
        Product::factory()->active()->withCategory($otherCategory->id)->count(2)->create();

        $related = $this->service->getRelatedProducts($target, 3);

        $this->assertCount(3, $related);
        $this->assertFalse($related->pluck('id')->contains($target->id));
        $this->assertTrue($related->every(fn (Product $item): bool => $item->category_id === $category->id));
        $this->assertTrue($related->every(fn (Product $item): bool => $item->relationLoaded('category')));
        $this->assertTrue($related->every(fn (Product $item): bool => $item->relationLoaded('images')));
        $this->assertTrue($related->every(fn (Product $item): bool => $item->relationLoaded('variants')));
    }
}
