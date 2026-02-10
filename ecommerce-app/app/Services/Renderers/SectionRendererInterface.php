<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

interface SectionRendererInterface
{
    /**
     * Render the section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array;
}
