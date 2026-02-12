<?php

namespace App\Services\Renderers;

use App\Models\HomeSection;

class FeaturedProductsRenderer implements SectionRendererInterface
{
    /**
     * Render the featured products section data.
     *
     * @param HomeSection $section
     * @return array
     */
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;
        $limit = $config['limit'] ?? 8;
        $viewAll = $config['view_all'] ?? [];

        $products = $section->items()
            ->with(['itemable.images', 'itemable.category'])
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                if (!$item->itemable) {
                    return null;
                }

                $product = $item->itemable;
                
                // Get image URL - handle both ProductImage relationship and direct path
                $imageUrl = null;
                $firstImage = $product->images->first();
                if ($firstImage) {
                    $imageUrl = $firstImage->url;
                } elseif (isset($product->image) && $product->image) {
                    // Fallback to direct image field if it exists
                    $imageUrl = filter_var($product->image, FILTER_VALIDATE_URL) 
                        ? $product->image 
                        : asset('storage/' . $product->image);
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price ?? null,
                    'image' => $imageUrl,
                    'category' => $product->category?->name ?? null,
                    'rating' => $product->average_rating ?? 0,
                ];
            })
            ->filter()
            ->values();

        return [
            'layout' => $config['layout'] ?? 'grid',
            'columns' => $config['columns'] ?? 4,
            'show_price' => $config['show_price'] ?? true,
            'show_rating' => $config['show_rating'] ?? true,
            'view_all' => [
                'enabled' => (bool) ($viewAll['enabled'] ?? true),
                'label' => $viewAll['label'] ?? 'Ver todos',
                'url' => $viewAll['url'] ?? '/products',
            ],
            'products' => $products->toArray(),
        ];
    }
}
