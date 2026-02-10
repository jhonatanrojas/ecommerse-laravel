<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user and return token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json($result, 201);
    }

    /**
     * Login and return token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->only('email', 'password'));

        return response()->json($result);
    }

    /**
     * Logout (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
