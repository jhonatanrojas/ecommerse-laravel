<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customer = $this->relationLoaded('customer') ? $this->customer : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'type' => $this->type?->value,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_default' => (bool) $this->is_default,
            'is_default_shipping' => $customer && (int) $customer->default_shipping_address_id === (int) $this->id,
            'is_default_billing' => $customer && (int) $customer->default_billing_address_id === (int) $this->id,
        ];
    }
}
