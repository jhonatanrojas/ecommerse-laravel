<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * OrderItemResource formats order item data for API responses.
 * 
 * Transforms order item model data into a consistent JSON structure
 * including product information, quantity, pricing, and totals.
 */
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Returns order item data including product details, quantity,
     * price, subtotal, tax, and total amounts.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $primaryImage = null;

        if ($this->relationLoaded('product') && $this->product) {
            $images = $this->product->relationLoaded('images')
                ? $this->product->images
                : collect();

            $primaryImage = optional($images->firstWhere('is_primary', true))->url
                ?? optional($images->first())->url;
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'product' => $this->whenLoaded('product', function () use ($primaryImage) {
                return [
                    'id' => $this->product?->id,
                    'name' => $this->product?->name ?? $this->product_name,
                    'slug' => $this->product?->slug,
                    'image_url' => $primaryImage,
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
