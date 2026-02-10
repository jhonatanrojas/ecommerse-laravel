# Design Document: Dynamic Home Page Management System

## Overview

The Dynamic Home Page Management System is a Laravel-based solution that enables administrators to configure, manage, and reorder home page sections through an intuitive admin interface. The system uses a layered architecture with Repository Pattern for data access, Service Layer for business logic, and Strategy Pattern for section-specific rendering. Configuration is cached and exposed via a public API endpoint for consumption by a Vue/Inertia frontend.

### Key Design Principles

1. **Separation of Concerns**: Clear boundaries between data access (Repository), business logic (Services), and presentation (Controllers/Resources)
2. **Strategy Pattern**: Pluggable renderers for different section types without conditional complexity
3. **Cache-First Architecture**: Aggressive caching with automatic invalidation via model observers
4. **SOLID Principles**: Single responsibility, dependency injection, interface-based contracts
5. **Performance**: Eager loading, database indexes, and caching to minimize query overhead

## Architecture

### System Layers

```
┌─────────────────────────────────────────────────────────────┐
│                     Presentation Layer                       │
│  ┌──────────────────────┐    ┌──────────────────────────┐  │
│  │  Admin Controllers   │    │   API Controllers        │  │
│  │  (Inertia/Blade)     │    │   (JSON Resources)       │  │
│  └──────────────────────┘    └──────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Service Layer                           │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  HomeConfigurationService                             │  │
│  │  - getCompleteConfiguration()                         │  │
│  │  - toggleSectionStatus()                              │  │
│  │  - reorderSections()                                  │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  HomeSectionRendererService (Strategy Coordinator)    │  │
│  │  - render(HomeSection): array                         │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Section Renderers (Strategy Implementations)         │  │
│  │  - HeroRenderer                                       │  │
│  │  - FeaturedProductsRenderer                           │  │
│  │  - FeaturedCategoriesRenderer                         │  │
│  │  - BannersRenderer                                    │  │
│  │  - TestimonialsRenderer                               │  │
│  │  - HtmlBlockRenderer                                  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Repository Layer                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  HomeSectionRepository                                │  │
│  │  - getAllActive(): Collection                         │  │
│  │  - getById(id): HomeSection                           │  │
│  │  - create(data): HomeSection                          │  │
│  │  - update(id, data): HomeSection                      │  │
│  │  - delete(id): bool                                   │  │
│  │  - reorder(ids): void                                 │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Data Layer                              │
│  ┌──────────────────────┐    ┌──────────────────────────┐  │
│  │  HomeSection Model   │    │  HomeSectionItem Model   │  │
│  │  - HasUuid           │    │  - Polymorphic Relations │  │
│  │  - SoftDeletes       │    │                          │  │
│  │  - Observer          │    │                          │  │
│  └──────────────────────┘    └──────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Cache Layer (Redis)                       │
│  Tag: 'home_sections'                                        │
│  TTL: 3600 seconds                                           │
└─────────────────────────────────────────────────────────────┘
```

### Request Flow

**Admin Section Update Flow:**
```
Admin UI → Controller → Service → Repository → Model → Observer → Cache Invalidation
```

**Public API Request Flow:**
```
Frontend → API Controller → Service → Repository → Cache Check
                                                   ↓ (miss)
                                          Query DB → Render Sections → Cache Store → Response
```

## Components and Interfaces

### 1. Database Schema

#### home_sections Table

```sql
CREATE TABLE home_sections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL,
    type ENUM('hero', 'featured_products', 'featured_categories', 'banners', 'testimonials', 'html_block') NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT UNSIGNED NOT NULL DEFAULT 0,
    configuration JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_uuid (uuid),
    INDEX idx_display_order (display_order),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at)
);
```

#### home_section_items Table

```sql
CREATE TABLE home_section_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    home_section_id BIGINT UNSIGNED NOT NULL,
    itemable_type VARCHAR(255) NOT NULL,
    itemable_id BIGINT UNSIGNED NOT NULL,
    display_order INT UNSIGNED NOT NULL DEFAULT 0,
    configuration JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (home_section_id) REFERENCES home_sections(id) ON DELETE CASCADE,
    INDEX idx_home_section_display (home_section_id, display_order),
    INDEX idx_itemable (itemable_type, itemable_id)
);
```

### 2. Eloquent Models

