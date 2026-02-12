<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetDefaultAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\CustomerAddressService;
use Illuminate\Http\JsonResponse;

/**
 * CustomerAddressController handles customer address management operations.
 * 
 * Provides endpoints for customers to manage their addresses including
 * listing, creating, updating, deleting, and setting default addresses.
 * All operations validate address ownership to ensure security.
 */
class CustomerAddressController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param CustomerAddressService $customerAddressService Service for address business logic
     */
    public function __construct(
        private CustomerAddressService $customerAddressService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the authenticated user's addresses.
     * 
     * Retrieves all non-deleted addresses for the authenticated user's customer profile.
     * Returns an empty array if the user has no customer profile or no addresses.
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        
        // Check if user has a customer profile
        if (!$user->customer) {
            return response()->json([]);
        }
        
        $addresses = $this->customerAddressService->getAddresses($user->customer);

        return response()->json(AddressResource::collection($addresses)->resolve());
    }

    /**
     * Store a newly created address.
     * 
     * Creates a new address associated with the authenticated user's customer profile.
     * Returns the created address with HTTP 201 status.
     * 
     * @param StoreAddressRequest $request Validated address creation request
     * @return JsonResponse
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check if user has a customer profile
            if (!$user->customer) {
                return response()->json([
                    'message' => 'Usuario no tiene perfil de cliente.'
                ], 403);
            }
            
            $address = $this->customerAddressService->createAddress(
                $user->customer,
                $request->validated()
            );
            
            return response()->json(new AddressResource($address), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la dirección.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified address.
     * 
     * Updates an existing address if it belongs to the authenticated user.
     * Returns HTTP 403 if address doesn't belong to user.
     * Returns HTTP 404 if address doesn't exist.
     * 
     * @param UpdateAddressRequest $request Validated address update request
     * @param string $uuid Address UUID
     * @return JsonResponse
     */
    public function update(UpdateAddressRequest $request, string $uuid): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check if user has a customer profile
            if (!$user->customer) {
                return response()->json([
                    'message' => 'Usuario no tiene perfil de cliente.'
                ], 403);
            }
            
            // Find address by UUID
            $address = Address::where('uuid', $uuid)->first();
            
            if (!$address) {
                return response()->json([
                    'message' => 'Dirección no encontrada.'
                ], 404);
            }
            
            // Verify ownership
            if ($address->customer_id !== $user->customer->id) {
                return response()->json([
                    'message' => 'No tienes permiso para actualizar esta dirección.'
                ], 403);
            }
            
            $updatedAddress = $this->customerAddressService->updateAddress(
                $address,
                $request->validated()
            );
            
            return response()->json(new AddressResource($updatedAddress), 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la dirección.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified address.
     * 
     * Soft deletes an address if it belongs to the authenticated user.
     * Automatically cleans up default address references if needed.
     * Returns HTTP 403 if address doesn't belong to user.
     * Returns HTTP 404 if address doesn't exist.
     * 
     * @param string $uuid Address UUID
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check if user has a customer profile
            if (!$user->customer) {
                return response()->json([
                    'message' => 'Usuario no tiene perfil de cliente.'
                ], 403);
            }
            
            // Find address by UUID
            $address = Address::where('uuid', $uuid)->first();
            
            if (!$address) {
                return response()->json([
                    'message' => 'Dirección no encontrada.'
                ], 404);
            }
            
            // Verify ownership
            if ($address->customer_id !== $user->customer->id) {
                return response()->json([
                    'message' => 'No tienes permiso para eliminar esta dirección.'
                ], 403);
            }
            
            $this->customerAddressService->deleteAddress($address);
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la dirección.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set an address as default for shipping or billing.
     * 
     * Configures the specified address as the default for either shipping or billing.
     * Returns HTTP 403 if address doesn't belong to user.
     * Returns HTTP 404 if address doesn't exist.
     * 
     * @param SetDefaultAddressRequest $request Validated default address request
     * @return JsonResponse
     */
    public function setDefaultAddress(SetDefaultAddressRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check if user has a customer profile
            if (!$user->customer) {
                return response()->json([
                    'message' => 'Usuario no tiene perfil de cliente.'
                ], 403);
            }
            
            // Find address by UUID
            $address = Address::where('uuid', $request->input('address_id'))->first();
            
            if (!$address) {
                return response()->json([
                    'message' => 'Dirección no encontrada.'
                ], 404);
            }
            
            // Verify ownership
            if ($address->customer_id !== $user->customer->id) {
                return response()->json([
                    'message' => 'No tienes permiso para configurar esta dirección.'
                ], 403);
            }
            
            $this->customerAddressService->setDefaultAddress(
                $user->customer,
                $address,
                $request->input('type')
            );
            
            $type = $request->input('type') === 'shipping' ? 'envío' : 'facturación';
            
            return response()->json([
                'message' => "Dirección configurada como predeterminada para {$type}."
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al configurar la dirección predeterminada.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
