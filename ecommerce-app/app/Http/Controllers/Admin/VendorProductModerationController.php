<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorProductModerationController extends Controller
{
    public function index(Request $request): View
    {
        $products = VendorProduct::query()
            ->with(['vendor', 'product.category'])
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->string('status')->value() === 'approved') {
                    $query->where('is_approved', true);
                }

                if ($request->string('status')->value() === 'pending') {
                    $query->where('is_approved', false);
                }
            })
            ->latest()
            ->paginate(20);

        return view('admin.vendor-products.index', compact('products'));
    }

    public function moderate(Request $request, VendorProduct $vendorProduct): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'moderation_notes' => ['nullable', 'string'],
        ]);

        $approved = $validated['action'] === 'approve';

        $history = $vendorProduct->moderation_history ?? [];
        $history[] = [
            'action' => $validated['action'],
            'notes' => $validated['moderation_notes'] ?? null,
            'at' => now()->toIso8601String(),
            'by' => auth('admin')->user()?->email,
        ];

        $vendorProduct->update([
            'is_approved' => $approved,
            'is_active' => $approved,
            'approved_at' => $approved ? now() : null,
            'moderation_notes' => $validated['moderation_notes'] ?? null,
            'moderation_history' => $history,
        ]);

        return back()->with('success', 'Moderación aplicada.');
    }
}
