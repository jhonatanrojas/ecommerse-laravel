<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('vendor.auth.login');
    }

    public function showRegisterForm(): View
    {
        return view('vendor.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'business_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'payout_cycle' => ['nullable', 'in:weekly,monthly,manual'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $settings = StoreSetting::query()->first();
            $autoApprove = (bool) ($settings?->auto_approve_vendors ?? false);

            Vendor::query()->create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'document' => $validated['document'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'status' => $autoApprove ? 'approved' : 'pending',
                'payout_cycle' => $validated['payout_cycle'] ?? 'manual',
                'approved_at' => $autoApprove ? now() : null,
            ]);
        });

        return redirect()
            ->route('vendor.login')
            ->with('success', 'Solicitud de vendedor enviada. Te notificaremos cuando sea aprobada.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('vendor')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::guard('vendor')->user();
        $vendor = $user?->vendor;

        if (! $vendor) {
            Auth::guard('vendor')->logout();
            throw ValidationException::withMessages([
                'email' => 'Tu usuario no tiene perfil de vendedor.',
            ]);
        }

        if ($vendor->status !== 'approved') {
            Auth::guard('vendor')->logout();

            return redirect()->route('vendor.pending');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('vendor.dashboard'));
    }

    public function pending(): View
    {
        return view('vendor.auth.pending');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login');
    }
}
