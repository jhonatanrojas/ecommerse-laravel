<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

class FeaturedCategoriesRenderer implements SectionRendererInterface
{
    /**
     * Render the featured categories section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;
        $limit = $config['limit'] ?? 6;
        $viewAll = $config['view_all'] ?? [];

        $categories = $section->items()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                if (!$item->itemable) {
                    return null;
                }

                $category = $item->itemable;
                
                // Get product count if the relationship exists
                $productCount = 0;
                if (method_exists($category, 'products')) {
                    $productCount = $category->products()->count();
                }
                
                // Handle category image - convert to full URL if needed
                $imageUrl = null;
                if ($category->image) {
                    $imageUrl = filter_var($category->image, FILTER_VALIDATE_URL) 
                        ? $category->image 
                        : asset('storage/' . $category->image);
                }
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $imageUrl,
                    'product_count' => $productCount,
                    'description' => $category->description ?? null,
                ];
            })
            ->filter()
            ->values();

        return [
            'layout' => $config['layout'] ?? 'grid',
            'columns' => $config['columns'] ?? 3,
            'show_product_count' => $config['show_product_count'] ?? true,
            'view_all' => [
                'enabled' => (bool) ($viewAll['enabled'] ?? true),
                'label' => $viewAll['label'] ?? 'Ver todas',
                'url' => $viewAll['url'] ?? '/categories',
            ],
            'categories' => $categories->toArray(),
        ];
    }
}
