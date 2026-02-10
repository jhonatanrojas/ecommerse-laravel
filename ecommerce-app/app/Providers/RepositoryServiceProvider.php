<?php

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\EloquentCategoryRepository;
use App\Repositories\EloquentProductRepository;
use App\Repositories\Eloquent\EloquentOrderRepository;
use App\Services\CategoryService;
use App\Services\Contracts\CategoryServiceInterface;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\OrderStatusServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Order\OrderService;
use App\Services\Order\OrderStatusService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Category Bindings
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );

        $this->app->bind(
            CategoryServiceInterface::class,
            CategoryService::class
        );

        // Product Bindings
        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );

        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );

        // Order Bindings
        $this->app->bind(
            OrderRepositoryInterface::class,
            EloquentOrderRepository::class
        );

        $this->app->bind(
            OrderServiceInterface::class,
            OrderService::class
        );

        $this->app->bind(
            OrderStatusServiceInterface::class,
            OrderStatusService::class
        );

        // Store Settings Bindings
        $this->app->bind(
            \App\Repositories\Contracts\StoreSettingRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentStoreSettingRepository::class
        );

        $this->app->bind(
            \App\Services\Contracts\StoreSettingServiceInterface::class,
            \App\Services\StoreSettingService::class
        );

        // File Service Binding
        $this->app->bind(
            \App\Services\Contracts\FileServiceInterface::class,
            \App\Services\FileService::class
        );

        // Home Section Bindings
        $this->app->bind(
            \App\Repositories\Contracts\HomeSectionRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentHomeSectionRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
