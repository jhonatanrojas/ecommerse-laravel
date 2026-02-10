<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomeSectionRequest;
use App\Http\Requests\UpdateHomeSectionRequest;
use App\Models\HomeSection;
use App\Repositories\Contracts\HomeSectionRepositoryInterface;
use App\Services\HomeConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeSectionController extends Controller
{
    public function __construct(
        private HomeSectionRepositoryInterface $repository,
        private HomeConfigurationService $service
    ) {}

    /**
     * Display a listing of home sections.
     */
    public function index()
    {
        $sections = HomeSection::withTrashed()
            ->ordered()
            ->get();

        return view('admin.home-sections.index', [
            'sections' => $sections,
        ]);
    }

    /**
     * Show the form for creating a new home section.
     */
    public function create()
    {
        return view('admin.home-sections.create');
    }

    /**
     * Store a newly created home section.
     */
    public function store(StoreHomeSectionRequest $request)
    {
        $data = $request->validated();
        
        // Process configuration based on type
        $data['configuration'] = $this->processConfiguration($request->input('configuration', []), $request->input('type'));
        
        $this->repository->create($data);
        
        // Clear cache explicitly
        $this->clearHomeCache();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'Section created successfully');
    }

    /**
     * Show the form for editing the specified home section.
     */
    public function edit(int $id)
    {
        $section = HomeSection::withTrashed()->findOrFail($id);

        return view('admin.home-sections.edit', [
            'section' => $section,
        ]);
    }

    /**
     * Update the specified home section.
     */
    public function update(UpdateHomeSectionRequest $request, int $id)
    {
        $data = $request->validated();
        
        // Process configuration based on type
        $data['configuration'] = $this->processConfiguration($request->input('configuration', []), $request->input('type'));
        
        $this->repository->update($id, $data);
        
        // Clear cache explicitly
        $this->clearHomeCache();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'Section updated successfully');
    }
    
    /**
     * Process configuration data based on section type.
     */
    private function processConfiguration(array $config, string $type): array
    {
        // Decode JSON strings for complex fields
        if (isset($config['cta_buttons']) && is_string($config['cta_buttons'])) {
            $config['cta_buttons'] = json_decode($config['cta_buttons'], true) ?? [];
        }
        
        if (isset($config['banners']) && is_string($config['banners'])) {
            $config['banners'] = json_decode($config['banners'], true) ?? [];
        }
        
        if (isset($config['testimonials']) && is_string($config['testimonials'])) {
            $config['testimonials'] = json_decode($config['testimonials'], true) ?? [];
        }
        
        // Convert checkbox values to boolean
        $booleanFields = ['show_price', 'show_rating', 'show_avatar', 'show_product_count', 'autoplay'];
        foreach ($booleanFields as $field) {
            if (isset($config[$field])) {
                $config[$field] = (bool) $config[$field];
            }
        }
        
        // Convert numeric fields
        $numericFields = ['limit', 'columns', 'autoplay_speed', 'overlay_opacity'];
        foreach ($numericFields as $field) {
            if (isset($config[$field])) {
                $config[$field] = is_numeric($config[$field]) ? (float) $config[$field] : $config[$field];
            }
        }
        
        return $config;
    }

    /**
     * Remove the specified home section.
     */
    public function destroy(int $id)
    {
        // Use withTrashed to find soft deleted records
        $homeSection = HomeSection::withTrashed()->findOrFail($id);
        
        if ($homeSection->trashed()) {
            // If already soft deleted, force delete permanently
            $homeSection->forceDelete();
            $message = 'Section permanently deleted';
        } else {
            // Soft delete
            $this->repository->delete($id);
            $message = 'Section deleted successfully';
        }
        
        // Clear cache explicitly
        $this->clearHomeCache();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Reorder home sections.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'section_ids' => 'required|array',
            'section_ids.*' => 'required|integer|exists:home_sections,id',
        ]);

        $this->service->reorderSections($request->section_ids);
        
        // Clear cache explicitly
        $this->clearHomeCache();

        return response()->json([
            'success' => true,
            'message' => 'Sections reordered successfully',
        ]);
    }

    /**
     * Toggle section active status.
     */
    public function toggleStatus(int $id)
    {
        $section = $this->repository->getById($id);
        $newStatus = !$section->is_active;

        $this->service->toggleSectionStatus($id, $newStatus);
        
        // Clear cache explicitly
        $this->clearHomeCache();

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
        ]);
    }
    
    /**
     * Clear all home page related caches.
     */
    private function clearHomeCache(): void
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
        \Log::info('Home page cache cleared from admin panel');
    }
}
