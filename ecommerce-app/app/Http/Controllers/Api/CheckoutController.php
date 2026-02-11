<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\Cart\CartService;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Process checkout
     */
    public function store(CheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        // Check if guest checkout is allowed
        $storeSetting = \App\Models\StoreSetting::first();
        $allowGuestCheckout = $storeSetting?->allow_guest_checkout ?? false;

        // If guest checkout is not allowed and user is not authenticated, return error
        if (!$allowGuestCheckout && !$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debe iniciar sesión para completar la compra',
            ], 401);
        }

        $cart = $this->cartService->findCart($user, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found',
            ], 404);
        }

        if ($cart->items()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot checkout with an empty cart',
            ], 422);
        }

        // Handle shipping address (ID or full address)
        $shippingAddressId = $request->validated('shipping_address_id');
        if (!$shippingAddressId && $request->has('shipping_address')) {
            // Create new address from provided data (only if user is authenticated)
            if ($user) {
                $shippingAddress = $user->addresses()->create($request->validated('shipping_address'));
                $shippingAddressId = $shippingAddress->id;
            }
            // For guest users, we'll pass the address data directly to the checkout service
        }

        // Handle billing address (ID or full address)
        $billingAddressId = $request->validated('billing_address_id');
        if (!$billingAddressId && $request->has('billing_address')) {
            // Create new address from provided data (only if user is authenticated)
            if ($user) {
                $billingAddress = $user->addresses()->create($request->validated('billing_address'));
                $billingAddressId = $billingAddress->id;
            }
            // For guest users, we'll pass the address data directly to the checkout service
        }

        $checkoutData = new CheckoutData(
            shippingAddressId: $shippingAddressId,
            billingAddressId: $billingAddressId,
            paymentMethod: $request->validated('payment_method'),
            shippingMethod: $request->validated('shipping_method'),
            customerNotes: $request->validated('notes'),
            // Pass raw address data for guest users
            shippingAddressData: !$shippingAddressId ? $request->validated('shipping_address') : null,
            billingAddressData: !$billingAddressId ? $request->validated('billing_address') : null
        );

        $order = $this->cartService->checkout($cart, $checkoutData, $user, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Checkout completed successfully',
            'data' => new OrderResource($order),
        ], 201);
    }
}
