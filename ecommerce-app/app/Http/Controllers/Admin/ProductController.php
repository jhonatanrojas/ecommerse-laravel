<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $perPage = $request->get('per_page', 15);

        $products = $this->productService->getPaginatedProducts($perPage, $search, $categoryId);
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $images = $request->file('images');

            $this->productService->createProduct($data, $images);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            abort(404, 'Producto no encontrado');
        }

        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            $images = $request->file('images');

            $result = $this->productService->updateProduct($id, $data, $images);

            if (!$result) {
                return back()
                    ->withInput()
                    ->with('error', 'Producto no encontrado.');
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $result = $this->productService->deleteProduct($id);

            if (!$result) {
                return back()->with('error', 'Producto no encontrado.');
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        try {
            $result = $this->productService->toggleStatus($id);

            if (!$result) {
                return back()->with('error', 'Producto no encontrado.');
            }

            return back()->with('success', 'Estado actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function toggleFeatured(int $id): RedirectResponse
    {
        try {
            $result = $this->productService->toggleFeatured($id);

            if (!$result) {
                return back()->with('error', 'Producto no encontrado.');
            }

            return back()->with('success', 'Destacado actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar destacado: ' . $e->getMessage());
        }
    }

    public function deleteImage(int $productId, int $imageId): RedirectResponse
    {
        try {
            $product = $this->productService->getProductById($productId);

            if (!$product) {
                return back()->with('error', 'Producto no encontrado.');
            }

            $image = $product->images()->find($imageId);

            if (!$image) {
                return back()->with('error', 'Imagen no encontrada.');
            }

            // Eliminar archivo físico
            $this->productService->deleteImage($image->image_path);
            if ($image->thumbnail_path) {
                $this->productService->deleteImage($image->thumbnail_path);
            }

            // Eliminar registro
            $image->delete();

            return back()->with('success', 'Imagen eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la imagen: ' . $e->getMessage());
        }
    }
}
