<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('vendor')->user();
        $vendor = $user?->vendor;

        if (! $vendor || $vendor->status !== 'approved') {
            Auth::guard('vendor')->logout();

            return redirect()
                ->route('vendor.login')
                ->with('error', 'Tu cuenta de vendedor no está aprobada para operar.');
        }

        return $next($request);
    }
}
