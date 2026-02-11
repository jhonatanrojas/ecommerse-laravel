<?php

namespace Tests\Feature\Cart;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CheckoutService;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected CheckoutService $checkoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutService = app(CheckoutService::class);
    }

    /** @test */
    public function it_creates_order_with_correct_data_from_cart()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);

        $product1 = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 50.00, 'stock' => 5]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 50.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: 'Test order'
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $this->assertNotNull($order);
        $this->assertNotNull($order->order_number);
        $this->assertTrue(str_starts_with($order->order_number, 'ORD-'));
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(PaymentStatus::Pending, $order->payment_status);
        $this->assertEquals(250.00, $order->subtotal); // 2*100 + 1*50
        $this->assertEquals(0, $order->discount);
        $this->assertGreaterThan(0, $order->tax);
        $this->assertGreaterThanOrEqual(0, $order->shipping_cost);
        $this->assertGreaterThan(0, $order->total);
        $this->assertEquals('credit_card', $order->payment_method);
        $this->assertEquals('standard', $order->shipping_method);
        $this->assertEquals('Test order', $order->customer_notes);
    }

    /** @test */
    public function it_creates_order_with_coupon_code_when_applied()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'coupon_code' => 'SAVE10',
            'discount_amount' => 10.00,
        ]);

        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);

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
        $this->assertEquals('SAVE10', $order->coupon_code);
        $this->assertEquals(10.00, $order->discount);
        $this->assertEquals(100.00, $order->subtotal);
    }

    /** @test */
    public function it_generates_unique_order_numbers()
    {
        // Arrange
        $user = User::factory()->create();
        $orderNumbers = [];

        // Act - Create multiple orders
        for ($i = 0; $i < 5; $i++) {
            $cart = Cart::factory()->create(['user_id' => $user->id]);
            $product = Product::factory()->create(['price' => 50.00, 'stock' => 10]);
            CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 50.00,
            ]);

            $checkoutData = new CheckoutData(
                shippingAddressId: null,
                billingAddressId: null,
                paymentMethod: 'credit_card',
                shippingMethod: 'standard',
                customerNotes: null
            );

            $order = $this->checkoutService->processCheckout($cart, $checkoutData);
            $orderNumbers[] = $order->order_number;
        }

        // Assert - All order numbers should be unique
        $this->assertCount(5, array_unique($orderNumbers));
    }

    /** @test */
    public function it_copies_address_ids_from_checkout_data()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'paypal',
            shippingMethod: 'express',
            customerNotes: 'Rush delivery'
        );

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $this->assertNull($order->shipping_address_id);
        $this->assertNull($order->billing_address_id);
        $this->assertEquals('paypal', $order->payment_method);
        $this->assertEquals('express', $order->shipping_method);
        $this->assertEquals('Rush delivery', $order->customer_notes);
    }

    /** @test */
    public function it_decrements_product_stock_after_checkout()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
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
        $product->refresh();
        $this->assertEquals(7, $product->stock); // 10 - 3 = 7
    }

    /** @test */
    public function it_throws_exception_when_insufficient_stock_during_checkout()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 2]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5, // More than available stock
            'price' => 100.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act & Assert
        $this->expectException(\App\Exceptions\Cart\CheckoutException::class);
        $this->expectExceptionMessage('insufficient stock');
        $this->checkoutService->processCheckout($cart, $checkoutData);
    }

    /** @test */
    public function it_increments_coupon_used_count_after_checkout()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $coupon = \App\Models\Coupon::factory()->create([
            'code' => 'SAVE20',
            'used_count' => 5,
        ]);
        
        $cart->coupon_code = 'SAVE20';
        $cart->discount_amount = 20.00;
        $cart->save();
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
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
        $coupon->refresh();
        $this->assertEquals(6, $coupon->used_count); // 5 + 1 = 6
    }

    /** @test */
    public function it_creates_coupon_user_pivot_entry_after_checkout()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $coupon = \App\Models\Coupon::factory()->create([
            'code' => 'WELCOME10',
            'used_count' => 0,
        ]);
        
        $cart->coupon_code = 'WELCOME10';
        $cart->discount_amount = 10.00;
        $cart->save();
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
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
        $this->assertDatabaseHas('coupon_user', [
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
        ]);
        
        // Verify the relationship
        $this->assertTrue($coupon->users->contains($user));
    }

    /** @test */
    public function it_does_not_increment_coupon_when_no_coupon_applied()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
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

        // Assert - no coupon_user entries should be created
        $this->assertDatabaseCount('coupon_user', 0);
    }

    /** @test */
    public function it_clears_cart_items_after_successful_checkout()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $product1 = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 50.00, 'stock' => 5]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 50.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Verify cart has items before checkout
        $this->assertCount(2, $cart->items);

        // Act
        $order = $this->checkoutService->processCheckout($cart, $checkoutData);

        // Assert
        $cart->refresh();
        $this->assertCount(0, $cart->items);
        $this->assertDatabaseCount('cart_items', 0);
    }

    /** @test */
    public function it_rolls_back_transaction_when_checkout_fails()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        // Create a product with insufficient stock
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 2]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5, // More than available stock
            'price' => 100.00,
        ]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Get initial counts
        $initialOrderCount = \App\Models\Order::count();
        $initialOrderItemCount = \App\Models\OrderItem::count();
        $initialCartItemCount = $cart->items()->count();

        // Act & Assert
        try {
            $this->checkoutService->processCheckout($cart, $checkoutData);
            $this->fail('Expected CheckoutException was not thrown');
        } catch (\App\Exceptions\Cart\CheckoutException $e) {
            // Verify no order was created
            $this->assertEquals($initialOrderCount, \App\Models\Order::count());
            
            // Verify no order items were created
            $this->assertEquals($initialOrderItemCount, \App\Models\OrderItem::count());
            
            // Verify cart items were not deleted
            $cart->refresh();
            $this->assertEquals($initialCartItemCount, $cart->items()->count());
            
            // Verify stock was not decremented
            $product->refresh();
            $this->assertEquals(2, $product->stock);
        }
    }

    /** @test */
    public function it_can_checkout_through_cart_service()
    {
        // Arrange
        $cartService = app(\App\Services\Cart\CartService::class);
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
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
        $order = $cartService->checkout($cart, $checkoutData);

        // Assert
        $this->assertNotNull($order);
        $this->assertInstanceOf(\App\Models\Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
    }

    /** @test */
    public function it_throws_exception_when_checking_out_empty_cart()
    {
        // Arrange
        $cartService = app(\App\Services\Cart\CartService::class);
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $checkoutData = new CheckoutData(
            shippingAddressId: null,
            billingAddressId: null,
            paymentMethod: 'credit_card',
            shippingMethod: 'standard',
            customerNotes: null
        );

        // Act & Assert
        $this->expectException(\App\Exceptions\Cart\CartException::class);
        $this->expectExceptionMessage('empty cart');
        $cartService->checkout($cart, $checkoutData);
    }

    /** @test */
    public function it_throws_exception_when_checking_out_expired_cart()
    {
        // Arrange
        $cartService = app(\App\Services\Cart\CartService::class);
        $cart = Cart::factory()->create([
            'user_id' => null,
            'session_id' => 'test-session',
            'expires_at' => now()->subDay(), // Expired yesterday
        ]);
        
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
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

        // Act & Assert
        $this->expectException(\App\Exceptions\Cart\CartExpiredException::class);
        $cartService->checkout($cart, $checkoutData);
    }
}
