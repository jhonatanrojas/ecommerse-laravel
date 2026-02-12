<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * OrderResource formats order data for API responses.
 * 
 * Transforms order model data into a consistent JSON structure
 * including order items and shipping/billing addresses.
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Returns order data including id, order_number, status, totals,
     * timestamps, order items, and shipping/billing addresses.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'payment_status' => $this->payment_status->value,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'tax_amount' => $this->tax, // Alias for compatibility
            'shipping_cost' => $this->shipping_cost,
            'shipping_amount' => $this->shipping_cost, // Alias for compatibility
            'total' => $this->total,
            'coupon_code' => $this->coupon_code,
            'payment_method' => $this->payment_method,
            'shipping_method' => $this->shipping_method,
            'notes' => $this->notes,
            'customer_notes' => $this->customer_notes,
            'shipped_at' => $this->shipped_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'shipping_address' => new AddressResource($this->whenLoaded('shippingAddress')),
            'shippingAddress' => new AddressResource($this->whenLoaded('shippingAddress')), // Alias for compatibility
            'billing_address' => new AddressResource($this->whenLoaded('billingAddress')),
            'billingAddress' => new AddressResource($this->whenLoaded('billingAddress')), // Alias for compatibility
        ];
    }
}
