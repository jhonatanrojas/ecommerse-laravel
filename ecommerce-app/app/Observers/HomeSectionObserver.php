<?php

namespace App\Observers;

use App\Models\HomeSection;
use Illuminate\Support\Facades\Cache;

class HomeSectionObserver
{
    /**
     * Handle the HomeSection "created" event.
     */
    public function created(HomeSection $homeSection): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSection "updated" event.
     */
    public function updated(HomeSection $homeSection): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSection "deleted" event.
     */
    public function deleted(HomeSection $homeSection): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSection "restored" event.
     */
    public function restored(HomeSection $homeSection): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the HomeSection "force deleted" event.
     */
    public function forceDeleted(HomeSection $homeSection): void
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
        \Log::info('Home page cache cleared by HomeSectionObserver');
    }
}
