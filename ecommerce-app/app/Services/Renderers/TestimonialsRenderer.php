<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

class TestimonialsRenderer implements SectionRendererInterface
{
    /**
     * Render the testimonials section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'layout' => $config['layout'] ?? 'carousel',
            'show_rating' => $config['show_rating'] ?? true,
            'show_avatar' => $config['show_avatar'] ?? true,
            'testimonials' => $config['testimonials'] ?? [],
        ];
    }
}
