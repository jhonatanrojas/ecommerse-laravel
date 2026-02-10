<?php

namespace App\Console\Commands;

use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;

class LinkHomeSectionItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'home:link-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link products and categories to home sections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Linking items to home sections...');

        // Link Featured Products
        $featuredProductsSection = HomeSection::where('type', 'featured_products')->first();
        if ($featuredProductsSection) {
            // Delete existing items
            $featuredProductsSection->items()->delete();
            
            // Add new items
            $products = Product::where('is_featured', true)->take(8)->get();
            foreach ($products as $index => $product) {
                HomeSectionItem::create([
                    'home_section_id' => $featuredProductsSection->id,
                    'itemable_type' => Product::class,
                    'itemable_id' => $product->id,
                    'display_order' => $index,
                ]);
            }
            $this->info("✓ Linked {$products->count()} products to Featured Products section");
        }

        // Link Featured Categories
        $featuredCategoriesSection = HomeSection::where('type', 'featured_categories')->first();
        if ($featuredCategoriesSection) {
            // Delete existing items
            $featuredCategoriesSection->items()->delete();
            
            // Add new items
            $categories = Category::take(6)->get();
            foreach ($categories as $index => $category) {
                HomeSectionItem::create([
                    'home_section_id' => $featuredCategoriesSection->id,
                    'itemable_type' => Category::class,
                    'itemable_id' => $category->id,
                    'display_order' => $index,
                ]);
            }
            $this->info("✓ Linked {$categories->count()} categories to Featured Categories section");
        }

        $this->info('Done!');
        
        return Command::SUCCESS;
    }
}
