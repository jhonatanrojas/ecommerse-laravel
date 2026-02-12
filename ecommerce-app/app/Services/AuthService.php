<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Registra un usuario y devuelve el token. No conoce HTTP.
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Crear perfil de cliente automáticamente
        $user->customer()->create([
            'phone' => $data['phone'] ?? null,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Login y devuelve token. Lanza ValidationException si credenciales inválidas.
     */
    public function login(array $credentials): array
    {
        if (! Auth::guard('customer')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son correctas.'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Revoca el token actual del usuario.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
