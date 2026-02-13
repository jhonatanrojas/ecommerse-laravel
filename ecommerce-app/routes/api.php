<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController as PublicCategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\Marketplace\VendorPublicController;
use App\Http\Controllers\Api\Marketplace\VendorRegistrationController;
use App\Http\Controllers\Api\Marketplace\DirectOrderController;
use App\Http\Controllers\Api\Marketplace\ProductQuestionController;
use App\Http\Controllers\Api\Marketplace\ProductReviewController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\ProductController as PublicProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController as ManageProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

Route::post('register', [AuthController::class , 'register']);
Route::post('login', [AuthController::class , 'login']);
Route::post('marketplace/vendors/register', [VendorRegistrationController::class, 'store']);
Route::post('payments/webhook', [PaymentWebhookController::class, 'handle']);
Route::get('search/autocomplete', [SearchController::class, 'autocomplete']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class , 'logout']);

    // Customer Backend API Routes - User profile and password
    Route::get('/user', [\App\Http\Controllers\Api\UserController::class, 'show']);
    Route::put('/user/password', [\App\Http\Controllers\Api\UserController::class, 'updatePassword']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/{productId}', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);

    Route::prefix('customer')->group(function () {
        // Customer Orders
        Route::get('/orders', [\App\Http\Controllers\Api\CustomerOrderController::class, 'index']);
        Route::get('/orders/{uuid}', [\App\Http\Controllers\Api\CustomerOrderController::class, 'show']);

        // Customer Addresses
        Route::get('/addresses', [\App\Http\Controllers\Api\CustomerAddressController::class, 'index']);
        Route::post('/addresses', [\App\Http\Controllers\Api\CustomerAddressController::class, 'store']);
        Route::put('/addresses/{uuid}', [\App\Http\Controllers\Api\CustomerAddressController::class, 'update']);
        Route::delete('/addresses/{uuid}', [\App\Http\Controllers\Api\CustomerAddressController::class, 'destroy']);
        Route::put('/default-address', [\App\Http\Controllers\Api\CustomerAddressController::class, 'setDefaultAddress']);
    });
});

Route::get('products', [PublicProductController::class, 'index']);
Route::get('products/{slug}/related', [PublicProductController::class, 'related']);
Route::get('products/{slug}', [PublicProductController::class, 'show']);
Route::get('categories', [PublicCategoryController::class, 'index']);
Route::get('categories/{slug}/products', [PublicCategoryController::class, 'products']);
Route::get('marketplace/products', [VendorPublicController::class, 'marketplace']);
Route::get('marketplace/search', [VendorPublicController::class, 'search']);
Route::get('marketplace/vendors', [VendorPublicController::class, 'vendors']);
Route::get('marketplace/vendors/{slug}', [VendorPublicController::class, 'profile']);
Route::get('marketplace/vendors/{slug}/products', [VendorPublicController::class, 'products']);
Route::get('marketplace/products/{slug}', [VendorPublicController::class, 'showProduct']);
Route::get('marketplace/products/{slug}/reviews', [ProductReviewController::class, 'index']);
Route::post('orders/direct', [DirectOrderController::class, 'store']);
Route::get('products/{id}/questions', [ProductQuestionController::class, 'index']);
Route::post('products/{id}/questions', [ProductQuestionController::class, 'store']);
Route::middleware('auth:sanctum')->post('marketplace/products/{slug}/reviews', [ProductReviewController::class, 'store']);

// Home Configuration (Public API)
Route::get('home-configuration', [\App\Http\Controllers\Api\HomeConfigurationController::class , 'index']);

// Store Configuration (Public API)
Route::get('store-config', [\App\Http\Controllers\Api\StoreConfigController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('products', [ManageProductController::class , 'store']);
    Route::put('products/{product}', [ManageProductController::class , 'update']);
    Route::delete('products/{product}', [ManageProductController::class , 'destroy']);

    Route::post('payments', [PaymentController::class, 'store']);
    Route::get('payments/{uuid}', [PaymentController::class, 'show']);
    Route::post('payments/{uuid}/refund', [PaymentController::class, 'refund']);
});

// Menus Public API
Route::get('/menus/location/{location}', [\App\Http\Controllers\Api\MenuController::class , 'getByLocation']);
Route::get('/menus/key/{key}', [\App\Http\Controllers\Api\MenuController::class , 'getByKey']);

// Cart API Routes
Route::prefix('cart')->group(function () {
    // Guest and authenticated users can access cart
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/items', [CartController::class, 'store'])->name('cart.items.store');
    Route::put('/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
    
    // Coupon management
    Route::post('/coupon', [CouponController::class, 'store'])->name('cart.coupon.store');
    Route::delete('/coupon', [CouponController::class, 'destroy'])->name('cart.coupon.destroy');
    
    // Checkout (conditional authentication based on store settings)
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('cart.checkout');
});
