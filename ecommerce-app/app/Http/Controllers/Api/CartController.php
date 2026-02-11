<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartSummaryResource;
use App\Models\CartItem;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Get cart summary
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        $cart = $this->cartService->findCart($user, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'summary' => [
                        'subtotal' => 0,
                        'discount' => 0,
                        'tax' => 0,
                        'shipping_cost' => 0,
                        'total' => 0,
                        'item_count' => 0,
                        'coupon_code' => null,
                    ],
                ],
            ]);
        }

        $summary = $this->cartService->getCartSummary($cart);

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => new CartResource($cart),
                'summary' => new CartSummaryResource($summary),
            ],
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(AddItemRequest $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        $cart = $this->cartService->getOrCreateCart($user, $sessionId);

        $item = $this->cartService->addItem(
            $cart,
            $request->validated('product_id'),
            $request->validated('variant_id'),
            $request->validated('quantity'),
            $user,
            $sessionId
        );

        $summary = $this->cartService->getCartSummary($cart->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => [
                'cart' => new CartResource($cart->fresh()),
                'summary' => new CartSummaryResource($summary),
            ],
        ], 201);
    }

    /**
     * Update item quantity
     */
    public function update(UpdateItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        $updatedItem = $this->cartService->updateItemQuantity(
            $cartItem,
            $request->validated('quantity'),
            $user,
            $sessionId
        );

        $cart = $updatedItem->cart->fresh();
        $summary = $this->cartService->getCartSummary($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item quantity updated successfully',
            'data' => [
                'cart' => new CartResource($cart),
                'summary' => new CartSummaryResource($summary),
            ],
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        $cart = $cartItem->cart;

        $this->cartService->removeItem($cartItem, $user, $sessionId);

        $cart = $cart->fresh();
        $summary = $this->cartService->getCartSummary($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
            'data' => [
                'cart' => new CartResource($cart),
                'summary' => new CartSummaryResource($summary),
            ],
        ]);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->session()->getId();

        $cart = $this->cartService->findCart($user, $sessionId);

        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart is already empty',
            ]);
        }

        $this->cartService->clearCart($cart, $user, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }
}
