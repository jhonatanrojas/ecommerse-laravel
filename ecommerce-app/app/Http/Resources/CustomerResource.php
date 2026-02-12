<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * CustomerResource formats customer data for API responses.
 * 
 * Transforms customer model data into a consistent JSON structure
 * for use in API responses.
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Returns customer data including id, phone, document, and birthdate.
     * Formats birthdate as Y-m-d when available.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'document' => $this->document,
            'birthdate' => $this->birthdate?->format('Y-m-d'),
        ];
    }
}