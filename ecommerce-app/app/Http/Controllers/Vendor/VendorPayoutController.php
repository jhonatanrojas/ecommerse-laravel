<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\VendorPayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorPayoutController extends Controller
{
    public function __construct(
        protected VendorPayoutService $payoutService
    ) {}

    public function index(): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $payouts = $vendor->payouts()->latest()->paginate(15);
        $pendingEarnings = (float) $vendor->orders()
            ->where('payout_status', 'pending')
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->sum('vendor_earnings');

        return view('vendor.payouts.index', compact('payouts', 'pendingEarnings'));
    }

    public function request(Request $request): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        $amount = $request->filled('amount') ? (float) $request->input('amount') : null;

        $payout = $this->payoutService->createPendingPayout($vendor, $amount);

        if (! $payout) {
            return back()->with('error', 'No tienes saldo disponible para liquidar.');
        }

        return back()->with('success', 'Solicitud de liquidación creada.');
    }
}
