<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->with('product.images')
            ->latest()
            ->get()
            ->map(fn (Wishlist $item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => [
                    'id' => $item->product?->id,
                    'name' => $item->product?->name,
                    'slug' => $item->product?->slug,
                    'price' => $item->product?->price,
                    'thumbnail' => optional($item->product?->images?->first())->url,
                ],
            ]);

        return response()->json(['data' => $items]);
    }

    public function store(Request $request, int $productId): JsonResponse
    {
        Product::query()->findOrFail($productId);

        $item = Wishlist::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'message' => 'Producto agregado a favoritos.',
            'data' => [
                'id' => $item->id,
                'product_id' => $item->product_id,
            ],
        ], 201);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Producto eliminado de favoritos.']);
    }
}
