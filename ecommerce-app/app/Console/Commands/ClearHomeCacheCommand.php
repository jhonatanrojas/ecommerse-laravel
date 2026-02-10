<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearHomeCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'home:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all home page related caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing home page cache...');
        
        try {
            // Clear tagged cache (only works with Redis/Memcached)
            if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
                Cache::tags(['home_sections'])->flush();
                $this->info('✓ Tagged cache cleared');
            } else {
                $this->warn('⚠ Tagged cache not supported with ' . config('cache.default') . ' driver');
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear tagged cache: ' . $e->getMessage());
        }
        
        // Clear specific cache keys (works with all drivers)
        Cache::forget('home_sections_active');
        Cache::forget('api_home_configuration');
        $this->info('✓ Specific cache keys cleared');
        
        $this->info('✓ Home page cache cleared successfully!');
        
        return Command::SUCCESS;
    }
}
