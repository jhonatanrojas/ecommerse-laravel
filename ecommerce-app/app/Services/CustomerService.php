<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Collection;

/**
 * CustomerService handles customer-related business logic.
 * 
 * Provides methods for retrieving customer orders and managing
 * customer-specific operations.
 */
class CustomerService
{
    /**
     * Get all orders for a customer.
     * 
     * Retrieves orders through the customer's user relationship
     * with eager loading of order items and shipping/billing addresses.
     * 
     * @param Customer $customer The customer whose orders to retrieve
     * @return Collection Collection of orders with loaded relationships
     */
    public function getOrders(Customer $customer): Collection
    {
        return $customer->user->orders()
            ->with([
                'items',
                'shippingAddress',
                'billingAddress'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
