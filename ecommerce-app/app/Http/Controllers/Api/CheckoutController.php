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

        $checkoutData = new CheckoutData(
            shippingAddressId: $request->validated('shipping_address_id'),
            billingAddressId: $request->validated('billing_address_id'),
            paymentMethod: $request->validated('payment_method'),
            shippingMethod: $request->validated('shipping_method'),
            customerNotes: $request->validated('customer_notes')
        );

        $order = $this->cartService->checkout($cart, $checkoutData, $user, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Checkout completed successfully',
            'data' => [
                'order' => new OrderResource($order),
            ],
        ], 201);
    }
}
