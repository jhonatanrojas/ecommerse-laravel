<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Get all products.
     */
    public function getAll(array $filters = []): Collection|LengthAwarePaginator
    {
        return $this->productRepository->all($filters);
    }

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): Product
    {
        return $this->productRepository->update($product, $data);
    }

    /**
     * Delete a product.
     */
    public function delete(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }
}
