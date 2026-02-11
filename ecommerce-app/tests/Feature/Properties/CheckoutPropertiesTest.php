<?php

namespace Tests\Feature\Properties;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Cart\CheckoutService;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for Checkout Process
 * 
 * These tests validate universal properties that should hold true
 * across many randomly generated inputs during checkout.
 */
class CheckoutPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected CheckoutService $checkoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutService = app(CheckoutService::class);
    }

    /**
     * Feature: shopping-cart-logic, Property 29: Stock decrement on checkout
     * 
     * For any product or variant in a cart being checked out with quantity Q, 
     * the product/variant's stock should decrease by Q after successful checkout.
     * 
     * Validates: Requirements 7.3
     */
    public function test_property_29_stock_decrement_on_checkout(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Create a user for the cart
            $user = User::factory()->create();
            $cart = Cart::factory()->create(['user_id' => $user->id]);

            // Test 1: Single product checkout
            $initialStock = rand(10, 100);
            $quantity = rand(1, min(10, $initialStock));
            
            $product = Product::factory()->create([
                'is_active' => true,
                'stock' => $initialStock,
                'price' => rand(10, 100),
            ]);

            CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'product_variant_id' => null,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);

            $checkoutData = new CheckoutData(
                shippingAddressId: null,
                billingAddressId: null,
                paymentMethod: 'credit_card',
                shippingMethod: 'standard',
                customerNotes: null
            );

            // Perform checkout
            $order = $this->checkoutService->processCheckout($cart, $checkoutData);

            // Verify stock was decremented
            $product->refresh();
            $expectedStock = $initialStock - $quantity;
            
            $this->assertEquals(
                $expectedStock,
                $product->stock,
                "Property 29 violated: Product stock should decrease by {$quantity} (from {$initialStock} to {$expectedStock}), but got {$product->stock}"
            );

            // No manual cleanup needed - RefreshDatabase will handle it
        }
    }

    /**
     * Feature: shopping-cart-logic, Property 29: Stock decrement on checkout (Variants)
     * 
     * For any variant in a cart being checked out with quantity Q, 
     * the variant's stock should decrease by Q after successful checkout.
     * 
     * Validates: Requirements 7.3
     */
    public function test_property_29_variant_stock_decrement_on_checkout(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Create a user for the cart
            $user = User::factory()->create();
            $cart = Cart::factory()->create(['user_id' => $user->id]);

            // Test: Variant checkout
            $initialVariantStock = rand(10, 100);
            $quantity = rand(1, min(10, $initialVariantStock));
            
            $product = Product::factory()->create([
                'is_active' => true,
                'stock' => rand(100, 200), // Parent has more stock
                'price' => rand(10, 100),
            ]);

            $variant = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'stock' => $initialVariantStock,
                'price' => rand(10, 100),
            ]);

            CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price,
            ]);

            $checkoutData = new CheckoutData(
                shippingAddressId: null,
                billingAddressId: null,
                paymentMethod: 'credit_card',
                shippingMethod: 'standard',
                customerNotes: null
            );

            // Perform checkout
            $order = $this->checkoutService->processCheckout($cart, $checkoutData);

            // Verify variant stock was decremented
            $variant->refresh();
            $expectedStock = $initialVariantStock - $quantity;
            
            $this->assertEquals(
                $expectedStock,
                $variant->stock,
                "Property 29 violated: Variant stock should decrease by {$quantity} (from {$initialVariantStock} to {$expectedStock}), but got {$variant->stock}"
            );

            // No manual cleanup needed - RefreshDatabase will handle it
        }
    }

    /**
     * Feature: shopping-cart-logic, Property 29: Stock decrement on checkout (Multiple Items)
     * 
     * For any cart with multiple items being checked out, each product/variant's 
     * stock should decrease by its respective quantity after successful checkout.
     * 
     * Validates: Requirements 7.3
     */
    public function test_property_29_multiple_items_stock_decrement_on_checkout(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 50; $i++) {
            // Create a user for the cart
            $user = User::factory()->create();
            $cart = Cart::factory()->create(['user_id' => $user->id]);

            // Create multiple products with random quantities
            $itemCount = rand(2, 5);
            $products = [];
            $initialStocks = [];
            $quantities = [];

            for ($j = 0; $j < $itemCount; $j++) {
                $initialStock = rand(10, 100);
                $quantity = rand(1, min(10, $initialStock));
                
                $uniqueId = uniqid() . '-' . $i . '-' . $j;
                $product = Product::factory()->create([
                    'name' => 'Product ' . $uniqueId,
                    'slug' => 'product-' . $uniqueId, // Explicitly set unique slug
                    'sku' => 'SKU-' . $uniqueId,
                    'is_active' => true,
                    'stock' => $initialStock,
                    'price' => rand(10, 100),
                ]);

                // Create cart item without using factory to avoid duplicate product creation
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $products[] = $product;
                $initialStocks[] = $initialStock;
                $quantities[] = $quantity;
            }

            $checkoutData = new CheckoutData(
                shippingAddressId: null,
                billingAddressId: null,
                paymentMethod: 'credit_card',
                shippingMethod: 'standard',
                customerNotes: null
            );

            // Perform checkout
            $order = $this->checkoutService->processCheckout($cart, $checkoutData);

            // Verify each product's stock was decremented correctly
            for ($j = 0; $j < $itemCount; $j++) {
                $products[$j]->refresh();
                $expectedStock = $initialStocks[$j] - $quantities[$j];
                
                $this->assertEquals(
                    $expectedStock,
                    $products[$j]->stock,
                    "Property 29 violated: Product {$j} stock should decrease by {$quantities[$j]} (from {$initialStocks[$j]} to {$expectedStock}), but got {$products[$j]->stock}"
                );
            }

            // No manual cleanup needed - RefreshDatabase will handle it
        }
    }
}
