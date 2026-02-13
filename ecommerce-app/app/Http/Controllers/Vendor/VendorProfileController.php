<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorProfileController extends Controller
{
    public function edit(): View
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        return view('vendor.profile.edit', compact('vendor'));
    }

    public function update(Request $request): RedirectResponse
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'payout_cycle' => ['required', 'in:weekly,monthly,manual'],
            'payout_method' => ['nullable', 'array'],
            'payout_method.provider' => ['nullable', 'string', 'max:50'],
            'payout_method.account' => ['nullable', 'string', 'max:255'],
            'payout_method.beneficiary' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor->update($validated);

        return back()->with('success', 'Perfil de vendedor actualizado.');
    }
}
