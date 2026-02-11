<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Cart\CheckoutService;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemsCreationTest extends TestCase
{
    use RefreshDatabase;

    protected CheckoutService $checkoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutService = app(CheckoutService::class);
    }

    /** @test */
    public function it_creates_order_items_with_product_snapshot()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'price' => 100.00,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $this->assertCount(1, $order->items);
        
        $orderItem = $order->items->first();
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertNull($orderItem->product_variant_id);
        $this->assertEquals('Test Product', $orderItem->product_name);
        $this->assertEquals('TEST-SKU-001', $orderItem->product_sku);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(100.00, $orderItem->price);
        $this->assertEquals(200.00, $orderItem->subtotal); // 2 * 100
        $this->assertGreaterThan(0, $orderItem->tax);
        $this->assertGreaterThan(0, $orderItem->total);
    }

    /** @test */
    public function it_creates_order_items_with_variant_snapshot()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create([
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-001',
            'price' => 50.00,
            'stock' => 0,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'name' => 'Large / Blue',
            'sku' => 'TSHIRT-001-L-BLUE',
            'price' => 55.00,
            'stock' => 5,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'price' => 55.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $this->assertCount(1, $order->items);
        
        $orderItem = $order->items->first();
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals($variant->id, $orderItem->product_variant_id);
        $this->assertEquals('T-Shirt - Large / Blue', $orderItem->product_name);
        $this->assertEquals('TSHIRT-001-L-BLUE', $orderItem->product_sku);
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(55.00, $orderItem->price);
        $this->assertEquals(55.00, $orderItem->subtotal);
        $this->assertGreaterThan(0, $orderItem->tax);
        $this->assertGreaterThan(0, $orderItem->total);
    }

    /** @test */
    public function it_creates_multiple_order_items_from_cart()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product1 = Product::factory()->create([
            'name' => 'Product 1',
            'sku' => 'PROD-001',
            'price' => 100.00,
            'stock' => 10,
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Product 2',
            'sku' => 'PROD-002',
            'price' => 50.00,
            'stock' => 5,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => 50.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $this->assertCount(2, $order->items);
        
        $orderItem1 = $order->items->where('product_id', $product1->id)->first();
        $this->assertEquals('Product 1', $orderItem1->product_name);
        $this->assertEquals(2, $orderItem1->quantity);
        $this->assertEquals(200.00, $orderItem1->subtotal);
        
        $orderItem2 = $order->items->where('product_id', $product2->id)->first();
        $this->assertEquals('Product 2', $orderItem2->product_name);
        $this->assertEquals(3, $orderItem2->quantity);
        $this->assertEquals(150.00, $orderItem2->subtotal);
    }

    /** @test */
    public function it_calculates_item_tax_correctly()
    {
        // Arrange
        config(['cart.tax_rate' => 0.10]); // 10% tax
        
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $orderItem = $order->items->first();
        $this->assertEquals(100.00, $orderItem->subtotal);
        $this->assertEquals(10.00, $orderItem->tax); // 10% of 100
        $this->assertEquals(110.00, $orderItem->total); // 100 + 10
    }

    /** @test */
    public function it_calculates_item_total_correctly()
    {
        // Arrange
        config(['cart.tax_rate' => 0.15]); // 15% tax
        
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create([
            'price' => 200.00,
            'stock' => 10,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 200.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $orderItem = $order->items->first();
        $this->assertEquals(600.00, $orderItem->subtotal); // 3 * 200
        $this->assertEquals(90.00, $orderItem->tax); // 15% of 600
        $this->assertEquals(690.00, $orderItem->total); // 600 + 90
    }
}
