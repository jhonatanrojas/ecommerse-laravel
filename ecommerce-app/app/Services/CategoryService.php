<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    ) {}

    public function getAllCategories(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedCategories(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    public function createCategory(array $data): Category
    {
        try {
            DB::beginTransaction();

            // Agregar auditoría
            if (Auth::check()) {
                $data['created_by'] = Auth::user()->uuid;
            }

            $category = $this->repository->create($data);

            Log::info('Categoría creada', [
                'category_id' => $category->id,
                'name' => $category->name,
                'created_by' => $data['created_by'] ?? null
            ]);

            DB::commit();

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear categoría', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateCategory(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();

            // Agregar auditoría
            if (Auth::check()) {
                $data['updated_by'] = Auth::user()->uuid;
            }

            $result = $this->repository->update($id, $data);

            if ($result) {
                Log::info('Categoría actualizada', [
                    'category_id' => $id,
                    'updated_by' => $data['updated_by'] ?? null
                ]);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar categoría', ['category_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteCategory(int $id): bool
    {
        try {
            DB::beginTransaction();

            // Agregar auditoría antes de eliminar
            if (Auth::check()) {
                $this->repository->update($id, ['deleted_by' => Auth::user()->uuid]);
            }

            $result = $this->repository->delete($id);

            if ($result) {
                Log::info('Categoría eliminada', ['category_id' => $id]);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar categoría', ['category_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getActiveCategories(): Collection
    {
        return $this->repository->getActive();
    }

    public function toggleStatus(int $id): bool
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return false;
        }

        return $this->updateCategory($id, ['is_active' => !$category->is_active]);
    }
}
