<?php

namespace App\Services\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface OrderServiceInterface
 * 
 * SOLID: Interface Segregation Principle (ISP)
 * Define solo los métodos de lógica de negocio para órdenes
 */
interface OrderServiceInterface
{
    /**
     * Obtener órdenes paginadas con filtros
     */
    public function getPaginatedOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Obtener orden por UUID
     */
    public function getOrderByUuid(string $uuid): ?Order;
    
    /**
     * Actualizar orden
     */
    public function updateOrder(string $uuid, array $data): bool;
    
    /**
     * Cancelar orden
     */
    public function cancelOrder(string $uuid, ?string $reason = null): bool;
    
    /**
     * Obtener estadísticas de órdenes
     */
    public function getOrderStatistics(): array;
}
