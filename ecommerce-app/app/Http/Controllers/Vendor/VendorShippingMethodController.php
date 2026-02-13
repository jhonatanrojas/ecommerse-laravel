<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorShippingMethodController extends Controller
{
    public function index(): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        $methods = $vendor->shippingMethods()->latest()->get();

        return view('vendor.shipping-methods.index', compact('methods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'extra_rate' => ['nullable', 'numeric', 'min:0'],
            'rules' => ['nullable', 'array'],
        ]);

        $vendor->shippingMethods()->create([
            ...$validated,
            'extra_rate' => $validated['extra_rate'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Método de envío creado.');
    }

    public function update(Request $request, VendorShippingMethod $method): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        abort_unless($method->vendor_id === $vendor->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'extra_rate' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'rules' => ['nullable', 'array'],
        ]);

        $method->update([
            ...$validated,
            'extra_rate' => $validated['extra_rate'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'Método de envío actualizado.');
    }

    public function destroy(VendorShippingMethod $method): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        abort_unless($method->vendor_id === $vendor->id, 403);

        $method->delete();

        return back()->with('success', 'Método de envío eliminado.');
    }
}
