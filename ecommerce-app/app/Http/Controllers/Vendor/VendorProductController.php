<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Models\VendorProduct;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {}

    public function index(Request $request): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $query = VendorProduct::query()
            ->with('product.category')
            ->where('vendor_id', $vendor->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('is_approved', $request->string('status')->value() === 'approved');
        }

        $vendorProducts = $query->paginate(15);

        return view('vendor.products.index', compact('vendorProducts'));
    }

    public function create(): View
    {
        $categories = Category::query()->active()->ordered()->get();

        return view('vendor.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        $validated = $request->validated();
        $variants = $request->input('variants', []);

        DB::transaction(function () use ($vendor, $validated, $request, $variants) {
            $product = $this->productService->createProduct($validated, $request->file('images'));

            $settings = StoreSetting::query()->first();
            $autoApproveProduct = (bool) ($settings?->auto_approve_vendor_products ?? false);

            VendorProduct::query()->create([
                'vendor_id' => $vendor->id,
                'product_id' => $product->id,
                'is_active' => true,
                'is_approved' => $autoApproveProduct,
                'approved_at' => $autoApproveProduct ? now() : null,
                'moderation_notes' => $autoApproveProduct ? 'Aprobación automática por configuración.' : null,
            ]);

            foreach ($variants as $variant) {
                if (empty($variant['name'])) {
                    continue;
                }

                ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'name' => $variant['name'],
                    'sku' => $variant['sku'] ?? null,
                    'price' => $variant['price'] ?? null,
                    'stock' => (int) ($variant['stock'] ?? 0),
                    'attributes' => $variant['attributes'] ?? null,
                ]);
            }
        });

        return redirect()->route('vendor.products.index')->with('success', 'Producto enviado a moderación.');
    }

    public function edit(Product $product): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $vendorProduct = VendorProduct::query()
            ->where('vendor_id', $vendor->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $product->load(['images', 'variants']);
        $categories = Category::query()->active()->ordered()->get();

        return view('vendor.products.edit', compact('product', 'categories', 'vendorProduct'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $vendorProduct = VendorProduct::query()
            ->where('vendor_id', $vendor->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        DB::transaction(function () use ($request, $product, $vendorProduct) {
            $this->productService->updateProduct($product->id, $request->validated(), $request->file('images'));

            $vendorProduct->update([
                'is_approved' => false,
                'approved_at' => null,
                'moderation_notes' => 'Producto actualizado por vendedor. Requiere re-aprobación.',
            ]);
        });

        return redirect()->route('vendor.products.index')->with('success', 'Producto actualizado y enviado a re-moderación.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $vendorProduct = VendorProduct::query()
            ->where('vendor_id', $vendor->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $vendorProduct->update([
            'is_active' => ! $vendorProduct->is_active,
        ]);

        return back()->with('success', 'Estado actualizado.');
    }
}
