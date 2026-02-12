<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * CustomerAddressService handles business logic for customer address management.
 * 
 * Provides CRUD operations for addresses with ownership validation,
 * default address management, and soft delete cleanup.
 */
class CustomerAddressService
{
    /**
     * Get all addresses for a customer (excluding soft-deleted).
     * 
     * @param Customer $customer The customer whose addresses to retrieve
     * @return Collection Collection of Address models
     */
    public function getAddresses(Customer $customer): Collection
    {
        return $customer->addresses()
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Create a new address for a customer.
     * 
     * @param Customer $customer The customer to associate the address with
     * @param array $data Address data from validated request
     * @return Address The created address
     */
    public function createAddress(Customer $customer, array $data): Address
    {
        $data['customer_id'] = $customer->id;
        $data['user_id'] = $customer->user_id;
        
        return Address::create($data);
    }

    /**
     * Update an existing address.
     * 
     * Validates that the address belongs to the customer before updating.
     * 
     * @param Address $address The address to update
     * @param array $data Updated address data from validated request
     * @return Address The updated address
     * @throws \Exception If address doesn't belong to customer
     */
    public function updateAddress(Address $address, array $data): Address
    {
        $address->update($data);
        
        return $address->fresh();
    }

    /**
     * Delete an address (soft delete).
     * 
     * Validates ownership and cleans up default address references
     * before performing soft delete.
     * 
     * @param Address $address The address to delete
     * @return bool True if deletion was successful
     */
    public function deleteAddress(Address $address): bool
    {
        return DB::transaction(function () use ($address) {
            $customer = $address->customer;
            
            // Clear default address references if this address is set as default
            if ($customer->default_shipping_address_id === $address->id) {
                $this->clearDefaultAddress($customer, 'shipping');
            }
            
            if ($customer->default_billing_address_id === $address->id) {
                $this->clearDefaultAddress($customer, 'billing');
            }
            
            // Perform soft delete
            return $address->delete();
        });
    }

    /**
     * Set an address as default for shipping or billing.
     * 
     * Validates ownership before setting the address as default.
     * 
     * @param Customer $customer The customer
     * @param Address $address The address to set as default
     * @param string $type Type of default address ('shipping' or 'billing')
     * @return bool True if operation was successful
     */
    public function setDefaultAddress(Customer $customer, Address $address, string $type): bool
    {
        $field = $type === 'shipping' 
            ? 'default_shipping_address_id' 
            : 'default_billing_address_id';
        
        return $customer->update([
            $field => $address->id
        ]);
    }

    /**
     * Clear default address reference for a customer.
     * 
     * @param Customer $customer The customer
     * @param string $type Type of default address to clear ('shipping' or 'billing')
     * @return void
     */
    private function clearDefaultAddress(Customer $customer, string $type): void
    {
        $field = $type === 'shipping' 
            ? 'default_shipping_address_id' 
            : 'default_billing_address_id';
        
        $customer->update([
            $field => null
        ]);
    }

    /**
     * Validate that an address belongs to a customer.
     * 
     * @param Address $address The address to validate
     * @param Customer $customer The customer
     * @return bool True if address belongs to customer
     */
    private function validateAddressOwnership(Address $address, Customer $customer): bool
    {
        return $address->customer_id === $customer->id;
    }
}
