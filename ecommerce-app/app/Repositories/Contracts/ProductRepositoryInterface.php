<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get all products with optional filters.
     */
    public function all(array $filters = []): Collection|LengthAwarePaginator;

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product;

    /**
     * Create a new product.
     */
    public function create(array $data): Product;

    /**
     * Update a product.
     */
    public function update(Product $product, array $data): Product;

    /**
     * Delete a product.
     */
    public function delete(Product $product): bool;
}
