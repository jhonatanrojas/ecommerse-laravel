<?php

namespace App\Services\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function getAllCategories(): Collection;
    
    public function getPaginatedCategories(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
    
    public function getCategoryById(int $id): ?Category;
    
    public function createCategory(array $data): Category;
    
    public function updateCategory(int $id, array $data): bool;
    
    public function deleteCategory(int $id): bool;
    
    public function getActiveCategories(): Collection;
    
    public function toggleStatus(int $id): bool;
}
