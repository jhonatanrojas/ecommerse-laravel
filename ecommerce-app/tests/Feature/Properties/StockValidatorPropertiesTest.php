<?php

namespace Tests\Feature\Properties;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart\StockValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for StockValidator
 * 
 * These tests validate universal properties that should hold true
 * across many randomly generated inputs.
 */
class StockValidatorPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected StockValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new StockValidator();
    }

    /**
     * Feature: shopping-cart-logic, Property 12: Active product validation
     * 
     * For any product being added to a cart, if the product does not exist 
     * or is_active is false, the operation should be rejected.
     * 
     * Validates: Requirements 3.1
     */
    public function test_property_12_active_product_validation(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Test 1: Non-existent product should fail validation
            $nonExistentId = rand(999999, 9999999);
            $result = $this->validator->validateProduct($nonExistentId);
            
            $this->assertFalse(
                $result->isValid,
                "Property 12 violated: Non-existent product (ID: {$nonExistentId}) should fail validation"
            );
            $this->assertStringContainsString(
                'not found',
                $result->errorMessage,
                "Property 12 violated: Error message should indicate product not found"
            );

            // Test 2: Inactive product should fail validation
            $inactiveProduct = Product::factory()->create([
                'is_active' => false,
                'stock' => rand(1, 100),
            ]);
            
            $result = $this->validator->validateProduct($inactiveProduct->id);
            
            $this->assertFalse(
                $result->isValid,
                "Property 12 violated: Inactive product (ID: {$inactiveProduct->id}) should fail validation"
            );
            $this->assertStringContainsString(
                'not active',
                $result->errorMessage,
                "Property 12 violated: Error message should indicate product is not active"
            );

            // Test 3: Active product should pass validation
            $activeProduct = Product::factory()->create([
                'is_active' => true,
                'stock' => rand(1, 100),
            ]);
            
            $result = $this->validator->validateProduct($activeProduct->id);
            
            $this->assertTrue(
                $result->isValid,
                "Property 12 violated: Active product (ID: {$activeProduct->id}) should pass validation"
            );
            $this->assertNull(
                $result->errorMessage,
                "Property 12 violated: Successful validation should have no error message"
            );

            // Clean up for next iteration
            $inactiveProduct->forceDelete();
            $activeProduct->forceDelete();
        }
    }

    /**
     * Feature: shopping-cart-logic, Property 13: Active variant validation
     * 
     * For any variant being added to a cart, if the variant does not exist 
     * or its parent product's is_active is false, the operation should be rejected.
     * 
     * Validates: Requirements 3.2
     */
    public function test_property_13_active_variant_validation(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Test 1: Non-existent variant should fail validation
            $nonExistentId = rand(999999, 9999999);
            $result = $this->validator->validateVariant($nonExistentId);
            
            $this->assertFalse(
                $result->isValid,
                "Property 13 violated: Non-existent variant (ID: {$nonExistentId}) should fail validation"
            );
            $this->assertStringContainsString(
                'not found',
                $result->errorMessage,
                "Property 13 violated: Error message should indicate variant not found"
            );

            // Test 2: Variant with inactive parent product should fail validation
            $inactiveProduct = Product::factory()->create([
                'is_active' => false,
                'stock' => rand(1, 100),
            ]);
            
            $variant = ProductVariant::factory()->create([
                'product_id' => $inactiveProduct->id,
                'stock' => rand(1, 100),
            ]);
            
            $result = $this->validator->validateVariant($variant->id);
            
            $this->assertFalse(
                $result->isValid,
                "Property 13 violated: Variant (ID: {$variant->id}) with inactive parent should fail validation"
            );
            $this->assertStringContainsString(
                'not active',
                $result->errorMessage,
                "Property 13 violated: Error message should indicate parent product is not active"
            );

            // Test 3: Variant with active parent product should pass validation
            $activeProduct = Product::factory()->create([
                'is_active' => true,
                'stock' => rand(1, 100),
            ]);
            
            $activeVariant = ProductVariant::factory()->create([
                'product_id' => $activeProduct->id,
                'stock' => rand(1, 100),
            ]);
            
            $result = $this->validator->validateVariant($activeVariant->id);
            
            $this->assertTrue(
                $result->isValid,
                "Property 13 violated: Variant (ID: {$activeVariant->id}) with active parent should pass validation"
            );
            $this->assertNull(
                $result->errorMessage,
                "Property 13 violated: Successful validation should have no error message"
            );

            // Clean up for next iteration
            $variant->forceDelete();
            $inactiveProduct->forceDelete();
            $activeVariant->forceDelete();
            $activeProduct->forceDelete();
        }
    }

    /**
     * Feature: shopping-cart-logic, Property 14: Stock availability validation
     * 
     * For any item being added or updated in a cart, if the requested quantity 
     * exceeds available stock, the operation should be rejected.
     * 
     * Validates: Requirements 3.3
     */
    public function test_property_14_stock_availability_validation(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Generate random stock level
            $availableStock = rand(1, 50);
            
            // Test 1: Product stock validation - quantity exceeds stock
            $product = Product::factory()->create([
                'is_active' => true,
                'stock' => $availableStock,
            ]);
            
            $excessQuantity = $availableStock + rand(1, 20);
            $result = $this->validator->validateStock($product->id, null, $excessQuantity);
            
            $this->assertFalse(
                $result->isValid,
                "Property 14 violated: Quantity ({$excessQuantity}) exceeding stock ({$availableStock}) should fail validation"
            );
            $this->assertStringContainsString(
                'Insufficient stock',
                $result->errorMessage,
                "Property 14 violated: Error message should indicate insufficient stock"
            );
            $this->assertStringContainsString(
                "Requested: {$excessQuantity}",
                $result->errorMessage,
                "Property 14 violated: Error message should show requested quantity"
            );
            $this->assertStringContainsString(
                "Available: {$availableStock}",
                $result->errorMessage,
                "Property 14 violated: Error message should show available stock"
            );

            // Test 2: Product stock validation - quantity equals stock (should pass)
            $result = $this->validator->validateStock($product->id, null, $availableStock);
            
            $this->assertTrue(
                $result->isValid,
                "Property 14 violated: Quantity ({$availableStock}) equal to stock ({$availableStock}) should pass validation"
            );

            // Test 3: Product stock validation - quantity less than stock (should pass)
            $validQuantity = rand(1, max(1, $availableStock - 1));
            $result = $this->validator->validateStock($product->id, null, $validQuantity);
            
            $this->assertTrue(
                $result->isValid,
                "Property 14 violated: Quantity ({$validQuantity}) less than stock ({$availableStock}) should pass validation"
            );

            // Test 4: Variant stock validation - quantity exceeds stock
            $variantStock = rand(1, 50);
            $activeProduct = Product::factory()->create([
                'is_active' => true,
                'stock' => rand(100, 200), // Parent has more stock
            ]);
            
            $variant = ProductVariant::factory()->create([
                'product_id' => $activeProduct->id,
                'stock' => $variantStock,
            ]);
            
            $excessVariantQuantity = $variantStock + rand(1, 20);
            $result = $this->validator->validateStock($activeProduct->id, $variant->id, $excessVariantQuantity);
            
            $this->assertFalse(
                $result->isValid,
                "Property 14 violated: Variant quantity ({$excessVariantQuantity}) exceeding stock ({$variantStock}) should fail validation"
            );
            $this->assertStringContainsString(
                'Insufficient stock',
                $result->errorMessage,
                "Property 14 violated: Error message should indicate insufficient stock for variant"
            );

            // Test 5: Variant stock validation - quantity within stock (should pass)
            $validVariantQuantity = rand(1, $variantStock);
            $result = $this->validator->validateStock($activeProduct->id, $variant->id, $validVariantQuantity);
            
            $this->assertTrue(
                $result->isValid,
                "Property 14 violated: Variant quantity ({$validVariantQuantity}) within stock ({$variantStock}) should pass validation"
            );

            // Test 6: Zero stock should reject any positive quantity
            $zeroStockProduct = Product::factory()->create([
                'is_active' => true,
                'stock' => 0,
            ]);
            
            $result = $this->validator->validateStock($zeroStockProduct->id, null, 1);
            
            $this->assertFalse(
                $result->isValid,
                "Property 14 violated: Any quantity requested for zero stock product should fail validation"
            );

            // Clean up for next iteration
            $product->forceDelete();
            $variant->forceDelete();
            $activeProduct->forceDelete();
            $zeroStockProduct->forceDelete();
        }
    }
}
