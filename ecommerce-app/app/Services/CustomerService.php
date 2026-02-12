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

    /**
     * Get a specific order for a customer.
     * 
     * Retrieves a single order by UUID if it belongs to the customer.
     * 
     * @param Customer $customer The customer whose order to retrieve
     * @param string $uuid The order UUID
     * @return \App\Models\Order|null The order or null if not found
     */
    public function getOrder(Customer $customer, string $uuid)
    {
        return $customer->user->orders()
            ->where('uuid', $uuid)
            ->with([
                'items.product.images',
                'shippingAddress',
                'billingAddress'
            ])
            ->first();
    }
}
