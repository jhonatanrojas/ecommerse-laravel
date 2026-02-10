<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
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

    public function getProductById(int $id): ?Product
    {
        return $this->repository->findById($id);
    }

    public function getProductByUuid(string $uuid): ?Product
    {
        return $this->repository->findByUuid($uuid);
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
}
