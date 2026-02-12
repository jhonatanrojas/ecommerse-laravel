<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * UserControllerTest verifies user profile and password update functionality.
 * 
 * Tests the API endpoints for retrieving user profile information
 * and updating user passwords.
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that authenticated user can retrieve their profile.
     */
    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'customer',
            ]);
    }

    /**
     * Test that authenticated user with customer can retrieve profile with customer data.
     */
    public function test_authenticated_user_with_customer_can_get_profile_with_customer_data(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'customer' => [
                    'id',
                    'phone',
                    'document',
                    'birthdate',
                ],
            ]);
    }

    /**
     * Test that unauthenticated user cannot access profile.
     */
    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /**
     * Test that authenticated user can update password with valid current password.
     */
    public function test_authenticated_user_can_update_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Contraseña actualizada exitosamente.',
            ]);
    }

    /**
     * Test that password update fails with incorrect current password.
     */
    public function test_password_update_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    /**
     * Test that password update requires current password.
     */
    public function test_password_update_requires_current_password(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    /**
     * Test that password update requires password confirmation.
     */
    public function test_password_update_requires_password_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test that password must be at least 8 characters.
     */
    public function test_password_must_be_at_least_8_characters(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/password', [
            'current_password' => 'old-password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test that unauthenticated user cannot update password.
     */
    public function test_unauthenticated_user_cannot_update_password(): void
    {
        $response = $this->putJson('/api/user/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(401);
    }
}
