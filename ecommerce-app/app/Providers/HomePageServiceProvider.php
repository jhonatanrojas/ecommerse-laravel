<?php

namespace App\Providers;

use App\Repositories\Contracts\HomeSectionRepositoryInterface;
use App\Repositories\HomeSectionRepository;
use App\Services\HomeConfigurationService;
use App\Services\HomeSectionRendererService;
use App\Services\Renderers\BannersRenderer;
use App\Services\Renderers\FeaturedCategoriesRenderer;
use App\Services\Renderers\FeaturedProductsRenderer;
use App\Services\Renderers\HeroRenderer;
use App\Services\Renderers\HtmlBlockRenderer;
use App\Services\Renderers\TestimonialsRenderer;
use Illuminate\Support\ServiceProvider;

class HomePageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interface to implementation
        $this->app->bind(
            HomeSectionRepositoryInterface::class,
            HomeSectionRepository::class
        );

        // Register services as singletons
        $this->app->singleton(HomeConfigurationService::class);
        $this->app->singleton(HomeSectionRendererService::class);

        // Register renderer implementations
        $this->app->singleton(HeroRenderer::class);
        $this->app->singleton(FeaturedProductsRenderer::class);
        $this->app->singleton(FeaturedCategoriesRenderer::class);
        $this->app->singleton(BannersRenderer::class);
        $this->app->singleton(TestimonialsRenderer::class);
        $this->app->singleton(HtmlBlockRenderer::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
