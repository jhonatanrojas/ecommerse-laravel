<?php

namespace App\Services\Cart;

use App\Exceptions\Cart\CartException;
use App\Exceptions\Cart\CartExpiredException;
use App\Exceptions\Cart\UnauthorizedCartAccessException;
use App\Events\Cart\CartCleared;
use App\Events\Cart\CartCreated;
use App\Events\Cart\CartItemAdded;
use App\Events\Cart\CartItemRemoved;
use App\Events\Cart\CartItemUpdated;
use App\Events\Cart\CartMigrated;
use App\Events\Cart\CouponApplied;
use App\Events\Cart\CouponRemoved;
use App\Models\Cart;
use App\Models\User;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository,
        private CouponValidator $couponValidator,
        private StockValidator $stockValidator,
        private CartCalculator $cartCalculator,
        private CheckoutService $checkoutService
    ) {}

    /**
     * Get or create a cart for the given user or session.
     *
     * @param User|null $user
     * @param string|null $sessionId
     * @return Cart
     */
    public function getOrCreateCart(?User $user, ?string $sessionId): Cart
    {
        $cart = $this->findCart($user, $sessionId);

        if ($cart) {
            return $cart;
        }

        // Create new cart
        $data = [];

        if ($user) {
            // Authenticated cart
            $data['user_id'] = $user->id;
            $data['expires_at'] = null;
        } else {
            // Guest cart
            $data['session_id'] = $sessionId;
            $data['expires_at'] = Carbon::now()->addDays(config('cart.guest_cart_expiration_days', 30));
        }

        $cart = $this->cartRepository->create($data);

        // Emit CartCreated event
        event(new CartCreated($cart));

        return $cart;
    }

    /**
     * Find an existing cart for the given user or session.
     *
     * @param User|null $user
     * @param string|null $sessionId
     * @return Cart|null
     */
    public function findCart(?User $user, ?string $sessionId): ?Cart
    {
        if ($user) {
            return $this->cartRepository->findByUser($user);
        }

        if ($sessionId) {
            return $this->cartRepository->findBySession($sessionId);
        }

        return null;
    }

    /**
     * Check if a cart is expired.
     *
     * @param Cart $cart
     * @return bool
     */
    public function isCartExpired(Cart $cart): bool
    {
        if ($cart->expires_at === null) {
            return false;
        }

        return Carbon::now()->isAfter($cart->expires_at);
    }

    /**
     * Validate that a cart is not expired.
     * Throws CartExpiredException if the cart is expired.
     * 
     * This method should be called at the beginning of all cart operations
     * (addItem, updateItemQuantity, removeItem, clearCart, applyCoupon, 
     * removeCoupon, checkout) to ensure the cart is still valid.
     *
     * @param Cart $cart
     * @return void
     * @throws CartExpiredException
     */
    protected function validateCartNotExpired(Cart $cart): void
    {
        if ($this->isCartExpired($cart)) {
            throw new CartExpiredException('The cart has expired.');
        }
    }

    /**
     * Authorize access to the cart.
     *
     * Verifies that the current user or session has permission to access the cart.
     * For authenticated users, checks that cart.user_id matches the current user.
     * For guests, checks that cart.session_id matches the current session.
     *
     * @param Cart $cart
     * @param User|null $user
     * @param string|null $sessionId
     * @return void
     * @throws UnauthorizedCartAccessException
     */
    public function authorizeCartAccess(Cart $cart, ?User $user, ?string $sessionId): void
    {
        if ($user) {
            // Authenticated user - verify cart belongs to this user
            if ($cart->user_id !== $user->id) {
                throw new UnauthorizedCartAccessException(
                    'You are not authorized to access this cart.'
                );
            }
        } else {
            // Guest user - verify cart belongs to this session
            if ($cart->session_id !== $sessionId) {
                throw new UnauthorizedCartAccessException(
                    'You are not authorized to access this cart.'
                );
            }
        }
    }


    /**
     * Add an item to the cart.
     *
     * @param Cart $cart
     * @param int $productId
     * @param int|null $variantId
     * @param int $quantity
     * @param User|null $user
     * @param string|null $sessionId
     * @return \App\Models\CartItem
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     * @throws \App\Exceptions\Cart\ProductNotFoundException
     * @throws \App\Exceptions\Cart\ProductInactiveException
     * @throws \App\Exceptions\Cart\InsufficientStockException
     * @throws \App\Exceptions\Cart\InvalidQuantityException
     */
    public function addItem(Cart $cart, int $productId, ?int $variantId, int $quantity, ?User $user = null, ?string $sessionId = null): \App\Models\CartItem
    {
        // Authorize cart access
        $this->authorizeCartAccess($cart, $user, $sessionId);
        
        // Validate cart is not expired
        $this->validateCartNotExpired($cart);

        // Validate quantity limits
        if ($quantity < 1) {
            throw new \App\Exceptions\Cart\InvalidQuantityException('Quantity must be at least 1.');
        }

        $maxQuantity = config('cart.max_item_quantity', 99);
        if ($quantity > $maxQuantity) {
            throw new \App\Exceptions\Cart\InvalidQuantityException("Quantity cannot exceed {$maxQuantity}.");
        }

        return DB::transaction(function () use ($cart, $productId, $variantId, $quantity) {
            // Validate product or variant
            if ($variantId) {
                $validationResult = $this->stockValidator->validateVariant($variantId);
                if (!$validationResult->isValid) {
                    throw new \App\Exceptions\Cart\ProductNotFoundException($validationResult->errorMessage);
                }

                // Lock variant for stock validation
                $variant = \App\Models\ProductVariant::lockForUpdate()->find($variantId);
                if (!$variant) {
                    throw new \App\Exceptions\Cart\ProductNotFoundException("Product variant with ID {$variantId} not found.");
                }

                // Validate stock
                $stockValidation = $this->stockValidator->validateStock($productId, $variantId, $quantity);
                if (!$stockValidation->isValid) {
                    throw new \App\Exceptions\Cart\InsufficientStockException($stockValidation->errorMessage);
                }

                // Get current price from database
                $price = $variant->price;
            } else {
                $validationResult = $this->stockValidator->validateProduct($productId);
                if (!$validationResult->isValid) {
                    throw new \App\Exceptions\Cart\ProductNotFoundException($validationResult->errorMessage);
                }

                // Lock product for stock validation
                $product = \App\Models\Product::lockForUpdate()->find($productId);
                if (!$product) {
                    throw new \App\Exceptions\Cart\ProductNotFoundException("Product with ID {$productId} not found.");
                }

                // Validate stock
                $stockValidation = $this->stockValidator->validateStock($productId, null, $quantity);
                if (!$stockValidation->isValid) {
                    throw new \App\Exceptions\Cart\InsufficientStockException($stockValidation->errorMessage);
                }

                // Get current price from database
                $price = $product->price;
            }

            // Check if item already exists in cart
            $existingItem = $cart->items()
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($existingItem) {
                // Update quantity instead of creating new item
                $newQuantity = $existingItem->quantity + $quantity;

                // Validate new quantity against limits
                $maxQuantity = config('cart.max_item_quantity', 99);
                if ($newQuantity > $maxQuantity) {
                    throw new \App\Exceptions\Cart\InvalidQuantityException("Total quantity cannot exceed {$maxQuantity}.");
                }

                // Validate new quantity against stock
                $stockValidation = $this->stockValidator->validateStock($productId, $variantId, $newQuantity);
                if (!$stockValidation->isValid) {
                    throw new \App\Exceptions\Cart\InsufficientStockException($stockValidation->errorMessage);
                }

                $oldQuantity = $existingItem->quantity;
                $existingItem->quantity = $newQuantity;
                $existingItem->save();

                // Emit CartItemUpdated event (since we updated existing item)
                event(new CartItemUpdated($cart, $existingItem, $oldQuantity));

                return $existingItem;
            }

            // Create new cart item
            $cartItem = \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            // Emit CartItemAdded event
            event(new CartItemAdded($cart, $cartItem));

            return $cartItem;
        });
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param \App\Models\CartItem $item
     * @param int $quantity
     * @param User|null $user
     * @param string|null $sessionId
     * @return \App\Models\CartItem
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     * @throws \App\Exceptions\Cart\InsufficientStockException
     * @throws \App\Exceptions\Cart\InvalidQuantityException
     */
    public function updateItemQuantity(\App\Models\CartItem $item, int $quantity, ?User $user = null, ?string $sessionId = null): \App\Models\CartItem
    {
        // Authorize cart access
        $this->authorizeCartAccess($item->cart, $user, $sessionId);
        
        // Validate cart is not expired
        $this->validateCartNotExpired($item->cart);

        // Validate quantity limits
        if ($quantity < 1) {
            throw new \App\Exceptions\Cart\InvalidQuantityException('Quantity must be at least 1.');
        }

        $maxQuantity = config('cart.max_item_quantity', 99);
        if ($quantity > $maxQuantity) {
            throw new \App\Exceptions\Cart\InvalidQuantityException("Quantity cannot exceed {$maxQuantity}.");
        }

        return DB::transaction(function () use ($item, $quantity) {
            // Lock product or variant for stock validation
            if ($item->product_variant_id) {
                $variant = \App\Models\ProductVariant::lockForUpdate()->find($item->product_variant_id);
                if (!$variant) {
                    throw new \App\Exceptions\Cart\ProductNotFoundException("Product variant with ID {$item->product_variant_id} not found.");
                }
            } else {
                $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                if (!$product) {
                    throw new \App\Exceptions\Cart\ProductNotFoundException("Product with ID {$item->product_id} not found.");
                }
            }

            // Validate stock
            $stockValidation = $this->stockValidator->validateStock(
                $item->product_id,
                $item->product_variant_id,
                $quantity
            );

            if (!$stockValidation->isValid) {
                throw new \App\Exceptions\Cart\InsufficientStockException($stockValidation->errorMessage);
            }

            // Update quantity
            $oldQuantity = $item->quantity;
            $item->quantity = $quantity;
            $item->save();

            // Emit CartItemUpdated event
            event(new CartItemUpdated($item->cart, $item, $oldQuantity));

            return $item;
        });
    }

    /**
     * Remove an item from the cart.
     *
     * @param \App\Models\CartItem $item
     * @param User|null $user
     * @param string|null $sessionId
     * @return void
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     */
    public function removeItem(\App\Models\CartItem $item, ?User $user = null, ?string $sessionId = null): void
    {
        // Authorize cart access
        $this->authorizeCartAccess($item->cart, $user, $sessionId);
        
        // Validate cart is not expired
        $this->validateCartNotExpired($item->cart);

        $cart = $item->cart;
        $item->delete();

        // Emit CartItemRemoved event
        event(new CartItemRemoved($cart, $item));
    }

    /**
     * Clear all items from the cart.
     *
     * @param Cart $cart
     * @param User|null $user
     * @param string|null $sessionId
     * @return void
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     */
    public function clearCart(Cart $cart, ?User $user = null, ?string $sessionId = null): void
    {
        // Authorize cart access
        $this->authorizeCartAccess($cart, $user, $sessionId);
        
        // Validate cart is not expired
        $this->validateCartNotExpired($cart);

        $cart->items()->delete();

        // Emit CartCleared event
        event(new CartCleared($cart));
    }

    /**
     * Get cart summary with all calculated totals.
     *
     * @param Cart $cart
     * @return \App\Services\Cart\DTOs\CartSummary
     * @throws CartExpiredException
     */
    public function getCartSummary(Cart $cart): \App\Services\Cart\DTOs\CartSummary
    {
        // Validate cart is not expired
        $this->validateCartNotExpired($cart);

        // Eager load cart items with products and variants
        $cart->load(['items.product', 'items.variant']);

        // Calculate all totals using CartCalculator
        $subtotal = $this->cartCalculator->calculateSubtotal($cart);
        $discount = $cart->discount_amount ?? 0;
        $subtotalAfterDiscount = $subtotal - $discount;
        $tax = $this->cartCalculator->calculateTax($subtotalAfterDiscount);
        $shippingCost = $this->cartCalculator->calculateShipping($cart);
        $total = $subtotalAfterDiscount + $tax + $shippingCost;

        // Get item count
        $itemCount = $cart->items->count();

        // Get coupon code
        $couponCode = $cart->coupon_code;

        return new \App\Services\Cart\DTOs\CartSummary(
            subtotal: $subtotal,
            discount: $discount,
            tax: $tax,
            shippingCost: $shippingCost,
            total: $total,
            itemCount: $itemCount,
            couponCode: $couponCode
        );
    }

    /**
     * Recalculate all cart totals.
     * This method should be called after any cart modification.
     *
     * @param Cart $cart
     * @return \App\Services\Cart\DTOs\CartSummary
     * @throws CartExpiredException
     */
    public function recalculateTotals(Cart $cart): \App\Services\Cart\DTOs\CartSummary
    {
        // Validate cart is not expired
        $this->validateCartNotExpired($cart);

        // Simply delegate to getCartSummary which already calculates all totals
        return $this->getCartSummary($cart);
    }

    /**
     * Apply a coupon to the cart.
     *
     * @param Cart $cart
     * @param string $couponCode
     * @param User|null $user
     * @param string|null $sessionId
     * @return Cart
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     * @throws \App\Exceptions\Cart\CouponNotFoundException
     * @throws \App\Exceptions\Cart\CouponInactiveException
     * @throws \App\Exceptions\Cart\CouponExpiredException
     * @throws \App\Exceptions\Cart\CouponUsageLimitException
     * @throws \App\Exceptions\Cart\MinimumPurchaseNotMetException
     */
    public function applyCoupon(Cart $cart, string $couponCode, ?User $user, ?string $sessionId = null): Cart
    {
        // Authorize cart access
        $this->authorizeCartAccess($cart, $user, $sessionId);
        
        // Validate cart is not expired
        $this->validateCartNotExpired($cart);

        return DB::transaction(function () use ($cart, $couponCode, $user) {
            // Find coupon by code
            $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

            if (!$coupon) {
                throw new \App\Exceptions\Cart\CouponNotFoundException("Coupon with code '{$couponCode}' not found.");
            }

            // Calculate current subtotal
            $subtotal = $this->cartCalculator->calculateSubtotal($cart);

            // Validate coupon using CouponValidator
            $validationResult = $this->couponValidator->validate($coupon, $subtotal, $user);

            if (!$validationResult->isValid) {
                // Determine specific exception type based on error message
                $errorMessage = $validationResult->errorMessage;

                if (str_contains($errorMessage, 'not active')) {
                    throw new \App\Exceptions\Cart\CouponInactiveException($errorMessage);
                } elseif (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'not yet valid')) {
                    throw new \App\Exceptions\Cart\CouponExpiredException($errorMessage);
                } elseif (str_contains($errorMessage, 'usage limit') || str_contains($errorMessage, 'maximum number of times')) {
                    throw new \App\Exceptions\Cart\CouponUsageLimitException($errorMessage);
                } elseif (str_contains($errorMessage, 'minimum purchase')) {
                    throw new \App\Exceptions\Cart\MinimumPurchaseNotMetException($errorMessage);
                } else {
                    throw new \App\Exceptions\Cart\CouponException($errorMessage);
                }
            }

            // Calculate discount using CartCalculator
            $discount = $this->cartCalculator->calculateDiscount($cart, $coupon);

            // Update cart with coupon_code and discount_amount
            $cart->coupon_code = $coupon->code;
            $cart->discount_amount = $discount;
            $cart->save();

            // Emit CouponApplied event
            event(new CouponApplied($cart, $coupon, $discount));

            return $cart;
        });
    }

    /**
     * Remove the coupon from the cart.
     *
     * @param Cart $cart
     * @param User|null $user
     * @param string|null $sessionId
     * @return Cart
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     */
    public function removeCoupon(Cart $cart, ?User $user = null, ?string $sessionId = null): Cart
    {
        // Authorize cart access
        $this->authorizeCartAccess($cart, $user, $sessionId);
        
        // Validate cart is not expired
        $this->validateCartNotExpired($cart);

        // Store coupon code before clearing
        $couponCode = $cart->coupon_code;

        // Clear coupon_code and discount_amount
        $cart->coupon_code = null;
        $cart->discount_amount = 0.00;
        $cart->save();

        // Recalculate totals after removal
        $this->recalculateTotals($cart);

        // Emit CouponRemoved event (only if there was a coupon)
        if ($couponCode) {
            event(new CouponRemoved($cart, $couponCode));
        }

        return $cart;
    }

    /**
     * Migrate a guest cart to an authenticated user cart.
     *
     * This method finds the guest cart by session_id, finds or creates a user cart,
     * and consolidates items from the guest cart into the user cart.
     * If the same product/variant exists in both carts, quantities are summed.
     * The guest cart is deleted after successful migration.
     *
     * @param string $sessionId
     * @param User $user
     * @return Cart The user's cart after migration
     * @throws \Exception
     */
    public function migrateGuestCartToUser(string $sessionId, User $user): Cart
    {
        return DB::transaction(function () use ($sessionId, $user) {
            // Find guest cart by session_id
            $guestCart = $this->cartRepository->findBySession($sessionId);

            // If no guest cart exists, just return or create user cart
            if (!$guestCart) {
                return $this->getOrCreateCart($user, null);
            }

            // Find or create user cart
            $userCart = $this->getOrCreateCart($user, null);

            // Eager load guest cart items
            $guestCart->load('items');

            // Migrate each guest cart item
            foreach ($guestCart->items as $guestItem) {
                // Check if same product/variant exists in user cart
                $existingUserItem = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existingUserItem) {
                    // Sum quantities if item exists
                    $newQuantity = $existingUserItem->quantity + $guestItem->quantity;

                    // Validate against max quantity limit
                    $maxQuantity = config('cart.max_item_quantity', 99);
                    if ($newQuantity > $maxQuantity) {
                        $newQuantity = $maxQuantity;
                    }

                    // Validate against stock
                    $stockValidation = $this->stockValidator->validateStock(
                        $guestItem->product_id,
                        $guestItem->product_variant_id,
                        $newQuantity
                    );

                    if ($stockValidation->isValid) {
                        $existingUserItem->quantity = $newQuantity;
                        $existingUserItem->save();
                    } else {
                        // If stock is insufficient, use the maximum available
                        $availableStock = $this->stockValidator->getAvailableStock(
                            $guestItem->product_id,
                            $guestItem->product_variant_id
                        );
                        $existingUserItem->quantity = min($newQuantity, $availableStock);
                        $existingUserItem->save();
                    }
                } else {
                    // Move item to user cart
                    $guestItem->cart_id = $userCart->id;
                    $guestItem->save();
                }
            }

            // Delete guest cart after migration
            $guestCart->delete();

            // Reload user cart items
            $userCart->load('items');

            // Emit CartMigrated event
            event(new CartMigrated($guestCart, $userCart, $user));

            return $userCart;
        });
    }

    /**
     * Process checkout for the cart
     *
     * @param Cart $cart
     * @param CheckoutData $data
     * @param User|null $user
     * @param string|null $sessionId
     * @return \App\Models\Order
     * @throws CartException
     * @throws CartExpiredException
     * @throws UnauthorizedCartAccessException
     */
    public function checkout(Cart $cart, CheckoutData $data, ?User $user = null, ?string $sessionId = null): \App\Models\Order
    {
        // Validate cart is not expired (check this first)
        $this->validateCartNotExpired($cart);
        
        // Authorize cart access
        $this->authorizeCartAccess($cart, $user, $sessionId);

        // Validate cart is not empty
        if ($cart->items()->count() === 0) {
            throw new CartException('Cannot checkout an empty cart');
        }

        // Delegate to CheckoutService
        return $this->checkoutService->processCheckout($cart, $data);
    }


}

