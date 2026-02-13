<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
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
            if ($user) {
                $shippingAddress = $user->addresses()->create(
                    $this->mapRequestAddressToModel($request->validated('shipping_address'), AddressType::Shipping)
                );
                $shippingAddressId = $shippingAddress->id;
            }
        }

        // Handle billing address (ID or full address)
        $billingAddressId = $request->validated('billing_address_id');
        if (!$billingAddressId && $request->has('billing_address')) {
            if ($user) {
                $billingAddress = $user->addresses()->create(
                    $this->mapRequestAddressToModel($request->validated('billing_address'), AddressType::Billing)
                );
                $billingAddressId = $billingAddress->id;
            }
        }

        $checkoutData = new CheckoutData(
            shippingAddressId: $shippingAddressId,
            billingAddressId: $billingAddressId,
            paymentMethod: $request->validated('payment_method'),
            shippingMethod: $request->validated('shipping_method'),
            customerNotes: $request->validated('notes')
        );

        $order = $this->cartService->checkout($cart, $checkoutData, $user, $sessionId);
        $order->load(['items', 'shippingAddress', 'billingAddress', 'payments', 'vendorOrders']);

        return response()->json([
            'success' => true,
            'message' => 'Checkout completed successfully',
            'data' => new OrderResource($order),
        ], 201);
    }

    /**
     * Map checkout request address (full_name, address_line_1, etc.) to Address model fields.
     */
    private function mapRequestAddressToModel(array $data, AddressType $type): array
    {
        $fullName = trim($data['full_name'] ?? '');
        $parts = preg_split('/\s+/', $fullName, 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? $firstName;

        return [
            'type' => $type,
            'first_name' => $firstName ?: 'N/A',
            'last_name' => $lastName ?: 'N/A',
            'address_line1' => $data['address_line_1'] ?? '',
            'address_line2' => $data['address_line_2'] ?? null,
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? '',
            'country' => $data['country'] ?? '',
            'is_default' => false,
        ];
    }
}
