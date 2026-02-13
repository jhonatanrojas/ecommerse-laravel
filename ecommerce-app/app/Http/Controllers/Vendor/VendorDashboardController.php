<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    public function __invoke(): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $stats = [
            'products_total' => $vendor->products()->count(),
            'products_approved' => $vendor->products()->where('is_approved', true)->count(),
            'orders_total' => $vendor->orders()->count(),
            'pending_payout' => (float) $vendor->orders()->where('payout_status', 'pending')->sum('vendor_earnings'),
            'paid_out' => (float) $vendor->orders()->where('payout_status', 'paid')->sum('vendor_earnings'),
        ];

        return view('vendor.dashboard', compact('vendor', 'stats'));
    }
}
