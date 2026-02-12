<?php

namespace Tests\Feature\Properties;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Property-Based Tests for Customer Address Management
 * 
 * These tests validate universal properties that should hold true
 * across many randomly generated inputs for address operations.
 */
class AddressPropertiesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Feature: customer-backend-api, Property 6: Customer Addresses Retrieval and Formatting
     * 
     * For any authenticated customer, the system should return only their addresses
     * (excluding soft-deleted) with AddressResource formatting that includes uuid, first_name,
     * last_name, company, phone, address_line1, address_line2, city, state, postal_code,
     * country, type, and is_default.
     * 
     * Validates: Requirements 5.1, 5.2
     */
    public function test_property_6_customer_addresses_retrieval_and_formatting(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Create authenticated user with customer
            $user = User::factory()->create();
            $customer = Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Create random number of addresses for this customer
            $addressCount = rand(1, 5);
            $createdAddresses = [];
            
            for ($j = 0; $j < $addressCount; $j++) {
                $createdAddresses[] = Address::factory()->create([
                    'customer_id' => $customer->id,
                ]);
            }

            // Create some soft-deleted addresses that should NOT appear
            $deletedCount = rand(0, 2);
            for ($j = 0; $j < $deletedCount; $j++) {
                Address::factory()->create([
                    'customer_id' => $customer->id,
                    'deleted_at' => now(),
                ]);
            }

            // Create addresses for another customer that should NOT appear
            $otherUser = User::factory()->create();
            $otherCustomer = Customer::factory()->create(['user_id' => $otherUser->id]);
            $otherAddressCount = rand(1, 3);
            for ($j = 0; $j < $otherAddressCount; $j++) {
                Address::factory()->create([
                    'customer_id' => $otherCustomer->id,
                ]);
            }

            // Make request to list addresses
            $response = $this->getJson('/api/customer/addresses');

            // Verify HTTP 200 response
            $response->assertStatus(200);

            // Verify correct number of addresses returned
            $responseData = $response->json();
            $this->assertCount($addressCount, $responseData, "Property 6 violated: Should return exactly {$addressCount} addresses");

            // Verify each address has correct structure
            foreach ($responseData as $addressData) {
                $this->assertArrayHasKey('id', $addressData);
                $this->assertArrayHasKey('uuid', $addressData);
                $this->assertArrayHasKey('first_name', $addressData);
                $this->assertArrayHasKey('last_name', $addressData);
                $this->assertArrayHasKey('company', $addressData);
                $this->assertArrayHasKey('phone', $addressData);
                $this->assertArrayHasKey('address_line1', $addressData);
                $this->assertArrayHasKey('address_line2', $addressData);
                $this->assertArrayHasKey('city', $addressData);
                $this->assertArrayHasKey('state', $addressData);
                $this->assertArrayHasKey('postal_code', $addressData);
                $this->assertArrayHasKey('country', $addressData);
                $this->assertArrayHasKey('type', $addressData);
                $this->assertArrayHasKey('is_default', $addressData);

                // Verify address belongs to authenticated customer
                $address = Address::where('uuid', $addressData['uuid'])->first();
                $this->assertNotNull($address);
                $this->assertEquals($customer->id, $address->customer_id, "Property 6 violated: Address should belong to authenticated customer");
            }
        }
    }

    /**
     * Feature: customer-backend-api, Property 6: Customer Addresses Retrieval - Empty List
     * 
     * For any authenticated customer with no addresses,
     * the system should return an empty array with HTTP 200.
     * 
     * Validates: Requirements 5.1
     */
    public function test_property_6_customer_addresses_retrieval_empty_list(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 50; $i++) {
            // Create authenticated user with customer but no addresses
            $user = User::factory()->create();
            Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Make request to list addresses
            $response = $this->getJson('/api/customer/addresses');

            // Verify HTTP 200 response
            $response->assertStatus(200);

            // Verify empty array
            $responseData = $response->json();
            $this->assertIsArray($responseData);
            $this->assertCount(0, $responseData, "Property 6 violated: Should return empty array when customer has no addresses");
        }
    }

    /**
     * Feature: customer-backend-api, Property 7: Address Creation Validation and Processing
     * 
     * For any request to create an address, the system should validate required fields
     * (first_name, last_name, address_line1, city, state, postal_code, country),
     * validate type as enum valid, validate phone as optional but with valid format,
     * create address associated to the correct customer, and return HTTP 201 with formatted address.
     * 
     * Validates: Requirements 6.1, 6.2, 6.3, 6.4, 6.5, 6.6
     */
    public function test_property_7_address_creation_validation_and_processing(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Create authenticated user with customer
            $user = User::factory()->create();
            $customer = Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Generate random valid address data
            $addressData = [
                'first_name' => $this->generateRandomString(rand(3, 50)),
                'last_name' => $this->generateRandomString(rand(3, 50)),
                'company' => rand(0, 1) ? $this->generateRandomString(rand(3, 50)) : null,
                'phone' => rand(0, 1) ? $this->generateRandomPhone() : null,
                'address_line1' => $this->generateRandomString(rand(5, 100)),
                'address_line2' => rand(0, 1) ? $this->generateRandomString(rand(5, 100)) : null,
                'city' => $this->generateRandomString(rand(3, 50)),
                'state' => $this->generateRandomString(rand(2, 50)),
                'postal_code' => $this->generateRandomPostalCode(),
                'country' => $this->generateRandomCountryCode(),
                'type' => rand(0, 1) ? 'shipping' : 'billing',
            ];

            // Make request to create address
            $response = $this->postJson('/api/customer/addresses', $addressData);

            // Verify HTTP 201 response
            $response->assertStatus(201);

            // Verify response structure
            $response->assertJsonStructure([
                'id',
                'uuid',
                'first_name',
                'last_name',
                'company',
                'phone',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'postal_code',
                'country',
                'type',
                'is_default',
            ]);

            // Verify address was created in database
            $this->assertDatabaseHas('addresses', [
                'customer_id' => $customer->id,
                'first_name' => $addressData['first_name'],
                'last_name' => $addressData['last_name'],
                'address_line1' => $addressData['address_line1'],
                'city' => $addressData['city'],
                'state' => $addressData['state'],
                'postal_code' => $addressData['postal_code'],
                'country' => $addressData['country'],
                'type' => $addressData['type'],
            ]);

            // Verify address belongs to correct customer
            $createdAddress = Address::where('customer_id', $customer->id)
                ->where('first_name', $addressData['first_name'])
                ->first();
            
            $this->assertNotNull($createdAddress, "Property 7 violated: Address should be created");
            $this->assertEquals($customer->id, $createdAddress->customer_id, "Property 7 violated: Address should belong to authenticated customer");
        }
    }

    /**
     * Feature: customer-backend-api, Property 7: Address Creation Validation - Missing Required Fields
     * 
     * For any request to create an address with missing required fields,
     * the system should return HTTP 422 with validation errors.
     * 
     * Validates: Requirements 6.1, 6.2
     */
    public function test_property_7_address_creation_validation_missing_required_fields(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 50; $i++) {
            // Create authenticated user with customer
            $user = User::factory()->create();
            Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Required fields
            $requiredFields = ['first_name', 'last_name', 'address_line1', 'city', 'state', 'postal_code', 'country', 'type'];
            
            // Pick a random required field to omit
            $fieldToOmit = $requiredFields[array_rand($requiredFields)];

            // Generate address data without the required field
            $addressData = [
                'first_name' => $this->generateRandomString(rand(3, 50)),
                'last_name' => $this->generateRandomString(rand(3, 50)),
                'address_line1' => $this->generateRandomString(rand(5, 100)),
                'city' => $this->generateRandomString(rand(3, 50)),
                'state' => $this->generateRandomString(rand(2, 50)),
                'postal_code' => $this->generateRandomPostalCode(),
                'country' => $this->generateRandomCountryCode(),
                'type' => rand(0, 1) ? 'shipping' : 'billing',
            ];

            // Remove the field
            unset($addressData[$fieldToOmit]);

            // Make request to create address
            $response = $this->postJson('/api/customer/addresses', $addressData);

            // Verify HTTP 422 response
            $response->assertStatus(422);
            
            // Verify error message contains the missing field
            $response->assertJsonValidationErrors($fieldToOmit);
        }
    }

    /**
     * Feature: customer-backend-api, Property 7: Address Creation Validation - Invalid Type
     * 
     * For any request to create an address with invalid type enum,
     * the system should return HTTP 422 with validation errors.
     * 
     * Validates: Requirements 6.4
     */
    public function test_property_7_address_creation_validation_invalid_type(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 50; $i++) {
            // Create authenticated user with customer
            $user = User::factory()->create();
            Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Generate address data with invalid type
            $invalidTypes = ['home', 'work', 'other', 'invalid', '', 123, true];
            $addressData = [
                'first_name' => $this->generateRandomString(rand(3, 50)),
                'last_name' => $this->generateRandomString(rand(3, 50)),
                'address_line1' => $this->generateRandomString(rand(5, 100)),
                'city' => $this->generateRandomString(rand(3, 50)),
                'state' => $this->generateRandomString(rand(2, 50)),
                'postal_code' => $this->generateRandomPostalCode(),
                'country' => $this->generateRandomCountryCode(),
                'type' => $invalidTypes[array_rand($invalidTypes)],
            ];

            // Make request to create address
            $response = $this->postJson('/api/customer/addresses', $addressData);

            // Verify HTTP 422 response
            $response->assertStatus(422);
            
            // Verify error message for type field
            $response->assertJsonValidationErrors('type');
        }
    }

    /**
     * Feature: customer-backend-api, Property 9: Address Deletion with Default Address Cleanup
     * 
     * For any request to delete an address, the system should verify ownership,
     * return HTTP 404 if not exists, return HTTP 403 if not owned,
     * clean up default address references if applicable, perform soft delete,
     * and return HTTP 204.
     * 
     * Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.6
     */
    public function test_property_9_address_deletion_with_default_address_cleanup(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 100; $i++) {
            // Create authenticated user with customer
            $user = User::factory()->create();
            $customer = Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Create an address
            $address = Address::factory()->create([
                'customer_id' => $customer->id,
                'type' => rand(0, 1) ? 'shipping' : 'billing',
            ]);

            // Randomly set as default address
            $setAsDefault = rand(0, 1);
            if ($setAsDefault) {
                if ($address->type === 'shipping') {
                    $customer->update(['default_shipping_address_id' => $address->id]);
                } else {
                    $customer->update(['default_billing_address_id' => $address->id]);
                }
            }

            // Make request to delete address
            $response = $this->deleteJson("/api/customer/addresses/{$address->uuid}");

            // Verify HTTP 204 response
            $response->assertStatus(204);

            // Verify address was soft deleted
            $this->assertSoftDeleted('addresses', [
                'id' => $address->id,
            ]);

            // Verify default address reference was cleaned up if it was set
            if ($setAsDefault) {
                $customer->refresh();
                if ($address->type === 'shipping') {
                    $this->assertNull($customer->default_shipping_address_id, "Property 9 violated: Default shipping address reference should be cleared");
                } else {
                    $this->assertNull($customer->default_billing_address_id, "Property 9 violated: Default billing address reference should be cleared");
                }
            }
        }
    }

    /**
     * Feature: customer-backend-api, Property 9: Address Deletion - Ownership Verification
     * 
     * For any request to delete an address that doesn't belong to the user,
     * the system should return HTTP 403.
     * 
     * Validates: Requirements 8.2
     */
    public function test_property_9_address_deletion_ownership_verification(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 50; $i++) {
            // Create two users with customers
            $user1 = User::factory()->create();
            $customer1 = Customer::factory()->create(['user_id' => $user1->id]);
            
            $user2 = User::factory()->create();
            $customer2 = Customer::factory()->create(['user_id' => $user2->id]);

            // Create address for user2
            $address = Address::factory()->create([
                'customer_id' => $customer2->id,
            ]);

            // Authenticate as user1
            Sanctum::actingAs($user1);

            // Try to delete user2's address
            $response = $this->deleteJson("/api/customer/addresses/{$address->uuid}");

            // Verify HTTP 403 response
            $response->assertStatus(403);

            // Verify address was NOT deleted
            $this->assertDatabaseHas('addresses', [
                'id' => $address->id,
                'deleted_at' => null,
            ]);
        }
    }

    /**
     * Feature: customer-backend-api, Property 9: Address Deletion - Not Found
     * 
     * For any request to delete a non-existent address,
     * the system should return HTTP 404.
     * 
     * Validates: Requirements 8.3
     */
    public function test_property_9_address_deletion_not_found(): void
    {
        // Run property test with multiple iterations
        for ($i = 0; $i < 50; $i++) {
            // Create authenticated user with customer
            $user = User::factory()->create();
            Customer::factory()->create(['user_id' => $user->id]);
            Sanctum::actingAs($user);

            // Generate random UUID that doesn't exist
            $fakeUuid = $this->generateRandomUuid();

            // Try to delete non-existent address
            $response = $this->deleteJson("/api/customer/addresses/{$fakeUuid}");

            // Verify HTTP 404 response
            $response->assertStatus(404);
        }
    }

    // Helper methods for generating random data

    private function generateRandomString(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return trim($string);
    }

    private function generateRandomPhone(): string
    {
        return '+' . rand(1, 999) . rand(1000000000, 9999999999);
    }

    private function generateRandomPostalCode(): string
    {
        return (string) rand(10000, 99999);
    }

    private function generateRandomCountryCode(): string
    {
        $codes = ['US', 'CA', 'MX', 'GB', 'FR', 'DE', 'ES', 'IT', 'BR', 'AR', 'CL', 'CO', 'PE'];
        return $codes[array_rand($codes)];
    }

    private function generateRandomUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
