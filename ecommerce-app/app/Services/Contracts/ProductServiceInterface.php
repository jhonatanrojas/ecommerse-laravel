<?php

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface ProductServiceInterface
{
    public function getAllProducts(): Collection;
    
    public function getPaginatedProducts(int $perPage = 15, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator;
    
    public function getProductById(int $id): ?Product;
    
    public function getProductByUuid(string $uuid): ?Product;
    
    public function createProduct(array $data, ?array $images = null): Product;
    
    public function updateProduct(int $id, array $data, ?array $images = null): bool;
    
    public function deleteProduct(int $id): bool;
    
    public function getActiveProducts(): Collection;
    
    public function getFeaturedProducts(int $limit = 10): Collection;
    
    public function getLowStockProducts(): Collection;
    
    public function toggleStatus(int $id): bool;
    
    public function toggleFeatured(int $id): bool;
    
    public function uploadImage(UploadedFile $file, string $directory = 'products'): string;
    
    public function deleteImage(string $path): bool;
}
