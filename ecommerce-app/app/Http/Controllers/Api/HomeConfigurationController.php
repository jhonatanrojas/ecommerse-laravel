<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeSectionResource;
use App\Services\HomeConfigurationService;
use Illuminate\Support\Facades\Cache;

class HomeConfigurationController extends Controller
{
    public function __construct(
        private HomeConfigurationService $service
    ) {}

    /**
     * Get the complete home page configuration.
     */
    public function index()
    {
        try {
            // Try to use tagged cache if supported
            if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
                $configuration = Cache::tags(['home_sections'])
                    ->remember('api_home_configuration', 3600, function () {
                        return $this->service->getCompleteConfiguration();
                    });
            } else {
                // Fallback to regular cache for other drivers
                $configuration = Cache::remember('api_home_configuration', 3600, function () {
                    return $this->service->getCompleteConfiguration();
                });
            }
        } catch (\Exception $e) {
            \Log::warning('Cache error in home configuration API: ' . $e->getMessage());
            // Return data without cache if cache fails
            $configuration = $this->service->getCompleteConfiguration();
        }

        return HomeSectionResource::collection($configuration);
    }
}
