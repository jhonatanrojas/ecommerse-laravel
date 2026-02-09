<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * Get all products.
     */
    public function all(array $filters = []);

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
