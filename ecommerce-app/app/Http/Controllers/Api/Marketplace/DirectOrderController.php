<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorProduct;
use App\Services\Marketplace\VendorOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DirectOrderController extends Controller
{
    public function __construct(
        protected VendorOrderService $vendorOrderService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
            'buyer_id' => ['nullable', 'integer', 'exists:users,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $vendorId = $validated['vendor_id']
            ?? VendorProduct::query()->where('product_id', $product->id)->where('is_active', true)->where('is_approved', true)->value('vendor_id');

        if (! $vendorId) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró vendedor activo para este producto.',
            ], 422);
        }

        $vendor = Vendor::query()->where('id', $vendorId)->where('status', 'approved')->first();
        if (! $vendor) {
            return response()->json([
                'success' => false,
                'message' => 'El vendedor no está disponible.',
            ], 422);
        }

        $buyerId = $request->user()?->id ?? ($validated['buyer_id'] ?? null);
        if (! $buyerId) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para comprar ahora.',
            ], 401);
        }

        $quantity = (int) ($validated['quantity'] ?? 1);
        $price = (float) $product->price;
        $subtotal = round($price * $quantity, 2);

        $order = DB::transaction(function () use ($buyerId, $product, $vendorId, $quantity, $price, $subtotal) {
            $order = Order::query()->create([
                'user_id' => $buyerId,
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => 0,
                'shipping_cost' => 0,
                'total' => $subtotal,
                'shipping_method' => 'marketplace_direct',
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'product_id' => $product->id,
                'product_variant_id' => null,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
            ]);

            $this->vendorOrderService->syncFromOrder($order->fresh('items.product.category'));

            return $order->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'Orden directa creada correctamente.',
            'data' => [
                'order_uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'redirect_to' => '/messages/' . $order->uuid,
            ],
        ], 201);
    }
}
