<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart\DTOs\ValidationResult;

class StockValidator
{
    /**
     * Validate that a product exists and is active.
     *
     * @param int $productId
     * @return ValidationResult
     */
    public function validateProduct(int $productId): ValidationResult
    {
        $product = Product::find($productId);

        if (!$product) {
            return ValidationResult::fail("Product with ID {$productId} not found.");
        }

        if (!$this->isProductActive($product)) {
            return ValidationResult::fail("Product '{$product->name}' is not active.");
        }

        return ValidationResult::success();
    }

    /**
     * Validate that a variant exists and its parent product is active.
     *
     * @param int $variantId
     * @return ValidationResult
     */
    public function validateVariant(int $variantId): ValidationResult
    {
        $variant = ProductVariant::with('product')->find($variantId);

        if (!$variant) {
            return ValidationResult::fail("Product variant with ID {$variantId} not found.");
        }

        if (!$variant->product) {
            return ValidationResult::fail("Parent product for variant ID {$variantId} not found.");
        }

        if (!$this->isProductActive($variant->product)) {
            return ValidationResult::fail("Parent product '{$variant->product->name}' for variant '{$variant->name}' is not active.");
        }

        return ValidationResult::success();
    }

    /**
     * Validate that the requested quantity does not exceed available stock.
     *
     * @param int $productId
     * @param int|null $variantId
     * @param int $quantity
     * @return ValidationResult
     */
    public function validateStock(int $productId, ?int $variantId, int $quantity): ValidationResult
    {
        $availableStock = $this->getAvailableStock($productId, $variantId);

        if ($quantity > $availableStock) {
            $itemName = $variantId 
                ? ProductVariant::find($variantId)?->name ?? "Variant ID {$variantId}"
                : Product::find($productId)?->name ?? "Product ID {$productId}";

            return ValidationResult::fail(
                "Insufficient stock for '{$itemName}'. Requested: {$quantity}, Available: {$availableStock}."
            );
        }

        return ValidationResult::success();
    }

    /**
     * Get the available stock for a product or variant.
     *
     * @param int $productId
     * @param int|null $variantId
     * @return int
     */
    public function getAvailableStock(int $productId, ?int $variantId): int
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            return $variant ? $variant->stock : 0;
        }

        $product = Product::find($productId);
        return $product ? $product->stock : 0;
    }

    /**
     * Check if a product is active.
     *
     * @param Product $product
     * @return bool
     */
    public function isProductActive(Product $product): bool
    {
        return $product->is_active === true;
    }
}
