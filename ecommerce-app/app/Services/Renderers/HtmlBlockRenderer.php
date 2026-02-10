<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

class HtmlBlockRenderer implements SectionRendererInterface
{
    /**
     * Render the HTML block section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'html_content' => $config['html_content'] ?? '',
            'css_classes' => $config['css_classes'] ?? '',
        ];
    }
}
