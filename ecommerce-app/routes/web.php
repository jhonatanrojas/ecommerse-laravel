<?php

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

// Checkout Routes
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/order-success/{orderId?}', function ($orderId = null) {
    return view('order-success');
})->name('order.success');

// Customer Dashboard (guard customer; opcional: verified)
Route::get('/customer', function () {
    return view('customer.dashboard');
})->middleware(['auth:customer', 'verified'])->name('customer.dashboard');

Route::middleware('auth:customer')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
