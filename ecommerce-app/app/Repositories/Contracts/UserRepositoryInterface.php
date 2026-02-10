<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function findById(int $id): ?User;
    
    public function findByUuid(string $uuid): ?User;
    
    public function create(array $data): User;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function toggleStatus(int $id): bool;
    
    public function getAllActive(): Collection;
    
    public function syncRoles(User $user, array $roleIds): void;
    
    public function syncPermissions(User $user, array $permissionIds): void;
}
