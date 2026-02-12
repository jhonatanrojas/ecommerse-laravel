<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $precioOferta = null;
        if ($this->compare_price !== null && (float) $this->compare_price > (float) $this->price) {
            $precioOferta = $this->price;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'nombre' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'descripcion' => $this->description,
            'price' => $this->price,
            'precio' => $this->price,
            'precio_oferta' => $precioOferta,
            'stock' => $this->stock,
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
        ];
    }
}
