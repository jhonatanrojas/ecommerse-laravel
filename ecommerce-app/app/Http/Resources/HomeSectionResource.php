<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource['uuid'],
            'type' => $this->resource['type'],
            'title' => $this->resource['title'],
            'display_order' => $this->resource['display_order'],
            'configuration' => $this->resource['configuration'],
            'rendered_data' => $this->resource['rendered_data'],
        ];
    }
}
