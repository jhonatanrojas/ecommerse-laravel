<?php

namespace App\Services\Payments\Drivers;

use App\Enums\PaymentRecordStatus;
use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PayPalPaymentDriver implements PaymentGatewayInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private array $config = []
    ) {}

    public function charge(Payment $payment): array
    {
        $forceFailure = (bool) ($this->config['force_failure'] ?? config('payments.gateways.paypal.force_failure', false));
        $status = $forceFailure ? PaymentRecordStatus::Failed->value : PaymentRecordStatus::Completed->value;

        return [
            'success' => !$forceFailure,
            'status' => $status,
            'transaction_id' => 'paypal_'.Str::upper(Str::random(18)),
            'gateway_response' => [
                'provider' => 'paypal',
                'message' => $forceFailure ? 'Payment rejected by PayPal.' : 'Payment processed successfully.',
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
                'provider' => 'paypal',
                'message' => 'Refund processed successfully.',
                'is_total_refund' => $isTotalRefund,
            ],
        ];
    }

    public function handleWebhook(Request $request): array
    {
        $this->assertValidSignature($request);

        $payload = $request->json()->all();
        $eventType = (string) ($payload['event_type'] ?? '');
        $resource = $payload['resource'] ?? [];

        $status = match ($eventType) {
            'PAYMENT.CAPTURE.COMPLETED' => PaymentRecordStatus::Completed->value,
            'PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.DECLINED' => PaymentRecordStatus::Failed->value,
            'PAYMENT.CAPTURE.REFUNDED' => PaymentRecordStatus::Refunded->value,
            default => PaymentRecordStatus::Pending->value,
        };

        return [
            'status' => $status,
            'transaction_id' => $payload['transaction_id'] ?? $resource['id'] ?? null,
            'payment_uuid' => $resource['custom_id'] ?? null,
            'gateway_response' => $payload,
        ];
    }

    private function assertValidSignature(Request $request): void
    {
        $secret = (string) ($this->config['webhook_secret'] ?? config('payments.gateways.paypal.webhook_secret', ''));
        if ($secret === '') {
            return;
        }

        $signature = (string) $request->header('PayPal-Transmission-Sig');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new AccessDeniedHttpException('Invalid PayPal webhook signature.');
        }
    }
}
