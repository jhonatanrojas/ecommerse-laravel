<?php

namespace Tests\Feature\Cart;

use App\Exceptions\Cart\UnauthorizedCartAccessException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
    }

    /** @test */
    public function it_prevents_authenticated_user_from_accessing_another_users_cart()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->addItem($cart, $product->id, null, 1, $user2, null);
    }

    /** @test */
    public function it_prevents_guest_from_accessing_another_sessions_cart()
    {
        // Arrange
        $cart = Cart::factory()->create([
            'user_id' => null,
            'session_id' => 'session-123',
        ]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->addItem($cart, $product->id, null, 1, null, 'session-456');
    }

    /** @test */
    public function it_allows_authenticated_user_to_access_their_own_cart()
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);

        // Act
        $cartItem = $this->cartService->addItem($cart, $product->id, null, 1, $user, null);

        // Assert
        $this->assertNotNull($cartItem);
        $this->assertEquals($cart->id, $cartItem->cart_id);
    }

    /** @test */
    public function it_allows_guest_to_access_their_own_cart()
    {
        // Arrange
        $sessionId = 'session-123';
        $cart = Cart::factory()->create([
            'user_id' => null,
            'session_id' => $sessionId,
        ]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);

        // Act
        $cartItem = $this->cartService->addItem($cart, $product->id, null, 1, null, $sessionId);

        // Assert
        $this->assertNotNull($cartItem);
        $this->assertEquals($cart->id, $cartItem->cart_id);
    }

    /** @test */
    public function it_prevents_unauthorized_update_item_quantity()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->updateItemQuantity($cartItem, 2, $user2, null);
    }

    /** @test */
    public function it_prevents_unauthorized_remove_item()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->removeItem($cartItem, $user2, null);
    }

    /** @test */
    public function it_prevents_unauthorized_clear_cart()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->clearCart($cart, $user2, null);
    }

    /** @test */
    public function it_prevents_unauthorized_apply_coupon()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->applyCoupon($cart, 'TESTCODE', $user2, null);
    }

    /** @test */
    public function it_prevents_unauthorized_remove_coupon()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);

        // Act & Assert
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->removeCoupon($cart, $user2, null);
    }

    /** @test */
    public function it_prevents_unauthorized_checkout()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user1->id]);
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
        $this->expectException(UnauthorizedCartAccessException::class);
        $this->cartService->checkout($cart, $checkoutData, $user2, null);
    }
}
