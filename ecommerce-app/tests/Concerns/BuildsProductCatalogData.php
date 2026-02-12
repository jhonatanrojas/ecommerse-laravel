<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait BuildsProductCatalogData
{
    protected function seedCatalogProducts(int $count = 6): Collection
    {
        $category = Category::factory()->active()->create([
            'name' => 'Electronics',
            'slug' => 'electronics-' . Str::lower(Str::random(6)),
        ]);

        return Product::factory()
            ->count($count)
            ->active()
            ->withCategory($category->id)
            ->sequence(
                ['name' => 'Laptop Pro 14', 'slug' => 'laptop-pro-14', 'price' => 999.99, 'stock' => 8],
                ['name' => 'Laptop Air 13', 'slug' => 'laptop-air-13', 'price' => 799.99, 'stock' => 12],
                ['name' => 'Mouse Wireless', 'slug' => 'mouse-wireless', 'price' => 39.99, 'stock' => 40],
                ['name' => 'Keyboard Mechanical', 'slug' => 'keyboard-mechanical', 'price' => 129.99, 'stock' => 18],
                ['name' => 'Monitor 27 4K', 'slug' => 'monitor-27-4k', 'price' => 449.99, 'stock' => 10],
                ['name' => 'Dock USB-C', 'slug' => 'dock-usbc', 'price' => 119.99, 'stock' => 22],
            )
            ->create()
            ->each(function (Product $product): void {
                ProductImage::factory()->primary()->create(['product_id' => $product->id]);
                ProductImage::factory()->create(['product_id' => $product->id, 'order' => 1]);
                ProductVariant::factory()->create(['product_id' => $product->id]);
            });
    }
}

