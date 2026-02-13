<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function autocomplete(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $limit = max(1, min((int) $request->query('limit', 6), 12));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'sellers' => [],
            ]);
        }

        $products = Product::query()
            ->active()
            ->with(['images' => fn ($q) => $q->orderBy('order')])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('vendor', fn ($vendorQ) => $vendorQ->where('business_name', 'like', "%{$query}%"));
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"])
            ->limit($limit)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'thumbnail' => optional($product->images->first())->url,
                'stock' => $product->stock,
            ]);

        $categories = Category::query()
            ->active()
            ->withCount('products')
            ->where('name', 'like', "%{$query}%")
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"])
            ->limit($limit)
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'product_count' => $category->products_count,
            ]);

        $sellers = Vendor::query()
            ->where('status', 'approved')
            ->withCount('orders')
            ->where('business_name', 'like', "%{$query}%")
            ->orderByRaw("CASE WHEN business_name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"])
            ->limit($limit)
            ->get()
            ->map(fn (Vendor $vendor) => [
                'id' => $vendor->id,
                'name' => $vendor->business_name,
                'slug' => $vendor->uuid,
                'logo' => null,
                'rating' => 4.8,
                'sales_count' => $vendor->orders_count,
                'location' => $this->extractLocation($vendor->address),
            ]);

        return response()->json([
            'products' => $products->values(),
            'categories' => $categories->values(),
            'sellers' => $sellers->values(),
        ]);
    }

    private function extractLocation(?string $address): ?string
    {
        if (! $address) {
            return null;
        }

        $chunks = array_map('trim', explode(',', $address));
        if (count($chunks) < 2) {
            return $address;
        }

        return implode(', ', array_slice($chunks, -2));
    }
}
