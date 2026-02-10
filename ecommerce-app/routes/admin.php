<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Admin Routes |-------------------------------------------------------------------------- */

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {

    // Categories CRUD
    Route::resource('categories', CategoryController::class);

    // Toggle category status
    Route::patch('categories/{category}/toggle-status', [CategoryController::class , 'toggleStatus'])
        ->name('categories.toggle-status');

    // Products CRUD
    Route::resource('products', ProductController::class);

    // Toggle product status
    Route::patch('products/{product}/toggle-status', [ProductController::class , 'toggleStatus'])
        ->name('products.toggle-status');

    // Toggle product featured
    Route::patch('products/{product}/toggle-featured', [ProductController::class , 'toggleFeatured'])
        ->name('products.toggle-featured');

    // Delete product image
    Route::delete('products/{product}/images/{image}', [ProductController::class , 'deleteImage'])
        ->name('products.images.delete');

    // Orders Management (CRUD parcial: index, show, update, destroy)
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)
        ->only(['index', 'show', 'update', 'destroy'])
        ->parameters(['orders' => 'uuid']);

    // Users CRUD
    Route::resource('users', UserController::class);

    // Toggle user status
    Route::patch('users/{user}/toggle-status', [UserController::class , 'toggleStatus'])
        ->name('users.toggle-status');

    // Store Settings
    Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('store', [\App\Http\Controllers\Admin\StoreSettingController::class , 'edit'])
                ->name('store.edit');
            Route::put('store', [\App\Http\Controllers\Admin\StoreSettingController::class , 'update'])
                ->name('store.update');
        }
        );

        // Home Sections Management
        // Custom routes BEFORE resource routes to avoid conflicts
        Route::post('home-sections/reorder', [\App\Http\Controllers\Admin\HomeSectionController::class , 'reorder'])
            ->name('home-sections.reorder');
        Route::post('home-sections/{id}/toggle-status', [\App\Http\Controllers\Admin\HomeSectionController::class , 'toggleStatus'])
            ->name('home-sections.toggle-status');

        // Resource routes
        Route::resource('home-sections', \App\Http\Controllers\Admin\HomeSectionController::class);

        // Menus Management
        Route::post('menus/{menu}/toggle', [\App\Http\Controllers\Admin\MenuController::class , 'toggle'])->name('menus.toggle');
        Route::post('menus/clear-cache', [\App\Http\Controllers\Admin\MenuController::class , 'clearCache'])->name('menus.clear-cache');
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);

        // Menu Items Management
        Route::post('menu-items/reorder', [\App\Http\Controllers\Admin\MenuItemController::class , 'reorder'])->name('menu-items.reorder');
        Route::post('menu-items/{item}/toggle', [\App\Http\Controllers\Admin\MenuItemController::class , 'toggle'])->name('menu-items.toggle');
        Route::post('menu-items', [\App\Http\Controllers\Admin\MenuItemController::class , 'store'])->name('menu-items.store');
        Route::put('menu-items/{item}', [\App\Http\Controllers\Admin\MenuItemController::class , 'update'])->name('menu-items.update');
        Route::delete('menu-items/{item}', [\App\Http\Controllers\Admin\MenuItemController::class , 'destroy'])->name('menu-items.destroy');    });
