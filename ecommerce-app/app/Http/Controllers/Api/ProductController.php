<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductServiceInterface $productService
    ) {}

    public function index(ProductFilterRequest $request): ProductCollection
    {
        $products = $this->productService->getProducts($request->validated());

        return new ProductCollection($products);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $product = $this->productService->getProductBySlug($slug);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }
    }

    public function related(Request $request, string $slug): JsonResponse
    {
        try {
            $product = $this->productService->getProductBySlug($slug);
            $limit = max(1, min((int) $request->query('limit', 8), 24));

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection(
                    $this->productService->getRelatedProducts($product, $limit)
                ),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }
    }
}

