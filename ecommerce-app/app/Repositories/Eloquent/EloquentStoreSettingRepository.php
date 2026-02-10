<?php

namespace App\Repositories\Eloquent;

use App\Models\StoreSetting;
use App\Repositories\Contracts\StoreSettingRepositoryInterface;

/**
 * Class EloquentStoreSettingRepository
 * 
 * Cumple con el Principio de Responsabilidad Única (SRP):
 * Solo se encarga de las operaciones de acceso a datos para store_settings.
 * 
 * Cumple con el Principio de Inversión de Dependencias (DIP):
 * Implementa la interfaz StoreSettingRepositoryInterface.
 */
class EloquentStoreSettingRepository implements StoreSettingRepositoryInterface
{
    /**
     * Get the store settings (singleton pattern - only one record).
     */
    public function getSettings(): ?StoreSetting
    {
        return StoreSetting::first();
    }

    /**
     * Update store settings.
     */
    public function update(array $data): bool
    {
        $settings = $this->getSettings();
        
        if (!$settings) {
            StoreSetting::create($data);
            return true;
        }

        return $settings->update($data);
    }

    /**
     * Get a specific setting value by key.
     */
    public function getSetting(string $key): mixed
    {
        $settings = $this->getSettings();
        return $settings?->$key;
    }
}
