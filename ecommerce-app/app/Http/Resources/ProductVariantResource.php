<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nombre' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'precio' => $this->price,
            'stock' => $this->stock,
            'attributes' => $this->attributes,
            'atributos' => $this->attributes,
        ];
    }
}
