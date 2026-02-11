<?php

namespace App\Services\Cart;

use App\Models\Coupon;
use App\Models\User;
use App\Services\Cart\DTOs\ValidationResult;
use Illuminate\Support\Facades\DB;

class CouponValidator
{
    /**
     * Validate all coupon rules.
     *
     * @param Coupon $coupon
     * @param float $subtotal
     * @param User|null $user
     * @return ValidationResult
     */
    public function validate(Coupon $coupon, float $subtotal, ?User $user): ValidationResult
    {
        // Check if coupon is active
        if (!$this->isActive($coupon)) {
            return ValidationResult::fail("Coupon '{$coupon->code}' is not active.");
        }

        // Check if coupon is within date range
        if (!$this->isWithinDateRange($coupon)) {
            $now = now();
            if ($coupon->starts_at && $now->isBefore($coupon->starts_at)) {
                return ValidationResult::fail("Coupon '{$coupon->code}' is not yet valid. It starts on {$coupon->starts_at->format('Y-m-d')}.");
            }
            return ValidationResult::fail("Coupon '{$coupon->code}' has expired.");
        }

        // Check if coupon has reached global usage limit
        if ($this->hasReachedUsageLimit($coupon)) {
            return ValidationResult::fail("Coupon '{$coupon->code}' has reached its usage limit.");
        }

        // Check if user has reached their personal usage limit
        if ($user && $this->hasUserReachedLimit($coupon, $user)) {
            return ValidationResult::fail("You have already used coupon '{$coupon->code}' the maximum number of times.");
        }

        // Check if cart meets minimum purchase requirement
        if (!$this->meetsMinimumPurchase($coupon, $subtotal)) {
            $minAmount = number_format($coupon->min_purchase_amount, 2);
            return ValidationResult::fail("Coupon '{$coupon->code}' requires a minimum purchase of \${$minAmount}.");
        }

        return ValidationResult::success();
    }

    /**
     * Check if coupon is active.
     *
     * @param Coupon $coupon
     * @return bool
     */
    public function isActive(Coupon $coupon): bool
    {
        return $coupon->is_active === true;
    }

    /**
     * Check if coupon is within its valid date range.
     *
     * @param Coupon $coupon
     * @return bool
     */
    public function isWithinDateRange(Coupon $coupon): bool
    {
        $now = now();

        // Check if coupon has started
        if ($coupon->starts_at && $now->isBefore($coupon->starts_at)) {
            return false;
        }

        // Check if coupon has expired
        if ($coupon->expires_at && $now->isAfter($coupon->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon has reached its global usage limit.
     *
     * @param Coupon $coupon
     * @return bool
     */
    public function hasReachedUsageLimit(Coupon $coupon): bool
    {
        // If no usage limit is set, it can be used unlimited times
        if ($coupon->usage_limit === null) {
            return false;
        }

        return $coupon->used_count >= $coupon->usage_limit;
    }

    /**
     * Check if user has reached their personal usage limit for this coupon.
     *
     * @param Coupon $coupon
     * @param User $user
     * @return bool
     */
    public function hasUserReachedLimit(Coupon $coupon, User $user): bool
    {
        // If no per-user limit is set, user can use it unlimited times
        if ($coupon->usage_limit_per_user === null) {
            return false;
        }

        // Count how many times this user has used this coupon
        $userUsageCount = DB::table('coupon_user')
            ->where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->count();

        return $userUsageCount >= $coupon->usage_limit_per_user;
    }

    /**
     * Check if cart subtotal meets the minimum purchase requirement.
     *
     * @param Coupon $coupon
     * @param float $subtotal
     * @return bool
     */
    public function meetsMinimumPurchase(Coupon $coupon, float $subtotal): bool
    {
        // If no minimum purchase is set, any amount is valid
        if ($coupon->min_purchase_amount === null) {
            return true;
        }

        return $subtotal >= $coupon->min_purchase_amount;
    }
}
