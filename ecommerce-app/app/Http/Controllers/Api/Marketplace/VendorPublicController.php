<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorPublicController extends Controller
{
    public function vendors(Request $request): JsonResponse
    {
        $vendors = Vendor::query()
            ->where('status', 'approved')
            ->withCount('orders')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q')->value();
                $query->where('business_name', 'like', "%{$term}%");
            })
            ->paginate((int) $request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $vendors->map(fn (Vendor $vendor) => [
                'id' => $vendor->id,
                'uuid' => $vendor->uuid,
                'slug' => Str::slug($vendor->business_name),
                'business_name' => $vendor->business_name,
                'phone' => $vendor->phone,
                'email' => $vendor->email,
                'address' => $vendor->address,
                'location' => $this->extractLocation($vendor->address),
                'sales_count' => $vendor->orders_count,
            ])->values(),
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'total' => $vendors->total(),
            ],
        ]);
    }

    public function profile(string $slug): JsonResponse
    {
        $vendor = $this->resolveVendor($slug);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $vendor->id,
                'uuid' => $vendor->uuid,
                'slug' => Str::slug($vendor->business_name),
                'business_name' => $vendor->business_name,
                'phone' => $vendor->phone,
                'email' => $vendor->email,
                'address' => $vendor->address,
                'location' => $this->extractLocation($vendor->address),
                'joined_at' => $vendor->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function products(Request $request, string $slug): JsonResponse
    {
        $vendor = $this->resolveVendor($slug);

        $query = $this->buildMarketplaceQuery($request)
            ->whereHas('vendorProducts', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->where('is_approved', true);
            });

        $products = $query->paginate((int) $request->input('per_page', 24));

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function marketplace(Request $request): JsonResponse
    {
        $products = $this->buildMarketplaceQuery($request)
            ->paginate((int) $request->input('per_page', 24));

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'filters' => [
                    'vendor_uuid' => $request->input('vendor_uuid'),
                    'category_id' => $request->input('category_id'),
                    'search' => $request->input('search', $request->input('q')),
                    'price_min' => $request->input('price_min'),
                    'price_max' => $request->input('price_max'),
                    'rating' => $request->input('rating'),
                    'free_shipping' => $request->boolean('free_shipping'),
                    'fast_shipping' => $request->boolean('fast_shipping'),
                    'location' => $request->input('location'),
                    'condition' => $request->input('condition'),
                    'sort' => $request->input('sort', 'relevance'),
                ],
            ],
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->merge([
            'search' => $request->input('q'),
        ]);

        return $this->marketplace($request);
    }

    public function showProduct(Request $request, string $slug): JsonResponse
    {
        $product = Product::query()
            ->with([
                'images',
                'category',
                'variants',
                'vendor.shippingMethods',
            ])
            ->withCount([
                'views as views_count',
                'orderItems as sales_count',
                'reviews as reviews_count' => fn ($q) => $q->where('is_approved', true),
            ])
            ->withAvg([
                'reviews as rating' => fn ($q) => $q->where('is_approved', true),
            ], 'rating')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('vendorProducts', function ($query) {
                $query->where('is_active', true)->where('is_approved', true);
            })
            ->firstOrFail();

        $this->trackProductView($request, $product->id);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    protected function resolveVendor(string $slug): Vendor
    {
        return Vendor::query()
            ->where('status', 'approved')
            ->where(function ($query) use ($slug) {
                $query->where('uuid', $slug)
                    ->orWhereRaw("LOWER(REPLACE(business_name, ' ', '-')) = ?", [Str::lower($slug)]);
            })
            ->firstOrFail();
    }

    protected function buildMarketplaceQuery(Request $request): Builder
    {
        $query = Product::query()
            ->with([
                'images',
                'category',
                'vendor.shippingMethods',
            ])
            ->withCount([
                'views as views_count',
                'orderItems as sales_count',
                'reviews as reviews_count' => fn ($q) => $q->where('is_approved', true),
            ])
            ->withAvg([
                'reviews as rating' => fn ($q) => $q->where('is_approved', true),
            ], 'rating')
            ->where('is_active', true)
            ->whereHas('vendorProducts', function ($q) {
                $q->where('is_active', true)->where('is_approved', true);
            });

        $search = $request->input('search', $request->input('q'));

        $query
            ->when($request->filled('vendor_uuid'), function ($builder) use ($request) {
                $vendorUuid = $request->string('vendor_uuid')->value();
                $builder->whereHas('vendorProducts.vendor', fn ($q) => $q->where('uuid', $vendorUuid));
            })
            ->when($request->filled('category_id'), fn ($builder) => $builder->where('category_id', (int) $request->input('category_id')))
            ->when($request->filled('categories'), function ($builder) use ($request) {
                $ids = collect(explode(',', (string) $request->input('categories')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->values();

                if ($ids->isNotEmpty()) {
                    $builder->whereIn('category_id', $ids->all());
                }
            })
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($q) use ($search) {
                    $q->search((string) $search)
                        ->orWhereHas('vendor', fn ($vendorQ) => $vendorQ->where('business_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('price_min'), fn ($builder) => $builder->where('price', '>=', (float) $request->input('price_min')))
            ->when($request->filled('price_max'), fn ($builder) => $builder->where('price', '<=', (float) $request->input('price_max')))
            ->when($request->filled('rating'), fn ($builder) => $builder->having('rating', '>=', (float) $request->input('rating')))
            ->when($request->boolean('free_shipping'), function ($builder) {
                $builder->whereHas('vendor.shippingMethods', function ($shippingQ) {
                    $shippingQ->where('is_active', true)
                        ->where('base_rate', '<=', 0);
                });
            })
            ->when($request->boolean('fast_shipping'), function ($builder) {
                $builder->whereHas('vendor.shippingMethods', function ($shippingQ) {
                    $shippingQ->where('is_active', true)
                        ->where(function ($inner) {
                            $inner->where('code', 'like', '%express%')
                                ->orWhereJsonContains('rules->eta_max_days', 1)
                                ->orWhereJsonContains('rules->eta_max_days', 2);
                        });
                });
            })
            ->when($request->filled('location'), function ($builder) use ($request) {
                $location = (string) $request->input('location');
                if ($location !== 'near') {
                    $builder->whereHas('vendor', function ($vendorQ) use ($location) {
                        $vendorQ->where('address', 'like', "%{$location}%");
                    });
                }
            })
            ->when($request->filled('condition'), function ($builder) use ($request) {
                $condition = (string) $request->input('condition');
                if ($condition === 'new') {
                    $builder->where('created_at', '>=', now()->subYear());
                }
                if ($condition === 'used') {
                    $builder->where('created_at', '<', now()->subYear());
                }
            });

        $sort = (string) $request->input('sort', 'relevance');

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            'best_selling' => $query->orderByDesc('sales_count'),
            'best_rated' => $query->orderByDesc('rating'),
            default => $query->orderByDesc('sales_count')->orderByDesc('rating')->orderByDesc('id'),
        };

        return $query;
    }

    private function trackProductView(Request $request, int $productId): void
    {
        $userId = $request->user()?->id;
        $sessionId = null;

        if ($request->hasSession()) {
            $sessionId = $request->session()->getId();
        }

        if (! $sessionId) {
            $sessionId = substr(sha1(($request->ip() ?? '') . '|' . ($request->userAgent() ?? '')), 0, 40);
        }

        $exists = ProductView::query()
            ->where('product_id', $productId)
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->where('viewed_at', '>=', now()->subHours(6))
            ->exists();

        if ($exists) {
            return;
        }

        ProductView::query()->create([
            'product_id' => $productId,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'viewed_at' => now(),
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
