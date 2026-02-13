<?php

namespace App\Services\Payments\Drivers;

use App\Enums\PaymentRecordStatus;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MercadoPagoPaymentDriver implements PaymentGatewayInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private array $config = []
    ) {}

    public function charge(Payment $payment): array
    {
        $forceFailure = (bool) ($this->config['force_failure'] ?? config('payments.gateways.mercadopago.force_failure', false));
        $status = $forceFailure ? PaymentRecordStatus::Failed->value : PaymentRecordStatus::Completed->value;

        return [
            'success' => !$forceFailure,
            'status' => $status,
            'transaction_id' => 'mp_'.Str::upper(Str::random(18)),
            'gateway_response' => [
                'provider' => 'mercadopago',
                'message' => $forceFailure ? 'Payment rejected by Mercado Pago.' : 'Payment processed successfully.',
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
                'provider' => 'mercadopago',
                'message' => 'Refund processed successfully.',
                'is_total_refund' => $isTotalRefund,
            ],
        ];
    }

    public function handleWebhook(Request $request): array
    {
        $this->assertValidSignature($request);

        $payload = $request->json()->all();
        $data = $payload['data'] ?? [];

        $rawStatus = (string) ($payload['status'] ?? $data['status'] ?? '');
        $status = match ($rawStatus) {
            'approved' => PaymentRecordStatus::Completed->value,
            'rejected', 'cancelled' => PaymentRecordStatus::Failed->value,
            'refunded' => PaymentRecordStatus::Refunded->value,
            default => PaymentRecordStatus::Pending->value,
        };

        return [
            'status' => $status,
            'transaction_id' => $payload['transaction_id'] ?? $data['id'] ?? null,
            'payment_uuid' => $data['external_reference'] ?? null,
            'gateway_response' => $payload,
        ];
    }

    private function assertValidSignature(Request $request): void
    {
        $secret = (string) ($this->config['webhook_secret'] ?? config('payments.gateways.mercadopago.webhook_secret', ''));
        if ($secret === '') {
            return;
        }

        $signature = (string) $request->header('X-Signature');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new AccessDeniedHttpException('Invalid Mercado Pago webhook signature.');
        }
    }
}
