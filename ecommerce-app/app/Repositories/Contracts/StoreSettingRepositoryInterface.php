<?php

namespace App\Repositories\Contracts;

use App\Models\StoreSetting;

/**
 * Interface StoreSettingRepositoryInterface
 * 
 * Cumple con el Principio de Inversión de Dependencias (DIP):
 * Define un contrato que permite desacoplar la lógica de negocio
 * de la implementación específica de acceso a datos.
 */
interface StoreSettingRepositoryInterface
{
    /**
     * Get the store settings (singleton pattern - only one record).
     */
    public function getSettings(): ?StoreSetting;

    /**
     * Update store settings.
     */
    public function update(array $data): bool;

    /**
     * Get a specific setting value by key.
     */
    public function getSetting(string $key): mixed;
}
