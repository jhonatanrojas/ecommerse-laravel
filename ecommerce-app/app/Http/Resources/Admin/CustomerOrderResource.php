<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order_number' => $this->order_number,
            'status' => $this->orderStatus?->slug ?? $this->status?->value,
            'status_label' => $this->orderStatus?->name ?? ucfirst((string) ($this->status?->value ?? '')),
            'shipping_status' => $this->shippingStatus?->name,
            'payment_status' => $this->payment_status?->value,
            'total' => (float) $this->total,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
