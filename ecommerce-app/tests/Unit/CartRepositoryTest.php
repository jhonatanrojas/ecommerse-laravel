<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\User;
use App\Services\Cart\CartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CartRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CartRepository();
    }

    /**
     * Create a user directly via DB to avoid UUID issues with the User model.
     */
    private function createUser(string $email): int
    {
        return DB::table('users')->insertGetId([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Test User',
            'email' => $email,
            'password' => bcrypt('password'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_finds_cart_by_user()
    {
        $userId = $this->createUser('test@example.com');
        $user = User::find($userId);
        
        $cart = Cart::factory()->create(['user_id' => $userId]);

        $foundCart = $this->repository->findByUser($user);

        $this->assertNotNull($foundCart);
        $this->assertEquals($cart->id, $foundCart->id);
        $this->assertEquals($userId, $foundCart->user_id);
    }

    /** @test */
    public function it_returns_null_when_user_has_no_cart()
    {
        $userId = $this->createUser('test2@example.com');
        $user = User::find($userId);

        $foundCart = $this->repository->findByUser($user);

        $this->assertNull($foundCart);
    }

    /** @test */
    public function it_finds_cart_by_session_id()
    {
        $sessionId = 'test-session-123';
        $cart = Cart::factory()->guest()->create(['session_id' => $sessionId]);

        $foundCart = $this->repository->findBySession($sessionId);

        $this->assertNotNull($foundCart);
        $this->assertEquals($cart->id, $foundCart->id);
        $this->assertEquals($sessionId, $foundCart->session_id);
    }

    /** @test */
    public function it_returns_null_when_session_has_no_cart()
    {
        $foundCart = $this->repository->findBySession('non-existent-session');

        $this->assertNull($foundCart);
    }

    /** @test */
    public function it_finds_cart_by_uuid()
    {
        $cart = Cart::factory()->create();

        $foundCart = $this->repository->findByUuid($cart->uuid);

        $this->assertNotNull($foundCart);
        $this->assertEquals($cart->id, $foundCart->id);
        $this->assertEquals($cart->uuid, $foundCart->uuid);
    }

    /** @test */
    public function it_returns_null_when_uuid_does_not_exist()
    {
        $foundCart = $this->repository->findByUuid('non-existent-uuid');

        $this->assertNull($foundCart);
    }

    /** @test */
    public function it_creates_cart_with_user_id()
    {
        $userId = $this->createUser('test3@example.com');
        
        $cart = $this->repository->create([
            'user_id' => $userId,
        ]);

        $this->assertNotNull($cart);
        $this->assertEquals($userId, $cart->user_id);
        $this->assertNull($cart->session_id);
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'user_id' => $userId,
        ]);
    }

    /** @test */
    public function it_creates_cart_with_session_id()
    {
        $sessionId = 'guest-session-456';
        
        $cart = $this->repository->create([
            'session_id' => $sessionId,
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        $this->assertNotNull($cart);
        $this->assertEquals($sessionId, $cart->session_id);
        $this->assertNull($cart->user_id);
        $this->assertNotNull($cart->expires_at);
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'session_id' => $sessionId,
        ]);
    }

    /** @test */
    public function it_updates_cart_data()
    {
        $cart = Cart::factory()->guest()->create([
            'discount_amount' => 0,
            'coupon_code' => null,
        ]);

        $updatedCart = $this->repository->update($cart, [
            'coupon_code' => 'SAVE20',
            'discount_amount' => 20.00,
        ]);

        $this->assertEquals('SAVE20', $updatedCart->coupon_code);
        $this->assertEquals(20.00, $updatedCart->discount_amount);
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'coupon_code' => 'SAVE20',
            'discount_amount' => 20.00,
        ]);
    }

    /** @test */
    public function it_deletes_expired_carts()
    {
        // Create expired carts
        Cart::factory()->count(3)->create([
            'session_id' => 'expired-session',
            'expires_at' => Carbon::now()->subDays(5),
        ]);

        // Create non-expired cart
        Cart::factory()->create([
            'session_id' => 'active-session',
            'expires_at' => Carbon::now()->addDays(10),
        ]);

        // Create authenticated cart (no expiration)
        $userId = $this->createUser('test4@example.com');
        Cart::factory()->create([
            'user_id' => $userId,
            'expires_at' => null,
        ]);

        $deletedCount = $this->repository->deleteExpiredCarts();

        $this->assertEquals(3, $deletedCount);
        $this->assertEquals(2, Cart::count());
    }

    /** @test */
    public function it_does_not_delete_carts_without_expiration()
    {
        $userId = $this->createUser('test5@example.com');
        Cart::factory()->create([
            'user_id' => $userId,
            'expires_at' => null,
        ]);

        $deletedCount = $this->repository->deleteExpiredCarts();

        $this->assertEquals(0, $deletedCount);
        $this->assertEquals(1, Cart::count());
    }

    /** @test */
    public function it_returns_zero_when_no_expired_carts_exist()
    {
        Cart::factory()->create([
            'session_id' => 'active-session',
            'expires_at' => Carbon::now()->addDays(10),
        ]);

        $deletedCount = $this->repository->deleteExpiredCarts();

        $this->assertEquals(0, $deletedCount);
        $this->assertEquals(1, Cart::count());
    }
}
