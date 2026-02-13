<?php

namespace App\Http\Resources;

use App\Models\Review;
use App\Models\VendorOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    private static array $vendorMetricsCache = [];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $precioOferta = null;
        if ($this->compare_price !== null && (float) $this->compare_price > (float) $this->price) {
            $precioOferta = $this->price;
        }

        $primaryImage = $this->images->firstWhere('is_primary', true)?->url
            ?? $this->images->first()?->url;

        $ratingValue = $this->rating ?? $this->reviews_avg_rating ?? 0;
        $reviewsCount = $this->reviews_count ?? 0;
        $salesCount = $this->sales_count ?? 0;
        $viewsCount = $this->views_count ?? 0;
        $discountPercentage = $this->computeDiscountPercentage();
        $isNew = $this->created_at instanceof Carbon
            ? $this->created_at->greaterThanOrEqualTo(now()->subDays(30))
            : false;
        $isHot = (int) $salesCount >= 20 || (int) $viewsCount >= 120;
        $installments = $this->buildInstallments();

        $vendorMetrics = $this->resolveVendorMetrics($this->vendor?->id);
        $shipping = $this->resolveShipping();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'nombre' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'descripcion' => $this->description,
            'short_description' => $this->short_description,
            'image' => $primaryImage,
            'price' => $this->price,
            'precio' => $this->price,
            'compare_price' => $this->compare_price,
            'precio_oferta' => $precioOferta,
            'stock' => $this->stock,
            'low_stock' => (bool) ($this->stock <= $this->low_stock_threshold),
            'rating' => round((float) $ratingValue, 1),
            'reviews_count' => (int) $reviewsCount,
            'sales_count' => (int) $salesCount,
            'views_count' => (int) $viewsCount,
            'discount_percentage' => $discountPercentage,
            'is_hot' => $isHot,
            'is_new' => $isNew,
            'free_shipping' => $shipping['free_shipping'],
            'estimated_delivery' => $shipping['estimated_delivery'],
            'installments' => $installments,
            'condition' => 'new',
            'category' => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ] : null;
            }),
            'categoria' => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'id' => $this->category->id,
                    'nombre' => $this->category->name,
                    'slug' => $this->category->slug,
                ] : null;
            }),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'imagenes' => ProductImageResource::collection($this->whenLoaded('images')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'variantes' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'status' => $this->is_active ? 'active' : 'inactive',
            'estado' => $this->is_active ? 'activo' : 'inactivo',
            'vendor' => $this->whenLoaded('vendor', function () {
                return $this->vendor ? [
                    'id' => $this->vendor->id,
                    'uuid' => $this->vendor->uuid,
                    'slug' => Str::slug($this->vendor->business_name),
                    'business_name' => $this->vendor->business_name,
                    'name' => $this->vendor->business_name,
                    'verified' => $this->vendor->status === 'approved',
                    'email' => $this->vendor->email,
                    'phone' => $this->vendor->phone,
                    'address' => $this->vendor->address,
                    'location' => $this->extractLocation($this->vendor->address),
                ] : null;
            }),
            'seller' => $this->whenLoaded('vendor', function () use ($vendorMetrics) {
                return $this->vendor ? [
                    'id' => $this->vendor->id,
                    'uuid' => $this->vendor->uuid,
                    'slug' => Str::slug($this->vendor->business_name),
                    'name' => $this->vendor->business_name,
                    'logo' => null,
                    'verified' => $this->vendor->status === 'approved',
                    'location' => $this->extractLocation($this->vendor->address),
                    'rating' => $vendorMetrics['rating'],
                    'reviews_count' => $vendorMetrics['reviews_count'],
                    'sales_count' => $vendorMetrics['sales_count'],
                    'on_time_delivery_rate' => $vendorMetrics['on_time_delivery_rate'],
                    'response_time' => $vendorMetrics['response_time'],
                ] : null;
            }),
            'seller_rating' => $vendorMetrics['rating'],
            'seller_sales_count' => $vendorMetrics['sales_count'],
            'seller_on_time_delivery_rate' => $vendorMetrics['on_time_delivery_rate'],
            'seller_response_time' => $vendorMetrics['response_time'],
        ];
    }

    private function computeDiscountPercentage(): int
    {
        $compare = (float) ($this->compare_price ?? 0);
        $price = (float) ($this->price ?? 0);

        if ($compare <= 0 || $compare <= $price) {
            return 0;
        }

        return (int) round((($compare - $price) / $compare) * 100);
    }

    private function buildInstallments(): ?array
    {
        $price = (float) ($this->price ?? 0);
        if ($price <= 0) {
            return null;
        }

        $count = $price >= 500 ? 12 : 6;

        return [
            'count' => $count,
            'amount' => round($price / $count, 2),
        ];
    }

    private function resolveShipping(): array
    {
        $method = $this->vendor?->shippingMethods?->firstWhere('is_active', true);
        $rules = $method?->rules ?? [];

        $minDays = (int) (data_get($rules, 'eta_min_days') ?? 2);
        $maxDays = (int) (data_get($rules, 'eta_max_days') ?? 5);
        if ($maxDays < $minDays) {
            $maxDays = $minDays;
        }

        $freeShipping = $method
            ? ((float) $method->base_rate <= 0
                || Str::contains(Str::lower((string) $method->code), ['free', 'gratis']))
            : false;

        return [
            'free_shipping' => $freeShipping,
            'estimated_delivery' => "Llega entre {$minDays} y {$maxDays} dias",
        ];
    }

    private function resolveVendorMetrics(?int $vendorId): array
    {
        if (! $vendorId) {
            return [
                'rating' => 0,
                'reviews_count' => 0,
                'sales_count' => 0,
                'on_time_delivery_rate' => 0,
                'response_time' => '< 1h',
            ];
        }

        if (isset(self::$vendorMetricsCache[$vendorId])) {
            return self::$vendorMetricsCache[$vendorId];
        }

        $reviewsAgg = Review::query()
            ->join('vendor_products', 'vendor_products.product_id', '=', 'reviews.product_id')
            ->where('vendor_products.vendor_id', $vendorId)
            ->where('reviews.is_approved', true)
            ->selectRaw('COALESCE(AVG(reviews.rating), 0) as avg_rating, COUNT(reviews.id) as total_reviews')
            ->first();

        $salesCount = VendorOrder::query()
            ->where('vendor_id', $vendorId)
            ->count();

        $deliveredCount = VendorOrder::query()
            ->where('vendor_id', $vendorId)
            ->where('shipping_status', 'delivered')
            ->count();

        $onTime = $salesCount > 0
            ? (int) round(($deliveredCount / $salesCount) * 100)
            : 0;

        self::$vendorMetricsCache[$vendorId] = [
            'rating' => round((float) ($reviewsAgg?->avg_rating ?? 0), 1),
            'reviews_count' => (int) ($reviewsAgg?->total_reviews ?? 0),
            'sales_count' => $salesCount,
            'on_time_delivery_rate' => $onTime,
            'response_time' => '< 1h',
        ];

        return self::$vendorMetricsCache[$vendorId];
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
