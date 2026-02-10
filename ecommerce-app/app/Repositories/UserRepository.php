<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('roles')->latest();

        // Filtro por búsqueda (nombre o email)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        // Filtro por estado
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', (bool) $filters['status']);
        }

        // Filtro por fecha
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?User
    {
        return $this->model->with('roles', 'permissions')->find($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->model->with('roles', 'permissions')->where('uuid', $uuid)->first();
    }

    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        // Solo hashear si se proporciona nueva contraseña
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function toggleStatus(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        $user->is_active = !$user->is_active;
        return $user->save();
    }

    public function getAllActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function syncRoles(User $user, array $roleIds): void
    {
        $user->syncRoles($roleIds);
    }

    public function syncPermissions(User $user, array $permissionIds): void
    {
        $user->syncPermissions($permissionIds);
    }
}
