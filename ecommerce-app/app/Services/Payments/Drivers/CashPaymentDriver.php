<?php

namespace App\Services\Payments\Drivers;

use App\Enums\PaymentRecordStatus;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CashPaymentDriver implements PaymentGatewayInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private array $config = []
    ) {}

    public function charge(Payment $payment): array
    {
        $autoApprove = (bool) ($this->config['auto_approve'] ?? true);

        return [
            'success' => $autoApprove,
            'status' => $autoApprove
                ? PaymentRecordStatus::Completed->value
                : PaymentRecordStatus::Pending->value,
            'transaction_id' => 'cash_'.Str::upper(Str::random(12)),
            'gateway_response' => [
                'provider' => 'cash',
                'message' => $autoApprove
                    ? 'Cash payment marked as completed.'
                    : 'Cash payment pending manual confirmation.',
            ],
        ];
    }

    public function refund(Payment $payment, float $amount): array
    {
        $alreadyRefunded = (float) ($payment->refund_amount ?? 0);
        $newTotal = round($alreadyRefunded + $amount, 2);
        $isTotalRefund = $newTotal >= (float) $payment->amount;

        return [
            'success' => true,
            'status' => $isTotalRefund
                ? PaymentRecordStatus::Refunded->value
                : PaymentRecordStatus::PartiallyRefunded->value,
            'refund_amount' => $amount,
            'gateway_response' => [
                'provider' => 'cash',
                'message' => 'Cash refund registered.',
                'is_total_refund' => $isTotalRefund,
            ],
        ];
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'status' => PaymentRecordStatus::Pending->value,
            'transaction_id' => null,
            'payment_uuid' => null,
            'gateway_response' => [
                'provider' => 'cash',
                'message' => 'Cash does not use webhooks.',
            ],
        ];
    }
}

