<?php

namespace App\Repositories;

use App\Models\HomeSection;
use App\Repositories\Contracts\HomeSectionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeSectionRepository implements HomeSectionRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY = 'home_sections_active';

    /**
     * Get all active home sections ordered by display order.
     *
     * @return Collection
     */
    public function getAllActive(): Collection
    {
        try {
            // Try to use tagged cache if supported
            if (config('cache.default') === 'redis' || config('cache.default') === 'memcached') {
                return Cache::tags(['home_sections'])
                    ->remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                        return $this->fetchActiveSections();
                    });
            }
            
            // Fallback to regular cache for other drivers
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return $this->fetchActiveSections();
            });
        } catch (\Exception $e) {
            \Log::warning('Cache error in getAllActive: ' . $e->getMessage());
            // Return data without cache if cache fails
            return $this->fetchActiveSections();
        }
    }
    
    /**
     * Fetch active sections from database.
     *
     * @return Collection
     */
    private function fetchActiveSections(): Collection
    {
        return HomeSection::active()
            ->ordered()
            ->with(['items.itemable'])
            ->get();
    }

    /**
     * Get a home section by ID with items.
     *
     * @param int $id
     * @return HomeSection
     */
    public function getById(int $id): HomeSection
    {
        return HomeSection::with(['items.itemable'])->findOrFail($id);
    }

    /**
     * Create a new home section.
     *
     * @param array $data
     * @return HomeSection
     */
    public function create(array $data): HomeSection
    {
        return HomeSection::create($data);
    }

    /**
     * Update a home section.
     *
     * @param int $id
     * @param array $data
     * @return HomeSection
     */
    public function update(int $id, array $data): HomeSection
    {
        $section = $this->getById($id);
        $section->update($data);
        return $section->fresh();
    }

    /**
     * Soft delete a home section.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $section = $this->getById($id);
        return $section->delete();
    }

    /**
     * Reorder home sections.
     *
     * @param array $sectionIds
     * @return void
     */
    public function reorder(array $sectionIds): void
    {
        foreach ($sectionIds as $index => $sectionId) {
            HomeSection::where('id', $sectionId)
                ->update(['display_order' => $index]);
        }
    }
}
