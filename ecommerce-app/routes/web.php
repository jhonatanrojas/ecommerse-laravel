<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/products', function () {
    return view('products');
})->name('products.index');

Route::get('/products/{slug}', function () {
    return view('product-detail');
})->name('products.show');

Route::view('/marketplace', 'marketplace')->name('marketplace.index');
Route::view('/marketplace/search', 'marketplace')->name('marketplace.search');
Route::view('/marketplace/vendors/{slug}', 'marketplace')->name('marketplace.vendors.show');
Route::view('/marketplace/products/{slug}', 'marketplace')->name('marketplace.products.show');
Route::view('/messages/{orderUuid}', 'marketplace')->name('messages.show');

Route::get('/categories', function () {
    return view('categories');
})->name('categories.index');

Route::get('/categories/{slug}', function () {
    return view('category-products');
})->name('categories.show');

// Checkout Routes
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/order-success/{orderId?}', function ($orderId = null) {
    return view('order-success');
})->name('order.success');

Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// Customer App (dashboard, órdenes y detalle)
Route::middleware(['auth:customer', 'verified'])->group(function () {
    Route::view('/customer', 'customer.dashboard')->name('customer.dashboard');
    Route::view('/customer/orders', 'customer.dashboard')->name('customer.orders');
    Route::view('/customer/orders/{id}', 'customer.dashboard')->name('customer.orders.show');
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/vendor.php';
