<?php

namespace App\Services\Payments;

use App\Enums\PaymentRecordStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Contracts\PaymentStatusServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class PaymentStatusService
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: gestión de estados de pagos
 */
class PaymentStatusService implements PaymentStatusServiceInterface
{
    /**
     * Matriz de transiciones de estado permitidas
     */
    protected array $allowedTransitions = [
        PaymentRecordStatus::Pending->value => [
            PaymentRecordStatus::Completed->value,
            PaymentRecordStatus::Failed->value,
        ],
        PaymentRecordStatus::Completed->value => [
            PaymentRecordStatus::Refunded->value,
            PaymentRecordStatus::PartiallyRefunded->value,
        ],
        PaymentRecordStatus::Failed->value => [
            PaymentRecordStatus::Pending->value,
        ],
        PaymentRecordStatus::PartiallyRefunded->value => [
            PaymentRecordStatus::Refunded->value,
        ],
        PaymentRecordStatus::Refunded->value => [],
    ];

    public function changeStatus(Payment $payment, PaymentRecordStatus $newStatus, ?string $adminNote = null): bool
    {
        if (!$this->canChangeStatus($payment, $newStatus)) {
            Log::warning('Intento de cambio de estado de pago no permitido', [
                'payment_id' => $payment->id,
                'current_status' => $payment->status->value,
                'new_status' => $newStatus->value,
            ]);
            return false;
        }

        try {
            DB::beginTransaction();

            $oldStatus = $payment->status;
            $payment->status = $newStatus;

            // Actualizar fechas según el estado
            if ($newStatus === PaymentRecordStatus::Completed && !$payment->payment_date) {
                $payment->payment_date = now();
            }

            if (in_array($newStatus, [PaymentRecordStatus::Refunded, PaymentRecordStatus::PartiallyRefunded]) && !$payment->refund_date) {
                $payment->refund_date = now();
            }

            // Agregar nota de auditoría en gateway_response
            $gatewayResponse = $payment->gateway_response ?? [];
            $gatewayResponse['admin_updates'] = $gatewayResponse['admin_updates'] ?? [];
            $gatewayResponse['admin_updates'][] = [
                'timestamp' => now()->toIso8601String(),
                'user' => Auth::check() ? Auth::user()->name : 'System',
                'user_uuid' => Auth::check() ? Auth::user()->uuid : null,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'note' => $adminNote,
            ];
            $payment->gateway_response = $gatewayResponse;

            $payment->save();

            // Sincronizar con el estado de la orden
            $this->syncOrderPaymentStatus($payment);

            DB::commit();

            Log::info('Estado de pago actualizado', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'admin_user' => Auth::check() ? Auth::user()->uuid : null,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar estado de pago', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function canChangeStatus(Payment $payment, PaymentRecordStatus $newStatus): bool
    {
        $currentStatus = $payment->status->value;
        
        // No se puede cambiar al mismo estado
        if ($currentStatus === $newStatus->value) {
            return false;
        }

        // Verificar si la transición está permitida
        return in_array($newStatus->value, $this->allowedTransitions[$currentStatus] ?? []);
    }

    public function getAvailableStatuses(Payment $payment): array
    {
        $currentStatus = $payment->status->value;
        $allowedValues = $this->allowedTransitions[$currentStatus] ?? [];

        return array_filter(
            PaymentRecordStatus::cases(),
            fn($status) => in_array($status->value, $allowedValues)
        );
    }

    public function getAllStatuses(): array
    {
        return PaymentRecordStatus::cases();
    }

    /**
     * Sincronizar el estado de pago de la orden con el estado del pago
     */
    protected function syncOrderPaymentStatus(Payment $payment): void
    {
        $order = $payment->order;
        if (!$order) {
            return;
        }

        // Mapear PaymentRecordStatus a PaymentStatus de la orden
        $orderPaymentStatus = match ($payment->status) {
            PaymentRecordStatus::Completed => PaymentStatus::Paid,
            PaymentRecordStatus::Failed => PaymentStatus::Failed,
            PaymentRecordStatus::Refunded => PaymentStatus::Refunded,
            PaymentRecordStatus::PartiallyRefunded => PaymentStatus::PartiallyRefunded,
            PaymentRecordStatus::Pending => PaymentStatus::Pending,
        };

        $order->payment_status = $orderPaymentStatus;
        $order->save();
    }
}
