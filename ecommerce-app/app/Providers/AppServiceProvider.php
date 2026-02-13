<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // User Module Bindings
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Services\Contracts\UserServiceInterface::class,
            \App\Services\UserService::class
        );

        // Payment Status Service Binding
        $this->app->bind(
            \App\Services\Contracts\PaymentStatusServiceInterface::class,
            \App\Services\Payments\PaymentStatusService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        \App\Models\HomeSection::observe(\App\Observers\HomeSectionObserver::class);
        \App\Models\HomeSectionItem::observe(\App\Observers\HomeSectionItemObserver::class);
    }
}
