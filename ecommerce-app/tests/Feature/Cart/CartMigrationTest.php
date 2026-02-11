<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = app(CartService::class);
    }

    /** @test */
    public function it_migrates_guest_cart_to_user_cart()
    {
        // Create a user first
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Create a guest cart with items
        $sessionId = 'test-session-123';
        $guestCart = Cart::factory()->create([
            'session_id' => $sessionId,
            'user_id' => null,
        ]);

        $product1 = Product::factory()->create(['stock' => 100, 'is_active' => true]);
        $product2 = Product::factory()->create(['stock' => 100, 'is_active' => true]);

        CartItem::factory()->create([
            'cart_id' => $guestCart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => $product1->price,
        ]);

        CartItem::factory()->create([
            'cart_id' => $guestCart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => $product2->price,
        ]);

        // Migrate guest cart to user
        $userCart = $this->cartService->migrateGuestCartToUser($sessionId, $user);

        // Assert user cart exists and has correct user_id
        $this->assertNotNull($userCart);
        $this->assertEquals($user->id, $userCart->user_id);

        // Assert all items were migrated
        $this->assertCount(2, $userCart->items);

        // Assert guest cart was deleted
        $this->assertDatabaseMissing('carts', ['id' => $guestCart->id]);
    }

    /** @test */
    public function it_consolidates_duplicate_items_during_migration()
    {
        // Create a user first
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Create a guest cart with an item
        $sessionId = 'test-session-456';
        $guestCart = Cart::factory()->create([
            'session_id' => $sessionId,
            'user_id' => null,
        ]);

        $product = Product::factory()->create(['stock' => 100, 'is_active' => true]);

        CartItem::factory()->create([
            'cart_id' => $guestCart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
        ]);

        // Create user cart containing the same product
        $userCart = Cart::factory()->create([
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        CartItem::factory()->create([
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => $product->price,
        ]);

        // Migrate guest cart to user
        $migratedCart = $this->cartService->migrateGuestCartToUser($sessionId, $user);

        // Assert quantities were summed
        $this->assertCount(1, $migratedCart->items);
        $this->assertEquals(5, $migratedCart->items->first()->quantity);

        // Assert guest cart was deleted
        $this->assertDatabaseMissing('carts', ['id' => $guestCart->id]);
    }

    /** @test */
    public function it_returns_user_cart_when_no_guest_cart_exists()
    {
        // Create a user first
        $user = User::create([
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $sessionId = 'non-existent-session';

        // Migrate (should just create/return user cart)
        $userCart = $this->cartService->migrateGuestCartToUser($sessionId, $user);

        // Assert user cart was created
        $this->assertNotNull($userCart);
        $this->assertEquals($user->id, $userCart->user_id);
        $this->assertNull($userCart->session_id);
    }
}
