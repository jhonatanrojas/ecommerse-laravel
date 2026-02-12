<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Login exclusivo para administradores en /admin/login.
| Resto de rutas protegidas por auth:admin (guard admin).
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Login administrador (solo invitados; si ya está autenticado como admin → dashboard)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('auth:admin');

    // Panel admin: requiere guard admin
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::get('dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Categories CRUD
        Route::resource('categories', CategoryController::class);

        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle-status');

        // Products CRUD
        Route::resource('products', ProductController::class);

        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');
        Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])
            ->name('products.toggle-featured');
        Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage'])
            ->name('products.images.delete');

        // Orders
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->parameters(['orders' => 'uuid']);

        // Users CRUD
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // Store Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('store', [\App\Http\Controllers\Admin\StoreSettingController::class, 'edit'])
                ->name('store.edit');
            Route::put('store', [\App\Http\Controllers\Admin\StoreSettingController::class, 'update'])
                ->name('store.update');
        });

        // Home Sections
        Route::post('home-sections/reorder', [\App\Http\Controllers\Admin\HomeSectionController::class, 'reorder'])
            ->name('home-sections.reorder');
        Route::post('home-sections/{id}/toggle-status', [\App\Http\Controllers\Admin\HomeSectionController::class, 'toggleStatus'])
            ->name('home-sections.toggle-status');
        Route::resource('home-sections', \App\Http\Controllers\Admin\HomeSectionController::class);

        // Menus
        Route::post('menus/{menu}/toggle', [\App\Http\Controllers\Admin\MenuController::class, 'toggle'])->name('menus.toggle');
        Route::post('menus/clear-cache', [\App\Http\Controllers\Admin\MenuController::class, 'clearCache'])->name('menus.clear-cache');
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);

        // Menu Items
        Route::post('menu-items/reorder', [\App\Http\Controllers\Admin\MenuItemController::class, 'reorder'])->name('menu-items.reorder');
        Route::post('menu-items/{item}/toggle', [\App\Http\Controllers\Admin\MenuItemController::class, 'toggle'])->name('menu-items.toggle');
        Route::post('menu-items', [\App\Http\Controllers\Admin\MenuItemController::class, 'store'])->name('menu-items.store');
        Route::put('menu-items/{item}', [\App\Http\Controllers\Admin\MenuItemController::class, 'update'])->name('menu-items.update');
        Route::delete('menu-items/{item}', [\App\Http\Controllers\Admin\MenuItemController::class, 'destroy'])->name('menu-items.destroy');
    });
});
