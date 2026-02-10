<?php

namespace App\Services;

use App\Services\Contracts\FileServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileService
 * 
 * Cumple con el Principio de Responsabilidad Única (SRP):
 * Solo se encarga de la gestión de archivos (subida, eliminación, URLs).
 * 
 * Cumple con el Principio Abierto/Cerrado (OCP):
 * Puede extenderse para soportar diferentes drivers de almacenamiento
 * sin modificar el código existente.
 */
class FileService implements FileServiceInterface
{
    /**
     * Upload a file to storage.
     */
    public function upload(UploadedFile $file, string $path = 'uploads'): string
    {
        return $file->store($path, 'public');
    }

    /**
     * Delete a file from storage.
     */
    public function delete(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        return Storage::disk('public')->delete($filePath);
    }

    /**
     * Get the full URL of a file.
     */
    public function getUrl(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        return asset('storage/' . $filePath);
    }
}
