<?php

namespace App\Providers;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository interface => implementation bindings.
     * Permite invertir dependencias: inyectar interfaces, no implementaciones.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // Añadir aquí más bindings cuando existan:
        // $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
