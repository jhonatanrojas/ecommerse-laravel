<?php

namespace App\Services\Payments\Contracts;

use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * @return array{success: bool, status: string, transaction_id?: string|null, gateway_response?: array<string, mixed>}
     */
    public function charge(Payment $payment): array;

    /**
     * @return array{success: bool, status: string, refund_amount?: float, gateway_response?: array<string, mixed>}
     */
    public function refund(Payment $payment, float $amount): array;

    /**
     * @return array{status: string, transaction_id?: string|null, payment_uuid?: string|null, gateway_response?: array<string, mixed>}
     */
    public function handleWebhook(Request $request): array;
}

