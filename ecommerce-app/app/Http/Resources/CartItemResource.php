<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->price * $this->quantity,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'slug' => $this->product->slug,
                    'sku' => $this->product->sku,
                    'price' => $this->product->price,
                    'stock' => $this->product->stock,
                    'is_active' => $this->product->is_active,
                    'image' => $this->product->images->first()?->image_path,
                ];
            }),
            'variant' => $this->whenLoaded('variant', function () {
                return $this->variant ? [
                    'id' => $this->variant->id,
                    'name' => $this->variant->name,
                    'sku' => $this->variant->sku,
                    'price' => $this->variant->price,
                    'stock' => $this->variant->stock,
                ] : null;
            }),
        ];
    }
}
