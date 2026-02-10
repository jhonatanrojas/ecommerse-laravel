<?php

namespace App\Services\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;

/**
 * Interface OrderStatusServiceInterface
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Servicio dedicado exclusivamente a la gestión de estados de órdenes
 * 
 * SOLID: Interface Segregation Principle (ISP)
 * Interfaz específica para operaciones de estado
 */
interface OrderStatusServiceInterface
{
    /**
     * Cambiar estado de la orden
     */
    public function changeStatus(Order $order, OrderStatus $newStatus): bool;
    
    /**
     * Validar si el cambio de estado es permitido
     */
    public function canChangeStatus(Order $order, OrderStatus $newStatus): bool;
    
    /**
     * Obtener estados disponibles para una orden
     */
    public function getAvailableStatuses(Order $order): array;
    
    /**
     * Obtener todos los estados posibles
     */
    public function getAllStatuses(): array;
}
