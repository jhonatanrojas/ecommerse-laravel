<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorPayout;
use App\Services\Marketplace\VendorPayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorPayoutController extends Controller
{
    public function __construct(
        protected VendorPayoutService $payoutService
    ) {}

    public function index(Request $request): View
    {
        $payouts = VendorPayout::query()
            ->with('vendor')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->latest()
            ->paginate(20);

        return view('admin.vendor-payouts.index', compact('payouts'));
    }

    public function process(VendorPayout $vendorPayout): RedirectResponse
    {
        $this->payoutService->processPayout($vendorPayout);

        return back()->with('success', 'Liquidación procesada.');
    }
}
