<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\ApplyCouponRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartSummaryResource;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Apply coupon to cart
     */
    public function store(ApplyCouponRequest $request): JsonResponse
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

        $cart = $this->cartService->applyCoupon(
            $cart,
            $request->validated('coupon_code'),
            $user,
            $sessionId
        );

        $summary = $this->cartService->getCartSummary($cart->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'cart' => new CartResource($cart->fresh()),
                'summary' => new CartSummaryResource($summary),
            ],
        ]);
    }

    /**
     * Remove coupon from cart
     */
    public function destroy(Request $request): JsonResponse
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

        if (!$cart->coupon_code) {
            return response()->json([
                'success' => true,
                'message' => 'No coupon to remove',
            ]);
        }

        $cart = $this->cartService->removeCoupon($cart, $user, $sessionId);

        $summary = $this->cartService->getCartSummary($cart->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully',
            'data' => [
                'cart' => new CartResource($cart->fresh()),
                'summary' => new CartSummaryResource($summary),
            ],
        ]);
    }
}
