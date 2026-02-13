<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    protected const DEFAULT_PRODUCTS_PER_PAGE = 12;

    protected const CACHE_TTL_SECONDS = 300;

    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedProducts(int $perPage = 15, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search, $categoryId);
    }

    public function getProducts(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->select([
                'id',
                'category_id',
                'name',
                'slug',
                'description',
                'price',
                'compare_price',
                'stock',
                'is_active',
                'created_at',
            ])
            ->with([
                'category:id,name,slug',
                'images:id,product_id,image_path,thumbnail_path,alt_text,is_primary,order',
                'variants:id,product_id,name,sku,price,stock,attributes',
            ]);

        $this->applyMarketplaceVisibility($query);
        $this->applyCatalogFilters($query, $filters);
        $this->applyCatalogSorting($query, $filters['sort'] ?? null);

        $perPage = (int) ($filters['per_page'] ?? self::DEFAULT_PRODUCTS_PER_PAGE);
        $perPage = max(1, min($perPage, 60));
        $page = isset($filters['page']) ? (int) $filters['page'] : null;

        return $this->remember(
            $this->buildProductsCacheKey($filters, $perPage),
            fn () => $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    public function getProductById(int $id): ?Product
    {
        return $this->repository->findById($id);
    }

    public function getProductByUuid(string $uuid): ?Product
    {
        return $this->repository->findByUuid($uuid);
    }

    public function getProductBySlug(string $slug): Product
    {
        return $this->remember("catalog:product:{$slug}", function () use ($slug) {
            $product = Product::query()
                ->select([
                    'id',
                    'category_id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'compare_price',
                    'stock',
                    'is_active',
                    'created_at',
                ])
                ->with([
                    'category:id,name,slug',
                    'images:id,product_id,image_path,thumbnail_path,alt_text,is_primary,order',
                    'variants:id,product_id,name,sku,price,stock,attributes',
                ])
                ->where('slug', $slug)
                ->where('is_active', true)
                ->where(function (Builder $query) {
                    $query->whereDoesntHave('vendorProducts')
                        ->orWhereHas('vendorProducts', function (Builder $q) {
                            $q->where('is_active', true)
                                ->where('is_approved', true)
                                ->whereHas('vendor', fn (Builder $vendorQuery) => $vendorQuery->where('status', 'approved'));
                        });
                })
                ->first();

            if (! $product) {
                throw (new ModelNotFoundException())->setModel(Product::class, [$slug]);
            }

            return $product;
        });
    }

    public function getRelatedProducts(Product $product, int $limit = 8): Collection
    {
        $limit = max(1, min($limit, 24));

        return $this->remember("catalog:related:{$product->id}:{$limit}", function () use ($product, $limit) {
            return Product::query()
                ->select([
                    'id',
                    'category_id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'compare_price',
                    'stock',
                    'is_active',
                    'created_at',
                ])
                ->with([
                    'category:id,name,slug',
                    'images:id,product_id,image_path,thumbnail_path,alt_text,is_primary,order',
                    'variants:id,product_id,name,sku,price,stock,attributes',
                ])
                ->where('is_active', true)
                ->where(function (Builder $query) {
                    $query->whereDoesntHave('vendorProducts')
                        ->orWhereHas('vendorProducts', function (Builder $q) {
                            $q->where('is_active', true)
                                ->where('is_approved', true)
                                ->whereHas('vendor', fn (Builder $vendorQuery) => $vendorQuery->where('status', 'approved'));
                        });
                })
                ->where('id', '!=', $product->id)
                ->when(
                    $product->category_id,
                    fn (Builder $query) => $query->where('category_id', $product->category_id),
                    fn (Builder $query) => $query->whereNull('category_id')
                )
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    public function createProduct(array $data, ?array $images = null): Product
    {
        try {
            DB::beginTransaction();

            // Generar slug si no existe
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Agregar auditoría
            if (Auth::check()) {
                $data['created_by'] = Auth::user()->uuid;
            }

            $product = $this->repository->create($data);

            // Procesar imágenes si existen
            if ($images && count($images) > 0) {
                $this->processImages($product, $images);
            }

            Log::info('Producto creado', [
                'product_id' => $product->id,
                'name' => $product->name,
                'created_by' => $data['created_by'] ?? null
            ]);

            DB::commit();

            return $product->load(['category', 'images']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear producto', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateProduct(int $id, array $data, ?array $images = null): bool
    {
        try {
            DB::beginTransaction();

            // Agregar auditoría
            if (Auth::check()) {
                $data['updated_by'] = Auth::user()->uuid;
            }

            $result = $this->repository->update($id, $data);

            if ($result) {
                $product = $this->getProductById($id);

                // Procesar nuevas imágenes si existen
                if ($images && count($images) > 0) {
                    $this->processImages($product, $images);
                }

                Log::info('Producto actualizado', [
                    'product_id' => $id,
                    'updated_by' => $data['updated_by'] ?? null
                ]);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar producto', ['product_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteProduct(int $id): bool
    {
        try {
            DB::beginTransaction();

            $product = $this->getProductById($id);

            if (!$product) {
                return false;
            }

            // Eliminar imágenes físicas
            foreach ($product->images as $image) {
                $this->deleteImage($image->image_path);
                if ($image->thumbnail_path) {
                    $this->deleteImage($image->thumbnail_path);
                }
            }

            // Agregar auditoría antes de eliminar
            if (Auth::check()) {
                $this->repository->update($id, ['deleted_by' => Auth::user()->uuid]);
            }

            $result = $this->repository->delete($id);

            if ($result) {
                Log::info('Producto eliminado', ['product_id' => $id]);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar producto', ['product_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getActiveProducts(): Collection
    {
        return $this->repository->getActive();
    }

    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->repository->getFeatured($limit);
    }

    public function getLowStockProducts(): Collection
    {
        return $this->repository->getLowStock();
    }

    public function toggleStatus(int $id): bool
    {
        $product = $this->getProductById($id);

        if (!$product) {
            return false;
        }

        return $this->updateProduct($id, ['is_active' => !$product->is_active]);
    }

    public function toggleFeatured(int $id): bool
    {
        $product = $this->getProductById($id);

        if (!$product) {
            return false;
        }

        return $this->updateProduct($id, ['is_featured' => !$product->is_featured]);
    }

    public function uploadImage(UploadedFile $file, string $directory = 'products'): string
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    public function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    protected function processImages(Product $product, array $images): void
    {
        foreach ($images as $index => $imageFile) {
            if ($imageFile instanceof UploadedFile) {
                $imagePath = $this->uploadImage($imageFile);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => $index === 0 && $product->images()->count() === 0,
                    'order' => $product->images()->count() + $index,
                ]);
            }
        }
    }

    protected function applyCatalogFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            }

            if ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        } else {
            $query->where('is_active', true);
        }
    }

    protected function applyCatalogSorting(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };
    }

    protected function applyMarketplaceVisibility(Builder $query): void
    {
        $query->where(function (Builder $builder) {
            $builder->whereDoesntHave('vendorProducts')
                ->orWhereHas('vendorProducts', function (Builder $q) {
                    $q->where('is_active', true)
                        ->where('is_approved', true)
                        ->whereHas('vendor', fn (Builder $vendorQuery) => $vendorQuery->where('status', 'approved'));
                });
        });
    }

    protected function buildProductsCacheKey(array $filters, int $perPage): string
    {
        ksort($filters);

        return 'catalog:products:' . md5(json_encode([
            'filters' => $filters,
            'per_page' => $perPage,
        ]));
    }

    protected function remember(string $key, callable $callback): mixed
    {
        if (! $this->isCatalogCacheEnabled()) {
            return $callback();
        }

        return Cache::remember($key, self::CACHE_TTL_SECONDS, $callback);
    }

    protected function isCatalogCacheEnabled(): bool
    {
        return (bool) env('PRODUCTS_API_CACHE', false);
    }
}
