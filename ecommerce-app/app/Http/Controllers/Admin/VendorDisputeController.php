<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorDispute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorDisputeController extends Controller
{
    public function index(Request $request): View
    {
        $disputes = VendorDispute::query()
            ->with(['vendor', 'order'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->latest()
            ->paginate(20);

        return view('admin.vendor-disputes.index', compact('disputes'));
    }

    public function update(Request $request, VendorDispute $vendorDispute): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_review,resolved,rejected'],
            'admin_response' => ['nullable', 'string'],
        ]);

        $vendorDispute->update($validated);

        return back()->with('success', 'Disputa actualizada.');
    }
}
