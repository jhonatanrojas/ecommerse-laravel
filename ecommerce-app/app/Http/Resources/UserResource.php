<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource formats user data for API responses.
 * 
 * Transforms user model data into a consistent JSON structure
 * including customer relationship when available.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'customer' => $this->whenLoaded('customer', fn () => new CustomerResource($this->customer)),
        ];
    }
}