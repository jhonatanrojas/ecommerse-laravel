<?php

namespace App\Services\Contracts;

use App\Enums\PaymentRecordStatus;
use App\Models\Payment;

/**
 * Interface PaymentStatusServiceInterface
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Servicio dedicado exclusivamente a la gestión de estados de pagos
 * 
 * SOLID: Interface Segregation Principle (ISP)
 * Interfaz específica para operaciones de estado de pagos
 */
interface PaymentStatusServiceInterface
{
    /**
     * Cambiar estado del pago
     */
    public function changeStatus(Payment $payment, PaymentRecordStatus $newStatus, ?string $adminNote = null): bool;
    
    /**
     * Validar si el cambio de estado es permitido
     */
    public function canChangeStatus(Payment $payment, PaymentRecordStatus $newStatus): bool;
    
    /**
     * Obtener estados disponibles para un pago
     */
    public function getAvailableStatuses(Payment $payment): array;
    
    /**
     * Obtener todos los estados posibles
     */
    public function getAllStatuses(): array;
}
