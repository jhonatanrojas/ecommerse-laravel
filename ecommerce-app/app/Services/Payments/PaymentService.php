<?php

namespace App\Services\Payments;

use App\Enums\PaymentRecordStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private PaymentGatewayFactory $paymentGatewayFactory
    ) {}

    public function createPayment(Order $order, string $method): Payment
    {
        return DB::transaction(function () use ($order, $method) {
            return Payment::create([
                'order_id' => $order->id,
                'payment_method' => strtolower(trim($method)),
                'amount' => $order->total,
                'currency' => 'USD',
                'status' => PaymentRecordStatus::Pending->value,
            ]);
        });
    }

    public function processPayment(Payment $payment): Payment
    {
        $driver = $this->paymentGatewayFactory->make($payment->payment_method);

        try {
            $response = $driver->charge($payment);
            $status = (string) ($response['status'] ?? PaymentRecordStatus::Failed->value);
            $this->updateStatus($payment, $status, (array) ($response['gateway_response'] ?? []));

            if (!empty($response['transaction_id'])) {
                $payment->transaction_id = (string) $response['transaction_id'];
                $payment->save();
            }
        } catch (\Throwable $exception) {
            $this->updateStatus(
                $payment,
                PaymentRecordStatus::Failed->value,
                [
                    'error' => $exception->getMessage(),
                    'provider' => $payment->payment_method,
                ]
            );
        }

        return $payment->fresh(['order']);
    }

    public function updateStatus(Payment $payment, string $status, array $gatewayResponse): Payment
    {
        $paymentStatus = PaymentRecordStatus::tryFrom($status) ?? PaymentRecordStatus::Pending;

        return DB::transaction(function () use ($payment, $paymentStatus, $gatewayResponse) {
            $currentGatewayResponse = (array) ($payment->gateway_response ?? []);
            $payment->status = $paymentStatus->value;
            $payment->gateway_response = array_merge($currentGatewayResponse, $gatewayResponse);

            if ($paymentStatus === PaymentRecordStatus::Completed) {
                $payment->payment_date = now();
            }

            $payment->save();

            $order = $payment->order()->lockForUpdate()->first();
            if (!$order) {
                return $payment;
            }

            if ($paymentStatus === PaymentRecordStatus::Completed) {
                $order->payment_status = PaymentStatus::Paid;
                $order->payment_method = $payment->payment_method;
            }

            if ($paymentStatus === PaymentRecordStatus::Failed) {
                $order->payment_status = PaymentStatus::Failed;
            }

            if ($paymentStatus === PaymentRecordStatus::Refunded) {
                $order->payment_status = PaymentStatus::Refunded;
            }

            if ($paymentStatus === PaymentRecordStatus::PartiallyRefunded) {
                $order->payment_status = PaymentStatus::Paid;
            }

            $order->save();

            return $payment;
        });
    }

    public function refund(Payment $payment, float $amount): Payment
    {
        if (!in_array($payment->status->value, [
            PaymentRecordStatus::Completed->value,
            PaymentRecordStatus::PartiallyRefunded->value,
        ], true)) {
            throw new \DomainException('Only completed payments can be refunded.');
        }

        $alreadyRefunded = (float) ($payment->refund_amount ?? 0);
        $maxRefundable = round((float) $payment->amount - $alreadyRefunded, 2);
        if ($amount <= 0 || $amount > $maxRefundable) {
            throw new \DomainException('Invalid refund amount.');
        }

        $driver = $this->paymentGatewayFactory->make($payment->payment_method);
        $response = $driver->refund($payment, $amount);
        if (!(bool) ($response['success'] ?? false)) {
            throw new \DomainException('The payment gateway rejected the refund.');
        }

        $newRefundTotal = round($alreadyRefunded + $amount, 2);
        $status = $newRefundTotal >= (float) $payment->amount
            ? PaymentRecordStatus::Refunded->value
            : PaymentRecordStatus::PartiallyRefunded->value;

        DB::transaction(function () use ($payment, $newRefundTotal, $status, $response) {
            $payment->refund_amount = $newRefundTotal;
            $payment->refund_date = now();
            $payment->gateway_response = array_merge(
                (array) ($payment->gateway_response ?? []),
                (array) ($response['gateway_response'] ?? []),
                ['last_refund' => ['amount' => $response['refund_amount'] ?? null]]
            );
            $payment->save();

            $this->updateStatus($payment, $status, (array) ($response['gateway_response'] ?? []));
        });

        return $payment->fresh(['order']);
    }

    public function handleWebhook(string $paymentMethod, array $payload): ?Payment
    {
        $query = Payment::query();

        if (!empty($payload['transaction_id'])) {
            $query->where('transaction_id', $payload['transaction_id']);
        } elseif (!empty($payload['payment_uuid'])) {
            $query->where('uuid', $payload['payment_uuid']);
        } else {
            return null;
        }

        $payment = $query->first();
        if (!$payment) {
            return null;
        }

        if (strtolower($payment->payment_method) !== strtolower($paymentMethod)) {
            return null;
        }

        $this->updateStatus(
            $payment,
            (string) ($payload['status'] ?? PaymentRecordStatus::Pending->value),
            (array) ($payload['gateway_response'] ?? [])
        );

        return $payment->fresh(['order']);
    }
}
