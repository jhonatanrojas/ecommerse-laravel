<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->select(['id', 'name', 'slug', 'image', 'description'])
            ->withCount([
                'products as product_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->where('is_active', true)
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function products(Request $request, string $slug): JsonResponse
    {
        $category = Category::query()
            ->select(['id', 'name', 'slug', 'image', 'description'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada.',
            ], 404);
        }

        $sort = $request->query('sort', 'newest');
        $perPage = max(1, min((int) $request->query('per_page', 12), 60));
        $page = max(1, (int) $request->query('page', 1));

        $query = Product::query()
            ->select([
                'id',
                'category_id',
                'name',
                'slug',
                'description',
                'price',
                'compare_price',
                'stock',
                'is_active',
                'created_at',
            ])
            ->with([
                'category:id,name,slug',
                'images:id,product_id,image_path,thumbnail_path,alt_text,is_primary,order',
                'variants:id,product_id,name,sku,price,stock,attributes',
            ])
            ->where('category_id', $category->id)
            ->where('is_active', true);

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->query('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->query('max_price'));
        }

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->image,
                'description' => $category->description,
            ],
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
        ]);
    }
}

