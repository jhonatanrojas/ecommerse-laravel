<?php

namespace App\Observers;

use App\Models\HomeSectionItem;
use Illuminate\Support\Facades\Cache;

class HomeSectionItemObserver
{
    /**
     * Handle the HomeSectionItem "created" event.
     */
    public function created(HomeSectionItem $homeSectionItem): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSectionItem "updated" event.
     */
    public function updated(HomeSectionItem $homeSectionItem): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSectionItem "deleted" event.
     */
    public function deleted(HomeSectionItem $homeSectionItem): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSectionItem "restored" event.
     */
    public function restored(HomeSectionItem $homeSectionItem): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSectionItem "force deleted" event.
     */
    public function forceDeleted(HomeSectionItem $homeSectionItem): void
    {
        $this->invalidateCache();
    }

    /**
     * Invalidate all home sections cache.
     */
    private function invalidateCache(): void
    {
        try {
            // Clear tagged cache (only works with Redis/Memcached)
            if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
                Cache::tags(['home_sections'])->flush();
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to clear tagged cache: ' . $e->getMessage());
        }
        
        // Clear specific cache keys (works with all drivers)
        Cache::forget('home_sections_active');
        Cache::forget('api_home_configuration');
        
        // Log cache clear for debugging
        \Log::info('Home page cache cleared by HomeSectionItemObserver');
    }
}
