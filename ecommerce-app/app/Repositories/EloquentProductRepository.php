<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        protected Product $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('category')->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'images']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->with(['category', 'images'])->find($id);
    }

    public function findByUuid(string $uuid): ?Product
    {
        return $this->model->with(['category', 'images'])->where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->with(['category', 'images'])->where('slug', $slug)->first();
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        
        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->with('category')->orderBy('name')->get();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->active()->featured()->with('category')->limit($limit)->get();
    }

    public function getLowStock(): Collection
    {
        return $this->model
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->with('category')
            ->orderBy('stock')
            ->get();
    }
}
