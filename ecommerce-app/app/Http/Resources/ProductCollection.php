<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCollection extends ResourceCollection
{
    public $collects = ProductResource::class;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => ProductResource::collection($this->collection),
            'meta' => $this->paginationMeta(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function paginationMeta(): array
    {
        if (! $this->resource instanceof LengthAwarePaginator) {
            return [];
        }

        return [
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
            'per_page' => $this->resource->perPage(),
            'total' => $this->resource->total(),
            'from' => $this->resource->firstItem(),
            'to' => $this->resource->lastItem(),
        ];
    }
}

