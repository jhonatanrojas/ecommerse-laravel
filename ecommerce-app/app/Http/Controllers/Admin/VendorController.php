<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $vendors = Vendor::query()
            ->with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->latest()
            ->paginate(20);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function show(Vendor $vendor): View
    {
        $vendor->load(['user', 'products.product', 'orders.order', 'payouts']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function updateStatus(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,suspended'],
            'rejection_reason' => ['nullable', 'string'],
        ]);

        $vendor->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
            'rejection_reason' => $validated['status'] === 'rejected' ? ($validated['rejection_reason'] ?? null) : null,
        ]);

        return back()->with('success', 'Estado de vendedor actualizado.');
    }
}
