<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        protected Category $model
    ) {}

    public function all(): Collection
    {
        return $this->model->ordered()->get();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model->query();

        if ($search) {
            $query->search($search);
        }

        return $query->orderBy('order')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->findById($id);
        
        if (!$category) {
            return false;
        }

        return $category->update($data);
    }

    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        
        if (!$category) {
            return false;
        }

        return $category->delete();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }
}