#### HomeSection Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HomeSection extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'is_active',
        'display_order',
        'configuration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array',
        'display_order' => 'integer',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(HomeSectionItem::class)->orderBy('display_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
```

#### HomeSectionItem Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSectionItem extends Model
{
    protected $fillable = [
        'home_section_id',
        'itemable_type',
        'itemable_id',
        'display_order',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
        'display_order' => 'integer',
    ];

    // Relationships
    public function homeSection()
    {
        return $this->belongsTo(HomeSection::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
```

#### HomeSectionObserver

```php
namespace App\Observers;

use App\Models\HomeSection;
use Illuminate\Support\Facades\Cache;

class HomeSectionObserver
{
    public function created(HomeSection $homeSection)
    {
        $this->invalidateCache();
    }

    public function updated(HomeSection $homeSection)
    {
        $this->invalidateCache();
    }

    public function deleted(HomeSection $homeSection)
    {
        $this->invalidateCache();
    }

    private function invalidateCache()
    {
        Cache::tags(['home_sections'])->flush();
    }
}
```

### 3. Repository Layer

#### HomeSectionRepositoryInterface

```php
namespace App\Repositories\Contracts;

use App\Models\HomeSection;
use Illuminate\Support\Collection;

interface HomeSectionRepositoryInterface
{
    public function getAllActive(): Collection;
    public function getById(int $id): HomeSection;
    public function create(array $data): HomeSection;
    public function update(int $id, array $data): HomeSection;
    public function delete(int $id): bool;
    public function reorder(array $sectionIds): void;
}
```

#### HomeSectionRepository Implementation

```php
namespace App\Repositories;

use App\Models\HomeSection;
use App\Repositories\Contracts\HomeSectionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeSectionRepository implements HomeSectionRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY = 'home_sections_active';

    public function getAllActive(): Collection
    {
        return Cache::tags(['home_sections'])
            ->remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return HomeSection::active()
                    ->ordered()
                    ->with(['items.itemable'])
                    ->get();
            });
    }

    public function getById(int $id): HomeSection
    {
        return HomeSection::with(['items.itemable'])->findOrFail($id);
    }

    public function create(array $data): HomeSection
    {
        return HomeSection::create($data);
    }

    public function update(int $id, array $data): HomeSection
    {
        $section = $this->getById($id);
        $section->update($data);
        return $section->fresh();
    }

    public function delete(int $id): bool
    {
        $section = $this->getById($id);
        return $section->delete();
    }

    public function reorder(array $sectionIds): void
    {
        foreach ($sectionIds as $index => $sectionId) {
            HomeSection::where('id', $sectionId)
                ->update(['display_order' => $index]);
        }
    }
}
```

### 4. Service Layer

#### HomeConfigurationService

```php
namespace App\Services;

use App\Repositories\Contracts\HomeSectionRepositoryInterface;

class HomeConfigurationService
{
    public function __construct(
        private HomeSectionRepositoryInterface $repository,
        private HomeSectionRendererService $rendererService
    ) {}

    public function getCompleteConfiguration(): array
    {
        $sections = $this->repository->getAllActive();

        return $sections->map(function ($section) {
            return [
                'uuid' => $section->uuid,
                'type' => $section->type,
                'title' => $section->title,
                'display_order' => $section->display_order,
                'configuration' => $section->configuration,
                'rendered_data' => $this->rendererService->render($section),
            ];
        })->toArray();
    }

    public function toggleSectionStatus(int $sectionId, bool $isActive): void
    {
        $this->repository->update($sectionId, ['is_active' => $isActive]);
    }

    public function reorderSections(array $sectionIds): void
    {
        $this->repository->reorder($sectionIds);
    }
}
```

#### HomeSectionRendererService (Strategy Coordinator)

```php
namespace App\Services;

use App\Models\HomeSection;
use App\Services\Renderers\SectionRendererInterface;
use App\Exceptions\InvalidSectionTypeException;

class HomeSectionRendererService
{
    private array $renderers = [];

    public function __construct(
        private HeroRenderer $heroRenderer,
        private FeaturedProductsRenderer $featuredProductsRenderer,
        private FeaturedCategoriesRenderer $featuredCategoriesRenderer,
        private BannersRenderer $bannersRenderer,
        private TestimonialsRenderer $testimonialsRenderer,
        private HtmlBlockRenderer $htmlBlockRenderer
    ) {
        $this->registerRenderers();
    }

    private function registerRenderers(): void
    {
        $this->renderers = [
            'hero' => $this->heroRenderer,
            'featured_products' => $this->featuredProductsRenderer,
            'featured_categories' => $this->featuredCategoriesRenderer,
            'banners' => $this->bannersRenderer,
            'testimonials' => $this->testimonialsRenderer,
            'html_block' => $this->htmlBlockRenderer,
        ];
    }

    public function render(HomeSection $section): array
    {
        if (!isset($this->renderers[$section->type])) {
            throw new InvalidSectionTypeException(
                "No renderer found for section type: {$section->type}"
            );
        }

        return $this->renderers[$section->type]->render($section);
    }
}
```

### 5. Strategy Pattern - Section Renderers

#### SectionRendererInterface

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

interface SectionRendererInterface
{
    public function render(HomeSection $section): array;
}
```

#### HeroRenderer

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

class HeroRenderer implements SectionRendererInterface
{
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'title' => $config['title'] ?? '',
            'subtitle' => $config['subtitle'] ?? '',
            'background_image' => $config['background_image'] ?? null,
            'background_video' => $config['background_video'] ?? null,
            'cta_buttons' => $config['cta_buttons'] ?? [],
            'overlay_opacity' => $config['overlay_opacity'] ?? 0.5,
        ];
    }
}
```

#### FeaturedProductsRenderer

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

class FeaturedProductsRenderer implements SectionRendererInterface
{
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;
        $limit = $config['limit'] ?? 8;

        $products = $section->items()
            ->with(['itemable.images', 'itemable.category'])
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                if (!$item->itemable) {
                    return null;
                }

                $product = $item->itemable;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'image' => $product->images->first()?->url,
                    'category' => $product->category?->name,
                    'rating' => $product->average_rating,
                ];
            })
            ->filter()
            ->values();

        return [
            'layout' => $config['layout'] ?? 'grid',
            'columns' => $config['columns'] ?? 4,
            'show_price' => $config['show_price'] ?? true,
            'show_rating' => $config['show_rating'] ?? true,
            'products' => $products,
        ];
    }
}
```

#### FeaturedCategoriesRenderer

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

class FeaturedCategoriesRenderer implements SectionRendererInterface
{
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;
        $limit = $config['limit'] ?? 6;

        $categories = $section->items()
            ->with(['itemable.image', 'itemable.products'])
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                if (!$item->itemable) {
                    return null;
                }

                $category = $item->itemable;
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->image?->url,
                    'product_count' => $category->products->count(),
                    'description' => $category->description,
                ];
            })
            ->filter()
            ->values();

        return [
            'layout' => $config['layout'] ?? 'grid',
            'columns' => $config['columns'] ?? 3,
            'show_product_count' => $config['show_product_count'] ?? true,
            'categories' => $categories,
        ];
    }
}
```

