<?php

namespace App\Events\Cart;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartMigrated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Cart $guestCart,
        public Cart $userCart,
        public User $user
    ) {}
}
