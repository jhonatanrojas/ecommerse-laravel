<?php

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Interface FileServiceInterface
 * 
 * Cumple con el Principio de Segregación de Interfaces (ISP):
 * Define solo los métodos necesarios para la gestión de archivos.
 * 
 * Cumple con el Principio de Inversión de Dependencias (DIP):
 * Permite que otros servicios dependan de esta abstracción.
 */
interface FileServiceInterface
{
    /**
     * Upload a file to storage.
     */
    public function upload(UploadedFile $file, string $path = 'uploads'): string;

    /**
     * Delete a file from storage.
     */
    public function delete(?string $filePath): bool;

    /**
     * Get the full URL of a file.
     */
    public function getUrl(?string $filePath): ?string;
}
