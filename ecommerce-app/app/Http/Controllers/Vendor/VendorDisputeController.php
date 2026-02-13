<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorDisputeController extends Controller
{
    public function index(): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        $disputes = $vendor->disputes()->latest()->paginate(15);
        $vendorOrders = VendorOrder::query()
            ->with('order')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->limit(50)
            ->get();

        return view('vendor.disputes.index', compact('disputes', 'vendorOrders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $validated = $request->validate([
            'order_id' => ['nullable', 'exists:orders,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $vendor->disputes()->create([
            'order_id' => $validated['order_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Disputa creada y enviada a revisión.');
    }
}
