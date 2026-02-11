<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

Route::post('register', [AuthController::class , 'register']);
Route::post('login', [AuthController::class , 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
            return $request->user();
        }
        );
        Route::post('logout', [AuthController::class , 'logout']);    });

Route::get('products', [ProductController::class , 'index']);
Route::get('products/{product}', [ProductController::class , 'show']);

// Home Configuration (Public API)
Route::get('home-configuration', [\App\Http\Controllers\Api\HomeConfigurationController::class , 'index']);

// Store Configuration (Public API)
Route::get('store-config', [\App\Http\Controllers\Api\StoreConfigController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('products', [ProductController::class , 'store']);
    Route::put('products/{product}', [ProductController::class , 'update']);
    Route::delete('products/{product}', [ProductController::class , 'destroy']);
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

