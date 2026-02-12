<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

/**
 * UserController handles user profile operations for authenticated users.
 * 
 * This controller provides endpoints for retrieving user profile information
 * and updating user passwords through the API.
 */
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param UserService $userService Service for user business logic
     */
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Display the authenticated user's profile information.
     * 
     * Retrieves the authenticated user with their customer relationship
     * and returns the data formatted through UserResource.
     * 
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = auth()->user()->load('customer');
        
        return response()->json(new UserResource($user));
    }

    /**
     * Update the authenticated user's password.
     * 
     * Validates the current password and updates to the new password
     * if validation passes. Returns appropriate error responses for
     * invalid current password or other failures.
     * 
     * @param UpdatePasswordRequest $request Validated password update request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            $this->userService->updatePassword(
                $user,
                $request->input('current_password'),
                $request->input('password')
            );

            return response()->json([
                'message' => 'Contraseña actualizada exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}