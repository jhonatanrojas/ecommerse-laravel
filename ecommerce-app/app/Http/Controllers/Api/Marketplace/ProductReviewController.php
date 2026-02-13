<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Request $request, string $slug): JsonResponse
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();

        $reviews = Review::query()
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->latest()
            ->paginate((int) $request->input('per_page', 20));

        $summary = Review::query()
            ->where('product_id', $product->id)
            ->where('is_approved', true)
            ->selectRaw('COUNT(*) as total, COALESCE(AVG(rating), 0) as avg_rating')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $reviews->map(fn (Review $review) => [
                'uuid' => $review->uuid,
                'rating' => (int) $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'is_verified_purchase' => (bool) $review->is_verified_purchase,
                'helpful_count' => (int) $review->helpful_count,
                'author' => [
                    'id' => $review->user?->id,
                    'name' => $review->user?->name,
                ],
                'created_at' => $review->created_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
                'summary' => [
                    'reviews_count' => (int) ($summary?->total ?? 0),
                    'rating' => round((float) ($summary?->avg_rating ?? 0), 1),
                ],
            ],
        ]);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesion para publicar una resena.',
            ], 401);
        }

        $product = Product::query()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:3000'],
        ]);

        $isVerifiedPurchase = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        $review = Review::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'is_verified_purchase' => $isVerifiedPurchase,
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => $user->uuid,
            ]
        )->fresh('user:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Resena guardada correctamente.',
            'data' => [
                'uuid' => $review->uuid,
                'rating' => (int) $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'is_verified_purchase' => (bool) $review->is_verified_purchase,
                'author' => [
                    'id' => $review->user?->id,
                    'name' => $review->user?->name,
                ],
                'created_at' => $review->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
