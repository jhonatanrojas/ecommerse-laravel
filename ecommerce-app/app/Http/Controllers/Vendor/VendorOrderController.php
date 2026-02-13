<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorOrderController extends Controller
{
    public function index(Request $request): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $orders = VendorOrder::query()
            ->with(['order.user'])
            ->where('vendor_id', $vendor->id)
            ->when($request->filled('shipping_status'), fn ($q) => $q->where('shipping_status', $request->string('shipping_status')->value()))
            ->latest()
            ->paginate(15);

        return view('vendor.orders.index', compact('orders'));
    }

    public function show(VendorOrder $vendorOrder): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        abort_unless($vendorOrder->vendor_id === $vendor->id, 403);

        $vendorOrder->load(['order.items.product', 'order.shippingAddress', 'order.billingAddress']);

        return view('vendor.orders.show', compact('vendorOrder'));
    }

    public function updateShipping(Request $request, VendorOrder $vendorOrder): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        abort_unless($vendorOrder->vendor_id === $vendor->id, 403);

        $validated = $request->validate([
            'shipping_status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
        ]);

        $vendorOrder->update([
            'shipping_status' => $validated['shipping_status'],
            'tracking_number' => $validated['tracking_number'] ?? $vendorOrder->tracking_number,
            'shipping_method' => $validated['shipping_method'] ?? $vendorOrder->shipping_method,
            'shipped_at' => $validated['shipping_status'] === 'shipped' ? now() : $vendorOrder->shipped_at,
            'delivered_at' => $validated['shipping_status'] === 'delivered' ? now() : $vendorOrder->delivered_at,
        ]);

        return back()->with('success', 'Estado de envío actualizado.');
    }
}