#### BannersRenderer

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

class BannersRenderer implements SectionRendererInterface
{
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'layout' => $config['layout'] ?? 'slider',
            'autoplay' => $config['autoplay'] ?? true,
            'autoplay_speed' => $config['autoplay_speed'] ?? 5000,
            'banners' => $config['banners'] ?? [],
        ];
    }
}
```

#### TestimonialsRenderer

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

class TestimonialsRenderer implements SectionRendererInterface
{
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'layout' => $config['layout'] ?? 'carousel',
            'show_rating' => $config['show_rating'] ?? true,
            'show_avatar' => $config['show_avatar'] ?? true,
            'testimonials' => $config['testimonials'] ?? [],
        ];
    }
}
```

#### HtmlBlockRenderer

```php
namespace App\Services\Renderers;

use App\Models\HomeSection;

class HtmlBlockRenderer implements SectionRendererInterface
{
    public function render(HomeSection $section): array
    {
        $config = $section->configuration;

        return [
            'html_content' => $config['html_content'] ?? '',
            'css_classes' => $config['css_classes'] ?? '',
        ];
    }
}
```

### 6. Controllers

#### Admin/HomeSectionController

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomeSectionRequest;
use App\Http\Requests\UpdateHomeSectionRequest;
use App\Services\HomeConfigurationService;
use App\Repositories\Contracts\HomeSectionRepositoryInterface;
use Inertia\Inertia;

class HomeSectionController extends Controller
{
    public function __construct(
        private HomeSectionRepositoryInterface $repository,
        private HomeConfigurationService $service
    ) {}

