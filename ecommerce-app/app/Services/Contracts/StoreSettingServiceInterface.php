<?php

namespace App\Services\Contracts;

use App\Models\StoreSetting;

/**
 * Interface StoreSettingServiceInterface
 * 
 * Cumple con el Principio de Inversión de Dependencias (DIP):
 * Define un contrato que permite desacoplar el controlador
 * de la implementación específica de la lógica de negocio.
 * 
 * Cumple con el Principio de Segregación de Interfaces (ISP):
 * Define solo los métodos necesarios para la gestión de ajustes.
 */
interface StoreSettingServiceInterface
{
    /**
     * Get the store settings.
     */
    public function getSettings(): ?StoreSetting;

    /**
     * Update store settings with file handling.
     */
    public function updateSettings(array $data): bool;

    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key): mixed;
}
