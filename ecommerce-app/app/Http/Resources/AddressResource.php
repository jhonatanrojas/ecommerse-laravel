<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * AddressResource formats address data for API responses.
 * 
 * Transforms address model data into a consistent JSON structure
 * including all address fields and calculated is_default field.
 */
class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Returns all address fields including uuid, first_name, last_name,
     * company, phone, address lines, city, state, postal_code, country,
     * type, and is_default status.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'phone' => $this->phone,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'type' => $this->type->value,
            'is_default' => $this->is_default,
        ];
    }
}
