<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'order_id' => $this->order_id,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status->value,
            'payment_date' => $this->payment_date?->toIso8601String(),
            'refund_date' => $this->refund_date?->toIso8601String(),
            'refund_amount' => $this->refund_amount !== null ? (float) $this->refund_amount : null,
            'gateway_response' => $this->gateway_response,
        ];
    }
}

