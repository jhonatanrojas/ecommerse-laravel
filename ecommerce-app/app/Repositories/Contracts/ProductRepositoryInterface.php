<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator;
    
    public function findById(int $id): ?Product;
    
    public function findByUuid(string $uuid): ?Product;
    
    public function findBySlug(string $slug): ?Product;
    
    public function findBySku(string $sku): ?Product;
    
    public function create(array $data): Product;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getActive(): Collection;
    
    public function getFeatured(int $limit = 10): Collection;
    
    public function getLowStock(): Collection;
}
