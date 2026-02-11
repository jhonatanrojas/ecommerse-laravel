<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Carbon;

class CartRepository
{
    /**
     * Find a cart by user ID.
     *
     * @param User $user
     * @return Cart|null
     */
    public function findByUser(User $user): ?Cart
    {
        return Cart::where('user_id', $user->id)->first();
    }

    /**
     * Find a cart by session ID.
     *
     * @param string $sessionId
     * @return Cart|null
     */
    public function findBySession(string $sessionId): ?Cart
    {
        return Cart::where('session_id', $sessionId)->first();
    }

    /**
     * Find a cart by UUID.
     *
     * @param string $uuid
     * @return Cart|null
     */
    public function findByUuid(string $uuid): ?Cart
    {
        return Cart::where('uuid', $uuid)->first();
    }

    /**
     * Create a new cart with the given data.
     *
     * @param array $data
     * @return Cart
     */
    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    /**
     * Update a cart with the given data.
     *
     * @param Cart $cart
     * @param array $data
     * @return Cart
     */
    public function update(Cart $cart, array $data): Cart
    {
        $cart->update($data);
        return $cart->fresh();
    }

    /**
     * Delete expired carts.
     *
     * @return int Number of carts deleted
     */
    public function deleteExpiredCarts(): int
    {
        return Cart::where('expires_at', '<', Carbon::now())
            ->whereNotNull('expires_at')
            ->delete();
    }
}