    public function index()
    {
        $sections = HomeSection::withTrashed()
            ->ordered()
            ->get();

        return Inertia::render('Admin/HomeSections/Index', [
            'sections' => $sections,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HomeSections/Create');
    }

    public function store(StoreHomeSectionRequest $request)
    {
        $this->repository->create($request->validated());

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'Section created successfully');
    }

    public function edit(int $id)
    {
        $section = $this->repository->getById($id);

        return Inertia::render('Admin/HomeSections/Edit', [
            'section' => $section,
        ]);
    }

    public function update(UpdateHomeSectionRequest $request, int $id)
    {
        $this->repository->update($id, $request->validated());

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'Section updated successfully');
    }

    public function destroy(int $id)
    {
        $this->repository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully',
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'section_ids' => 'required|array',
            'section_ids.*' => 'required|integer|exists:home_sections,id',
        ]);

        $this->service->reorderSections($request->section_ids);

        return response()->json([
            'success' => true,
            'message' => 'Sections reordered successfully',
        ]);
    }

    public function toggleStatus(int $id)
    {
        $section = $this->repository->getById($id);
        $newStatus = !$section->is_active;

        $this->service->toggleSectionStatus($id, $newStatus);

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
        ]);
    }
}
```

#### Api/HomeConfigurationController

```php
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

    public function index()
    {
        $configuration = Cache::tags(['home_sections'])
            ->remember('api_home_configuration', 3600, function () {
                return $this->service->getCompleteConfiguration();
            });

        return HomeSectionResource::collection($configuration);
    }
}
```

### 7. Form Requests

#### StoreHomeSectionRequest

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:hero,featured_products,featured_categories,banners,testimonials,html_block',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
            'configuration' => 'required|array',
        ];
    }
}
```

#### UpdateHomeSectionRequest

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:hero,featured_products,featured_categories,banners,testimonials,html_block',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
            'configuration' => 'required|array',
        ];
    }
}
```

### 8. API Resources

#### HomeSectionResource

```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid' => $this->resource['uuid'],
            'type' => $this->resource['type'],
            'title' => $this->resource['title'],
            'display_order' => $this->resource['display_order'],
            'configuration' => $this->resource['configuration'],
            'rendered_data' => $this->resource['rendered_data'],
        ];
    }
}
```

## Data Models

### HomeSection Entity

```
HomeSection {
    id: integer (primary key)
    uuid: string (unique, public identifier)
    type: enum (hero, featured_products, featured_categories, banners, testimonials, html_block)
    title: string (max 255)
    is_active: boolean
    display_order: integer (unsigned)
    configuration: json
    created_at: timestamp
    updated_at: timestamp
    deleted_at: timestamp (nullable)
}
```

### HomeSectionItem Entity

```
HomeSectionItem {
    id: integer (primary key)
    home_section_id: integer (foreign key)
    itemable_type: string (polymorphic type)
    itemable_id: integer (polymorphic id)
    display_order: integer (unsigned)
    configuration: json (nullable)
    created_at: timestamp
    updated_at: timestamp
}
```

### Configuration JSON Schemas

#### Hero Section Configuration
```json
{
    "title": "Welcome to Our Store",
    "subtitle": "Discover amazing products",
    "background_image": "/images/hero-bg.jpg",
    "background_video": null,
    "overlay_opacity": 0.5,
    "cta_buttons": [
        {
            "text": "Shop Now",
            "url": "/shop",
            "style": "primary"
        }
    ]
}
```

#### Featured Products Configuration
```json
{
    "layout": "grid",
    "columns": 4,
    "limit": 8,
    "show_price": true,
    "show_rating": true
}
```

#### Featured Categories Configuration
```json
{
    "layout": "grid",
    "columns": 3,
    "limit": 6,
    "show_product_count": true
}
```

#### Banners Configuration
```json
{
    "layout": "slider",
    "autoplay": true,
    "autoplay_speed": 5000,
    "banners": [
        {
            "image": "/images/banner1.jpg",
            "title": "Summer Sale",
            "subtitle": "Up to 50% off",
            "link": "/sale",
            "button_text": "Shop Sale"
        }
    ]
}
```

#### Testimonials Configuration
```json
{
    "layout": "carousel",
    "show_rating": true,
    "show_avatar": true,
    "testimonials": [
        {
            "name": "John Doe",
            "avatar": "/images/avatar1.jpg",
            "rating": 5,
            "text": "Great products and service!",
            "date": "2024-01-15"
        }
    ]
}
```

#### HTML Block Configuration
```json
{
    "html_content": "<div class='custom-block'>...</div>",
    "css_classes": "my-custom-section"
}
```

