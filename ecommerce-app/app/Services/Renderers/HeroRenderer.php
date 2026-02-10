<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

class HeroRenderer implements SectionRendererInterface
{
    /**
     * Render the hero section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'title' => $config['title'] ?? '',
            'subtitle' => $config['subtitle'] ?? '',
            'background_image' => $config['background_image'] ?? null,
            'background_video' => $config['background_video'] ?? null,
            'cta_buttons' => $config['cta_buttons'] ?? [],
            'overlay_opacity' => $config['overlay_opacity'] ?? 0.5,
        ];
    }
}
