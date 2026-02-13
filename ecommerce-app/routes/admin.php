<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCustomerAddressController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminCustomerOrderController;
use App\Http\Controllers\Admin\AdminOrderShippingStatusUpdateController;
use App\Http\Controllers\Admin\AdminOrderStatusController;
use App\Http\Controllers\Admin\AdminOrderStatusUpdateController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminPaymentMethodController;
use App\Http\Controllers\Admin\AdminShippingStatusController;
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
        
        // Update payment status
        Route::patch('orders/{uuid}/payment-status', [\App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])
            ->name('orders.payment-status.update');
        Route::patch('orders/{uuid}/status', [AdminOrderStatusUpdateController::class, 'update'])
            ->name('orders.status.update');

        // Users CRUD
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // Pages CMS
        Route::get('pages', [AdminPageController::class, 'index'])
            ->name('pages.index')
            ->middleware('permission:manage_pages');
        Route::get('pages/create', [AdminPageController::class, 'create'])
            ->name('pages.create')
            ->middleware('permission:manage_pages');
        Route::post('pages', [AdminPageController::class, 'store'])
            ->name('pages.store')
            ->middleware('permission:manage_pages');
        Route::get('pages/{uuid}/edit', [AdminPageController::class, 'edit'])
            ->name('pages.edit')
            ->middleware('permission:edit_pages|manage_pages');
        Route::put('pages/{uuid}', [AdminPageController::class, 'update'])
            ->name('pages.update')
            ->middleware('permission:edit_pages|manage_pages');
        Route::patch('pages/{uuid}/toggle', [AdminPageController::class, 'togglePublish'])
            ->name('pages.toggle')
            ->middleware('permission:edit_pages|manage_pages');
        Route::delete('pages/{uuid}', [AdminPageController::class, 'destroy'])
            ->name('pages.destroy')
            ->middleware('permission:delete_pages|manage_pages');

        // Customers
        Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::put('customers/{id}', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::patch('customers/{id}/toggle', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle');
        Route::get('customers/{id}/orders', [AdminCustomerOrderController::class, 'index'])->name('customers.orders.index');
        Route::get('customers/{id}/addresses', [AdminCustomerAddressController::class, 'index'])->name('customers.addresses.index');

        // Store Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('store', [\App\Http\Controllers\Admin\StoreSettingController::class, 'edit'])
                ->name('store.edit');
            Route::put('store', [\App\Http\Controllers\Admin\StoreSettingController::class, 'update'])
                ->name('store.update');
        });

        // Payment methods (admin)
        Route::get('payment-methods', [AdminPaymentMethodController::class, 'index'])
            ->name('payment-methods.index');
        Route::get('payment-methods/create', [AdminPaymentMethodController::class, 'create'])
            ->name('payment-methods.create');
        Route::post('payment-methods', [AdminPaymentMethodController::class, 'store'])
            ->name('payment-methods.store');
        Route::get('payment-methods/{payment_method}/edit', [AdminPaymentMethodController::class, 'edit'])
            ->name('payment-methods.edit');
        Route::put('payment-methods/{payment_method}', [AdminPaymentMethodController::class, 'update'])
            ->name('payment-methods.update');
        Route::patch('payment-methods/{payment_method}/toggle', [AdminPaymentMethodController::class, 'toggleStatus'])
            ->name('payment-methods.toggle');
        Route::delete('payment-methods/{payment_method}', [AdminPaymentMethodController::class, 'destroy'])
            ->name('payment-methods.destroy');

        // Order statuses (admin)
        Route::get('order-statuses', [AdminOrderStatusController::class, 'index'])
            ->name('order-statuses.index');
        Route::post('order-statuses', [AdminOrderStatusController::class, 'store'])
            ->name('order-statuses.store');
        Route::put('order-statuses/{id}', [AdminOrderStatusController::class, 'update'])
            ->name('order-statuses.update');
        Route::patch('order-statuses/{id}/toggle', [AdminOrderStatusController::class, 'toggleStatus'])
            ->name('order-statuses.toggle');
        Route::patch('order-statuses/{id}/default', [AdminOrderStatusController::class, 'setDefault'])
            ->name('order-statuses.default');
        Route::delete('order-statuses/{id}', [AdminOrderStatusController::class, 'destroy'])
            ->name('order-statuses.destroy');

        // Shipping statuses (admin)
        Route::get('shipping-statuses', [AdminShippingStatusController::class, 'index'])
            ->name('shipping-statuses.index');
        Route::post('shipping-statuses', [AdminShippingStatusController::class, 'store'])
            ->name('shipping-statuses.store');
        Route::put('shipping-statuses/{id}', [AdminShippingStatusController::class, 'update'])
            ->name('shipping-statuses.update');
        Route::patch('shipping-statuses/{id}/toggle', [AdminShippingStatusController::class, 'toggleStatus'])
            ->name('shipping-statuses.toggle');
        Route::patch('shipping-statuses/{id}/default', [AdminShippingStatusController::class, 'setDefault'])
            ->name('shipping-statuses.default');
        Route::delete('shipping-statuses/{id}', [AdminShippingStatusController::class, 'destroy'])
            ->name('shipping-statuses.destroy');

        // Update shipping status from order
        Route::patch('orders/{uuid}/shipping-status', [AdminOrderShippingStatusUpdateController::class, 'update'])
            ->name('orders.shipping-status.update');

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
