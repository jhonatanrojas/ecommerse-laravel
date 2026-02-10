<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Contracts\OrderStatusServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderStatusService
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: gestión de estados de órdenes
 * 
 * SOLID: Open/Closed Principle (OCP)
 * Abierto para extensión: se pueden agregar nuevas reglas de transición sin modificar el código existente
 * 
 * SOLID: Dependency Inversion Principle (DIP)
 * Implementa la interfaz OrderStatusServiceInterface
 */
class OrderStatusService implements OrderStatusServiceInterface
{
    /**
     * Matriz de transiciones de estado permitidas
     * 
     * SOLID: Open/Closed Principle (OCP)
     * Para agregar nuevas transiciones, solo se modifica este array
     */
    protected array $allowedTransitions = [
        OrderStatus::Pending->value => [
            OrderStatus::Processing->value,
            OrderStatus::Cancelled->value,
        ],
        OrderStatus::Processing->value => [
            OrderStatus::Shipped->value,
            OrderStatus::Cancelled->value,
        ],
        OrderStatus::Shipped->value => [
            OrderStatus::Delivered->value,
            OrderStatus::Returned->value,
        ],
        OrderStatus::Delivered->value => [
            OrderStatus::Returned->value,
        ],
        OrderStatus::Cancelled->value => [],
        OrderStatus::Returned->value => [],
    ];

    public function changeStatus(Order $order, OrderStatus $newStatus): bool
    {
        if (!$this->canChangeStatus($order, $newStatus)) {
            Log::warning('Intento de cambio de estado no permitido', [
                'order_id' => $order->id,
                'current_status' => $order->status->value,
                'new_status' => $newStatus->value,
            ]);
            return false;
        }

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $order->status = $newStatus;

            // Actualizar timestamps específicos según el estado
            $this->updateStatusTimestamps($order, $newStatus);

            // Auditoría
            if (Auth::check()) {
                $order->updated_by = Auth::user()->uuid;
            }

            $order->save();

            Log::info('Estado de orden actualizado', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'updated_by' => $order->updated_by,
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar estado de orden', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function canChangeStatus(Order $order, OrderStatus $newStatus): bool
    {
        $currentStatus = $order->status->value;
        $targetStatus = $newStatus->value;

        // No se puede cambiar al mismo estado
        if ($currentStatus === $targetStatus) {
            return false;
        }

        // Verificar si la transición está permitida
        return in_array($targetStatus, $this->allowedTransitions[$currentStatus] ?? []);
    }

    public function getAvailableStatuses(Order $order): array
    {
        $currentStatus = $order->status->value;
        $allowedStatusValues = $this->allowedTransitions[$currentStatus] ?? [];

        return array_map(
            fn($statusValue) => OrderStatus::from($statusValue),
            $allowedStatusValues
        );
    }

    public function getAllStatuses(): array
    {
        return OrderStatus::cases();
    }

    /**
     * Actualizar timestamps específicos según el estado
     * 
     * SOLID: Single Responsibility Principle (SRP)
     * Método privado con responsabilidad única
     */
    protected function updateStatusTimestamps(Order $order, OrderStatus $status): void
    {
        match ($status) {
            OrderStatus::Shipped => $order->shipped_at = now(),
            OrderStatus::Delivered => $order->delivered_at = now(),
            OrderStatus::Cancelled => $order->cancelled_at = now(),
            default => null,
        };
    }
}
