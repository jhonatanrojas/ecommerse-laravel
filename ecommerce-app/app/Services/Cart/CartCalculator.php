<?php

namespace App\Services\Cart;

use App\Enums\CouponType;
use App\Models\Cart;
use App\Models\Coupon;

/**
 * CartCalculator handles all cart total calculations.
 * 
 * This service is responsible for calculating:
 * - Subtotal (sum of price × quantity for all items)
 * - Discount (based on coupon type and value)
 * - Tax (applied on subtotal after discount)
 * - Shipping cost (from configuration)
 * - Total (subtotal - discount + tax + shipping)
 */
class CartCalculator
{
    /**
     * Calculate the subtotal as sum of (price × quantity) for all cart items.
     * 
     * @param Cart $cart
     * @return float
     */
    public function calculateSubtotal(Cart $cart): float
    {
        return $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Calculate discount amount based on coupon type and value.
     * 
     * For fixed coupons: discount = coupon value (capped at subtotal)
     * For percentage coupons: discount = subtotal × (value / 100), capped at max_discount_amount
     * 
     * @param Cart $cart
     * @param Coupon $coupon
     * @return float
     */
    public function calculateDiscount(Cart $cart, Coupon $coupon): float
    {
        $subtotal = $this->calculateSubtotal($cart);

        if ($coupon->type === CouponType::Fixed) {
            // Fixed discount: use coupon value, but don't exceed subtotal
            return min($coupon->value, $subtotal);
        }

        if ($coupon->type === CouponType::Percentage) {
            // Percentage discount: calculate percentage of subtotal
            $discount = $subtotal * ($coupon->value / 100);

            // Apply max_discount_amount cap if defined
            if ($coupon->max_discount_amount !== null) {
                $discount = min($discount, $coupon->max_discount_amount);
            }

            return $discount;
        }

        return 0.0;
    }

    /**
     * Calculate tax on (subtotal - discount).
     * 
     * @param float $subtotalAfterDiscount
     * @return float
     */
    public function calculateTax(float $subtotalAfterDiscount): float
    {
        $taxRate = $this->getTaxRate();
        return $subtotalAfterDiscount * $taxRate;
    }

    /**
     * Calculate shipping cost from configuration.
     * 
     * @param Cart $cart
     * @return float
     */
    public function calculateShipping(Cart $cart): float
    {
        return (float) config('cart.default_shipping_cost', 0);
    }

    /**
     * Calculate total as (subtotal - discount + tax + shipping).
     * 
     * @param Cart $cart
     * @return float
     */
    public function calculateTotal(Cart $cart): float
    {
        $subtotal = $this->calculateSubtotal($cart);
        $discount = $cart->discount_amount ?? 0;
        $subtotalAfterDiscount = $subtotal - $discount;
        $tax = $this->calculateTax($subtotalAfterDiscount);
        $shipping = $this->calculateShipping($cart);

        return $subtotalAfterDiscount + $tax + $shipping;
    }

    /**
     * Get tax rate from configuration.
     * 
     * @return float
     */
    public function getTaxRate(): float
    {
        return (float) config('cart.tax_rate', 0);
    }
}
