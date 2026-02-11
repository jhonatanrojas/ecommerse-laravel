<?php

namespace Tests\Feature\Cart;

use App\Enums\CouponType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponManagementTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
    }

    public function test_apply_fixed_coupon_to_cart(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a cart with items
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10, 'is_active' => true]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        // Create a fixed coupon
        $coupon = Coupon::factory()->create([
            'code' => 'FIXED10',
            'type' => CouponType::Fixed,
            'value' => 10,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        // Apply coupon
        $result = $this->cartService->applyCoupon($cart, 'FIXED10', $user);

        // Assert coupon was applied
        $this->assertEquals('FIXED10', $result->coupon_code);
        $this->assertEquals(10, $result->discount_amount);
    }

    public function test_apply_percentage_coupon_to_cart(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a cart with items
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10, 'is_active' => true]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        // Create a percentage coupon (10% off)
        $coupon = Coupon::factory()->create([
            'code' => 'PERCENT10',
            'type' => CouponType::Percentage,
            'value' => 10,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        // Apply coupon
        $result = $this->cartService->applyCoupon($cart, 'PERCENT10', $user);

        // Assert coupon was applied
        // Subtotal is 200 (100 * 2), 10% discount = 20
        $this->assertEquals('PERCENT10', $result->coupon_code);
        $this->assertEquals(20, $result->discount_amount);
    }

    public function test_remove_coupon_from_cart(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a cart with items and a coupon
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'coupon_code' => 'TESTCODE',
            'discount_amount' => 10,
        ]);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10, 'is_active' => true]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        // Remove coupon
        $result = $this->cartService->removeCoupon($cart);

        // Assert coupon was removed
        $this->assertNull($result->coupon_code);
        $this->assertEquals(0.00, $result->discount_amount);
    }

    public function test_apply_coupon_throws_exception_for_invalid_code(): void
    {
        $this->expectException(\App\Exceptions\Cart\CouponNotFoundException::class);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $this->cartService->applyCoupon($cart, 'INVALID', $user);
    }

    public function test_apply_coupon_throws_exception_for_inactive_coupon(): void
    {
        $this->expectException(\App\Exceptions\Cart\CouponInactiveException::class);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10, 'is_active' => true]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'INACTIVE',
            'type' => CouponType::Fixed,
            'value' => 10,
            'is_active' => false,
        ]);

        $this->cartService->applyCoupon($cart, 'INACTIVE', $user);
    }

    public function test_apply_coupon_throws_exception_for_expired_coupon(): void
    {
        $this->expectException(\App\Exceptions\Cart\CouponExpiredException::class);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10, 'is_active' => true]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'type' => CouponType::Fixed,
            'value' => 10,
            'is_active' => true,
            'starts_at' => now()->subDays(10),
            'expires_at' => now()->subDay(),
        ]);

        $this->cartService->applyCoupon($cart, 'EXPIRED', $user);
    }

    public function test_apply_coupon_throws_exception_for_minimum_purchase_not_met(): void
    {
        $this->expectException(\App\Exceptions\Cart\MinimumPurchaseNotMetException::class);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10, 'stock' => 10, 'is_active' => true]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10,
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'MINPURCHASE',
            'type' => CouponType::Fixed,
            'value' => 5,
            'is_active' => true,
            'min_purchase_amount' => 100,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $this->cartService->applyCoupon($cart, 'MINPURCHASE', $user);
    }
}
