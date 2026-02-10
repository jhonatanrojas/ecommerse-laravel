<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface OrderRepositoryInterface
 * 
 * SOLID: Interface Segregation Principle (ISP)
 * Define solo los métodos necesarios para el acceso a datos de órdenes
 */
interface OrderRepositoryInterface
{
    /**
     * Obtener todas las órdenes con relaciones
     */
    public function all(): Collection;
    
    /**
     * Paginar órdenes con filtros opcionales
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    /**
     * Buscar orden por UUID
     */
    public function findByUuid(string $uuid): ?Order;
    
    /**
     * Buscar orden por número de orden
     */
    public function findByOrderNumber(string $orderNumber): ?Order;
    
    /**
     * Actualizar orden
     */
    public function update(string $uuid, array $data): bool;
    
    /**
     * Eliminar orden (soft delete)
     */
    public function delete(string $uuid): bool;
    
    /**
     * Obtener órdenes por estado
     */
    public function getByStatus(string $status): Collection;
    
    /**
     * Obtener órdenes por rango de fechas
     */
    public function getByDateRange(string $startDate, string $endDate): Collection;
    
    /**
     * Obtener órdenes por usuario
     */
    public function getByUser(int $userId): Collection;
}
