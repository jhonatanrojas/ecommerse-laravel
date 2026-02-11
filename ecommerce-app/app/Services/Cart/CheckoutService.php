<?php

namespace App\Services\Cart;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\Cart\CartCheckedOut;
use App\Events\Cart\CheckoutFailed;
use App\Exceptions\Cart\CheckoutException;
use App\Exceptions\Cart\InsufficientStockException;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart\DTOs\CheckoutData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct()
    {
        // Dependencies will be injected as needed
    }

    /**
     * Process the complete checkout flow
     * All operations are wrapped in a database transaction for atomicity
     */
    public function processCheckout(Cart $cart, CheckoutData $data): Order
    {
        try {
            $order = DB::transaction(function () use ($cart, $data) {
                // Create the order
                $order = $this->createOrder($cart, $data);

                // Create order items from cart items
                $this->createOrderItems($order, $cart);

                // Decrement stock for all cart items
                $this->decrementStock($cart);

                // Increment coupon usage if coupon was applied
                $this->incrementCouponUsage($cart);

                // Clear the cart after successful checkout
                $this->clearCart($cart);

                return $order;
            });

            // Emit CartCheckedOut event after successful checkout
            event(new CartCheckedOut($cart, $order));

            return $order;
        } catch (InsufficientStockException $e) {
            // Emit CheckoutFailed event
            event(new CheckoutFailed($cart, "Insufficient stock: {$e->getMessage()}"));

            // Re-throw stock exceptions with original message
            throw new CheckoutException(
                "Checkout failed due to insufficient stock: {$e->getMessage()}",
                0,
                $e
            );
        } catch (\Exception $e) {
            // Emit CheckoutFailed event
            event(new CheckoutFailed($cart, $e->getMessage()));

            // Wrap any other exceptions in CheckoutException
            throw new CheckoutException(
                "Checkout failed: {$e->getMessage()}",
                0,
                $e
            );
        }
    }


    /**
     * Create order items from cart items with product snapshot
     */
    private function createOrderItems(Order $order, Cart $cart): void
    {
        // Eager load cart items with their products and variants
        $cart->load(['items.product', 'items.variant']);

        // Get tax rate for item-level tax calculation
        $calculator = app(CartCalculator::class);
        $taxRate = $calculator->getTaxRate();

        foreach ($cart->items as $cartItem) {
            // Get product name and SKU from product or variant
            $productName = $cartItem->product->name;
            $productSku = null;

            if ($cartItem->product_variant_id && $cartItem->variant) {
                // If variant exists, use variant SKU and append variant name
                $productSku = $cartItem->variant->sku;
                $productName .= ' - ' . $cartItem->variant->name;
            } else {
                // Otherwise use product SKU
                $productSku = $cartItem->product->sku;
            }

            // Calculate item totals
            $itemSubtotal = $cartItem->price * $cartItem->quantity;
            $itemTax = $itemSubtotal * $taxRate;
            $itemTotal = $itemSubtotal + $itemTax;

            // Create order item with snapshot of product data
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'product_variant_id' => $cartItem->product_variant_id,
                'product_name' => $productName,
                'product_sku' => $productSku,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'subtotal' => $itemSubtotal,
                'tax' => $itemTax,
                'total' => $itemTotal,
            ]);
        }
    }

    /**
     * Decrement stock for all cart items
     * Uses pessimistic locking to prevent race conditions
     */
    private function decrementStock(Cart $cart): void
    {
        // Eager load cart items
        $cart->load('items');

        foreach ($cart->items as $cartItem) {
            if ($cartItem->product_variant_id) {
                // Decrement variant stock
                $variant = ProductVariant::where('id', $cartItem->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    throw new InsufficientStockException(
                        "Product variant not found (ID: {$cartItem->product_variant_id})"
                    );
                }

                // Validate stock is still sufficient
                if ($variant->stock < $cartItem->quantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for variant '{$variant->name}'. Available: {$variant->stock}, Requested: {$cartItem->quantity}"
                    );
                }

                // Decrement the stock
                $variant->decrement('stock', $cartItem->quantity);
            } else {
                // Decrement product stock
                $product = Product::where('id', $cartItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new InsufficientStockException(
                        "Product not found (ID: {$cartItem->product_id})"
                    );
                }

                // Validate stock is still sufficient
                if ($product->stock < $cartItem->quantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for product '{$product->name}'. Available: {$product->stock}, Requested: {$cartItem->quantity}"
                    );
                }

                // Decrement the stock
                $product->decrement('stock', $cartItem->quantity);
            }
        }
    }

    /**
     * Increment coupon usage count and track user usage
     */
    private function incrementCouponUsage(Cart $cart): void
    {
        // Check if cart has a coupon applied
        if (!$cart->coupon_code) {
            return;
        }

        // Find the coupon by code
        $coupon = Coupon::where('code', $cart->coupon_code)->first();

        if (!$coupon) {
            // Coupon not found - this shouldn't happen if validation was done correctly
            // but we'll handle it gracefully
            return;
        }

        // Increment the coupon's used_count
        $coupon->increment('used_count');

        // If cart has a user, create entry in coupon_user pivot table
        if ($cart->user_id) {
            $coupon->users()->attach($cart->user_id, [
                'used_at' => now(),
            ]);
        }
    }

    /**
     * Clear all items from the cart after successful checkout
     */
    private function clearCart(Cart $cart): void
    {
        // Delete all cart items
        $cart->items()->delete();
    }

    /**
     * Create an order from cart data
     */
    private function createOrder(Cart $cart, CheckoutData $data): Order
    {
        // Generate unique order number
        $orderNumber = $this->generateOrderNumber();

        // Calculate totals using CartCalculator
        $calculator = app(CartCalculator::class);
        $subtotal = $calculator->calculateSubtotal($cart);
        
        // Calculate discount
        $discount = 0;
        if ($cart->coupon_code) {
            $discount = $cart->discount_amount;
        }
        
        // Calculate tax on subtotal after discount
        $tax = $calculator->calculateTax($subtotal - $discount);
        
        // Calculate shipping
        $shippingCost = $calculator->calculateShipping($cart);
        
        // Calculate total
        $total = $subtotal - $discount + $tax + $shippingCost;

        // Create the order
        $order = Order::create([
            'user_id' => $cart->user_id,
            'order_number' => $orderNumber,
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'coupon_code' => $cart->coupon_code,
            'payment_method' => $data->paymentMethod,
            'shipping_method' => $data->shippingMethod,
            'shipping_address_id' => $data->shippingAddressId,
            'billing_address_id' => $data->billingAddressId,
            'customer_notes' => $data->customerNotes,
        ]);

        return $order;
    }

    /**
     * Generate a unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            // Format: ORD-YYYYMMDD-RANDOM (e.g., ORD-20260210-A1B2C3)
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
