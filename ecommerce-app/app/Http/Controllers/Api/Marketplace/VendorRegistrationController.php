<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'business_name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
        ]);

        $vendor = DB::transaction(function () use ($validated) {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            $settings = StoreSetting::query()->first();
            $autoApprove = (bool) ($settings?->auto_approve_vendors ?? false);

            return Vendor::query()->create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'document' => $validated['document'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'status' => $autoApprove ? 'approved' : 'pending',
                'approved_at' => $autoApprove ? now() : null,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Registro de vendedor recibido.',
            'data' => [
                'uuid' => $vendor->uuid,
                'status' => $vendor->status,
                'business_name' => $vendor->business_name,
            ],
        ], 201);
    }
}
