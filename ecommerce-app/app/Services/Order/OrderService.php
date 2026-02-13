<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\OrderStatusServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderService
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: lógica de negocio de órdenes
 * 
 * SOLID: Dependency Inversion Principle (DIP)
 * Depende de interfaces, no de implementaciones concretas
 * 
 * SOLID: Open/Closed Principle (OCP)
 * Abierto para extensión mediante inyección de dependencias
 */
class OrderService implements OrderServiceInterface
{
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected OrderStatusServiceInterface $statusService
    ) {}

    public function getPaginatedOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getOrderByUuid(string $uuid): ?Order
    {
        return $this->repository->findByUuid($uuid);
    }

    public function updateOrder(string $uuid, array $data): bool
    {
        try {
            DB::beginTransaction();

            // Auditoría
            if (Auth::check()) {
                $data['updated_by'] = Auth::user()->uuid;
            }

            $result = $this->repository->update($uuid, $data);

            if ($result) {
                Log::info('Orden actualizada', [
                    'order_uuid' => $uuid,
                    'updated_by' => $data['updated_by'] ?? null,
                ]);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar orden', [
                'order_uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function cancelOrder(string $uuid, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $order = $this->repository->findByUuid($uuid);

            if (!$order) {
                return false;
            }

            // Usar el servicio de estados para cambiar a cancelado
            $result = $this->statusService->changeStatus(
                $order,
                \App\Enums\OrderStatus::Cancelled
            );

            if ($result && $reason) {
                // Guardar razón de cancelación en notas
                $notes = $order->notes ? $order->notes . "\n\n" : '';
                $notes .= "Cancelado: " . $reason;
                
                $this->repository->update($uuid, ['notes' => $notes]);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cancelar orden', [
                'order_uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getOrderStatistics(): array
    {
        try {
            $allOrders = $this->repository->all();

            $statusCount = fn (string $slug) => $allOrders->filter(
                fn ($order) => ($order->orderStatus?->slug ?? $order->status?->value ?? (string) $order->status) === $slug
            )->count();

            return [
                'total' => $allOrders->count(),
                'pending' => $statusCount('pending'),
                'processing' => $statusCount('processing'),
                'shipped' => $statusCount('shipped'),
                'delivered' => $statusCount('delivered'),
                'cancelled' => $statusCount('cancelled'),
                'returned' => $statusCount('returned'),
                'total_revenue' => $allOrders->where('payment_status', 'paid')->sum('total'),
                'average_order_value' => $allOrders->where('payment_status', 'paid')->avg('total'),
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de órdenes', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
