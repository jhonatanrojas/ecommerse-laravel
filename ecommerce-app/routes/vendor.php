<?php

use App\Http\Controllers\Vendor\VendorAuthController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorDisputeController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\VendorPayoutController;
use App\Http\Controllers\Vendor\VendorProductController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorShippingMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::middleware('guest:vendor')->group(function () {
        Route::get('login', [VendorAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [VendorAuthController::class, 'login'])->name('login.submit');
        Route::get('register', [VendorAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [VendorAuthController::class, 'register'])->name('register.submit');
        Route::get('pending', [VendorAuthController::class, 'pending'])->name('pending');
    });

    Route::post('logout', [VendorAuthController::class, 'logout'])
        ->name('logout')
        ->middleware('auth:vendor');

    Route::middleware(['auth:vendor', 'vendor.approved'])->group(function () {
        Route::get('dashboard', VendorDashboardController::class)->name('dashboard');

        Route::get('products', [VendorProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [VendorProductController::class, 'create'])->name('products.create');
        Route::post('products', [VendorProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [VendorProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [VendorProductController::class, 'update'])->name('products.update');
        Route::patch('products/{product}/toggle', [VendorProductController::class, 'toggle'])->name('products.toggle');

        Route::get('orders', [VendorOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{vendorOrder}', [VendorOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{vendorOrder}/shipping', [VendorOrderController::class, 'updateShipping'])->name('orders.shipping.update');

        Route::get('payouts', [VendorPayoutController::class, 'index'])->name('payouts.index');
        Route::post('payouts/request', [VendorPayoutController::class, 'request'])->name('payouts.request');

        Route::get('profile', [VendorProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [VendorProfileController::class, 'update'])->name('profile.update');

        Route::get('shipping-methods', [VendorShippingMethodController::class, 'index'])->name('shipping-methods.index');
        Route::post('shipping-methods', [VendorShippingMethodController::class, 'store'])->name('shipping-methods.store');
        Route::put('shipping-methods/{method}', [VendorShippingMethodController::class, 'update'])->name('shipping-methods.update');
        Route::delete('shipping-methods/{method}', [VendorShippingMethodController::class, 'destroy'])->name('shipping-methods.destroy');

        Route::get('disputes', [VendorDisputeController::class, 'index'])->name('disputes.index');
        Route::post('disputes', [VendorDisputeController::class, 'store'])->name('disputes.store');
    });
});
