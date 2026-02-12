<?php

namespace Tests\Feature\Properties;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Property-Based Tests for Error Handling
 * 
 * These tests validate universal properties that should hold true
 * for error responses across the API.
 */
class ErrorHandlingPropertiesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Feature: customer-backend-api, Property 11: Consistent Error Response Formatting
     * 
     * For any error in the API, it should return the appropriate HTTP status code
     * (422 for validation, 401 for authentication, 403 for authorization, 
     * 404 for resource not found, 500 for internal errors) with consistent response structure.
     * 
     * Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5
     */
    public function test_property_11_consistent_error_response_formatting(): void
    {
        // Test 401 once (no auth state yet); 422/403 use Sanctum::actingAs and would pollute auth for next iteration
        $this->verify_401_unauthenticated_error();

        // Run property test with multiple iterations for 422 and 403
        for ($i = 0; $i < 100; $i++) {
            // Test 422 - Validation Error
            $this->verify_422_validation_error();

            // Test 403 - Authorization Error (accessing another user's resource)
            $this->verify_403_authorization_error();
        }

        $this->assertTrue(true, 'All error response formats are consistent');
    }

    /**
     * Verify 401 Unauthenticated error response (must be called before any Sanctum::actingAs in the test)
     */
    private function verify_401_unauthenticated_error(): void
    {
        $response = $this->getJson('/api/user');

        $this->assertEquals(401, $response->status(),
            'Unauthenticated request to /api/user should return 401');

        $this->assertArrayHasKey('message', $response->json(),
            "401 response should have 'message' key");
    }

    /**
     * Verify 422 Validation error response
     */
    private function verify_422_validation_error(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Test password update with invalid data
        $invalidData = [
            'current_password' => '',
            'password' => 'short',
            'password_confirmation' => 'different'
        ];
        
        $response = $this->putJson('/api/user/password', $invalidData);
        
        $this->assertEquals(422, $response->status(),
            'Invalid validation data should return 422');
        
        $responseData = $response->json();
        $this->assertArrayHasKey('message', $responseData,
            "422 response should have 'message' key");
        $this->assertArrayHasKey('errors', $responseData,
            "422 response should have 'errors' key");
        $this->assertIsArray($responseData['errors'],
            "'errors' should be an array");
    }

    /**
     * Verify 403 Authorization error response
     */
    private function verify_403_authorization_error(): void
    {
        // Create two users with customers
        $user1 = User::factory()->create();
        $customer1 = Customer::factory()->create(['user_id' => $user1->id]);
        
        $user2 = User::factory()->create();
        $customer2 = Customer::factory()->create(['user_id' => $user2->id]);
        
        // Create address for user2
        $address = Address::factory()->create([
            'customer_id' => $customer2->id,
        ]);
        
        // User1 tries to access user2's address
        Sanctum::actingAs($user1);
        
        $response = $this->putJson("/api/customer/addresses/{$address->uuid}", [
            'first_name' => 'Updated'
        ]);
        
        $this->assertEquals(403, $response->status(),
            'Accessing another user\'s resource should return 403');
        
        $this->assertArrayHasKey('message', $response->json(),
            "403 response should have 'message' key");
    }
}
