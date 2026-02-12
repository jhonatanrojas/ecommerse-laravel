<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedUsers(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($perPage, $filters);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByUuid(string $uuid): ?User
    {
        return $this->userRepository->findByUuid($uuid);
    }

    public function createUser(array $data): User
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->create($data);

            // Asignar roles si se proporcionan
            if (!empty($data['roles'])) {
                $this->userRepository->syncRoles($user, $data['roles']);
            }

            // Asignar permisos individuales si se proporcionan
            if (!empty($data['permissions'])) {
                $this->userRepository->syncPermissions($user, $data['permissions']);
            }

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUser(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->userRepository->update($id, $data);

            if (!$result) {
                DB::rollBack();
                return false;
            }

            $user = $this->getUserById($id);

            // Actualizar roles si se proporcionan
            if (isset($data['roles'])) {
                $this->userRepository->syncRoles($user, $data['roles']);
            }

            // Actualizar permisos individuales si se proporcionan
            if (isset($data['permissions'])) {
                $this->userRepository->syncPermissions($user, $data['permissions']);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser(int $id): bool
    {
        try {
            // Validar que no sea el usuario actual
            if (auth()->id() === $id) {
                throw new \Exception('No puedes eliminar tu propio usuario.');
            }

            return $this->userRepository->delete($id);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function toggleUserStatus(int $id): bool
    {
        try {
            // Validar que no sea el usuario actual
            if (auth()->id() === $id) {
                throw new \Exception('No puedes desactivar tu propio usuario.');
            }

            return $this->userRepository->toggleStatus($id);
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            throw $e;
        }
    }

    public function assignRoles(int $userId, array $roleIds): bool
    {
        try {
            $user = $this->getUserById($userId);

            if (!$user) {
                return false;
            }

            $this->userRepository->syncRoles($user, $roleIds);

            return true;
        } catch (\Exception $e) {
            Log::error('Error assigning roles: ' . $e->getMessage());
            throw $e;
        }
    }

    public function assignPermissions(int $userId, array $permissionIds): bool
    {
        try {
            $user = $this->getUserById($userId);

            if (!$user) {
                return false;
            }

            $this->userRepository->syncPermissions($user, $permissionIds);

            return true;
        } catch (\Exception $e) {
            Log::error('Error assigning permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update user password with current password validation.
     *
     * @param User $user The user whose password will be updated
     * @param string $currentPassword The current password for validation
     * @param string $newPassword The new password to set
     * @return bool True if password was updated successfully
     * @throws \Exception If current password is invalid or update fails
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        try {
            // Validate current password
            if (!$this->validateCurrentPassword($user, $currentPassword)) {
                throw new \Exception('La contraseña actual es incorrecta.');
            }

            // Update password
            $user->password = Hash::make($newPassword);
            $user->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate the current password for a user.
     *
     * @param User $user The user to validate password for
     * @param string $password The password to validate
     * @return bool True if password is valid
     */
    private function validateCurrentPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
