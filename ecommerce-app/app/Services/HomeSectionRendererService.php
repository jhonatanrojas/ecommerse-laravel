<?php

namespace App\Services;

use App\Exceptions\InvalidSectionTypeException;
use App\Models\HomeSection;
use App\Services\Renderers\BannersRenderer;
use App\Services\Renderers\FeaturedCategoriesRenderer;
use App\Services\Renderers\FeaturedProductsRenderer;
use App\Services\Renderers\HeroRenderer;
use App\Services\Renderers\HtmlBlockRenderer;
use App\Services\Renderers\SectionRendererInterface;
use App\Services\Renderers\TestimonialsRenderer;

class HomeSectionRendererService
{
    private array $renderers = [];

    public function __construct(
        private HeroRenderer $heroRenderer,
        private FeaturedProductsRenderer $featuredProductsRenderer,
        private FeaturedCategoriesRenderer $featuredCategoriesRenderer,
        private BannersRenderer $bannersRenderer,
        private TestimonialsRenderer $testimonialsRenderer,
        private HtmlBlockRenderer $htmlBlockRenderer
    ) {
        $this->registerRenderers();
    }

    /**
     * Register all section renderers.
     *
     * @return void
     */
    private function registerRenderers(): void
    {
        $this->renderers = [
            'hero' => $this->heroRenderer,
            'featured_products' => $this->featuredProductsRenderer,
            'featured_categories' => $this->featuredCategoriesRenderer,
            'banners' => $this->bannersRenderer,
            'testimonials' => $this->testimonialsRenderer,
            'html_block' => $this->htmlBlockRenderer,
        ];
    }

    /**
     * Render a home section using the appropriate renderer.
     *
     * @param HomeSection $section
     * @return array
     * @throws InvalidSectionTypeException
     */
    public function render(HomeSection $section): array
    {
        if (!isset($this->renderers[$section->type])) {
            throw new InvalidSectionTypeException(
                "No renderer found for section type: {$section->type}"
            );
        }

        return $this->renderers[$section->type]->render($section);
    }
}
