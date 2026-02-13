<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class EloquentOrderRepository
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: acceso a datos de órdenes
 * 
 * SOLID: Dependency Inversion Principle (DIP)
 * Implementa la interfaz OrderRepositoryInterface
 */
class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        protected Order $model
    ) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['user', 'items.product', 'shippingAddress', 'billingAddress', 'orderStatus', 'shippingStatus'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['user', 'items.product', 'shippingAddress', 'billingAddress', 'orderStatus', 'shippingStatus']);

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query->where(function ($statusQuery) use ($filters) {
                $statusQuery
                    ->whereHas('orderStatus', function ($q) use ($filters) {
                        $q->where('slug', $filters['status']);
                    })
                    ->orWhere(function ($q) use ($filters) {
                        $q->whereNull('order_status_id')
                            ->where('status', $filters['status']);
                    });
                });
        }

        // Filtro por rango de fechas
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Filtro por cliente (búsqueda por nombre o email)
        if (!empty($filters['customer'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['customer'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['customer'] . '%');
            });
        }

        // Filtro por número de orden
        if (!empty($filters['order_number'])) {
            $query->where('order_number', 'like', '%' . $filters['order_number'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Order
    {
        return $this->model
            ->with(['user', 'items.product', 'shippingAddress', 'billingAddress', 'payments', 'orderStatus', 'shippingStatus'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model
            ->with(['user', 'items.product', 'shippingAddress', 'billingAddress', 'orderStatus', 'shippingStatus'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function update(string $uuid, array $data): bool
    {
        $order = $this->model->where('uuid', $uuid)->first();
        
        if (!$order) {
            return false;
        }

        return $order->update($data);
    }

    public function delete(string $uuid): bool
    {
        $order = $this->model->where('uuid', $uuid)->first();
        
        if (!$order) {
            return false;
        }

        return $order->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model
            ->with(['user', 'items.product', 'orderStatus'])
            ->whereHas('orderStatus', fn ($q) => $q->where('slug', $status))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->with(['user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return $this->model
            ->with(['items.product', 'shippingAddress', 'billingAddress', 'orderStatus', 'shippingStatus'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
