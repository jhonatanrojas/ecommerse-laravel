<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function getPaginatedUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function getUserById(int $id): ?User;
    
    public function getUserByUuid(string $uuid): ?User;
    
    public function createUser(array $data): User;
    
    public function updateUser(int $id, array $data): bool;
    
    public function deleteUser(int $id): bool;
    
    public function toggleUserStatus(int $id): bool;
    
    public function assignRoles(int $userId, array $roleIds): bool;
    
    public function assignPermissions(int $userId, array $permissionIds): bool;
}
