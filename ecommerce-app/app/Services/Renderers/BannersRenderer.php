<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

class BannersRenderer implements SectionRendererInterface
{
    /**
     * Render the banners section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'layout' => $config['layout'] ?? 'slider',
            'autoplay' => $config['autoplay'] ?? true,
            'autoplay_speed' => $config['autoplay_speed'] ?? 5000,
            'banners' => $config['banners'] ?? [],
        ];
    }
}
