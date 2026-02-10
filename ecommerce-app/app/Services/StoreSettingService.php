<?php

namespace App\Services;

use App\Models\StoreSetting;
use App\Repositories\Contracts\StoreSettingRepositoryInterface;
use App\Services\Contracts\FileServiceInterface;
use App\Services\Contracts\StoreSettingServiceInterface;
use Illuminate\Http\UploadedFile;

/**
 * Class StoreSettingService
 * 
 * Cumple con el Principio de Responsabilidad Única (SRP):
 * Solo se encarga de la lógica de negocio relacionada con los ajustes de la tienda.
 * 
 * Cumple con el Principio de Inversión de Dependencias (DIP):
 * Depende de abstracciones (interfaces) en lugar de implementaciones concretas.
 * 
 * Cumple con el Principio Abierto/Cerrado (OCP):
 * Puede extenderse con nuevas funcionalidades sin modificar el código existente.
 */
class StoreSettingService implements StoreSettingServiceInterface
{
    public function __construct(
        private StoreSettingRepositoryInterface $repository,
        private FileServiceInterface $fileService
    ) {}

    /**
     * Get the store settings.
     */
    public function getSettings(): ?StoreSetting
    {
        return $this->repository->getSettings();
    }

    /**
     * Update store settings with file handling.
     */
    public function updateSettings(array $data): bool
    {
        // Handle logo upload if present
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $settings = $this->getSettings();
            
            // Delete old logo if exists
            if ($settings && $settings->logo) {
                $this->fileService->delete($settings->logo);
            }

            // Upload new logo
            $data['logo'] = $this->fileService->upload($data['logo'], 'logos');
        } else {
            // Remove logo from data if not uploading a new one
            unset($data['logo']);
        }

        // Convert maintenance_mode checkbox to boolean
        $data['maintenance_mode'] = isset($data['maintenance_mode']) && $data['maintenance_mode'] === '1';

        return $this->repository->update($data);
    }

    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key): mixed
    {
        return $this->repository->getSetting($key);
    }
}
