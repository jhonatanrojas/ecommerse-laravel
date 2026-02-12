<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

/**
 * CustomerOrderController handles customer order operations.
 * 
 * Provides endpoints for customers to retrieve their order history
 * with complete order details including items and addresses.
 */
class CustomerOrderController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param CustomerService $customerService Service for customer business logic
     */
    public function __construct(
        private CustomerService $customerService
    ) {}

    /**
     * Display a listing of the authenticated user's orders.
     * 
     * Retrieves all orders for the authenticated user's customer profile
     * with order items, shipping address, and billing address loaded.
     * Returns an empty array if the user has no customer profile or no orders.
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
        
        $orders = $this->customerService->getOrders($user->customer);
        
        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Display the specified order.
     * 
     * Retrieves a specific order for the authenticated user's customer profile.
     * Returns 404 if order doesn't exist or doesn't belong to the user.
     * 
     * @param string $uuid Order UUID
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $user = auth()->user();
        
        // Check if user has a customer profile
        if (!$user->customer) {
            return response()->json([
                'message' => 'Usuario no tiene perfil de cliente.'
            ], 403);
        }
        
        $order = $this->customerService->getOrder($user->customer, $uuid);
        
        if (!$order) {
            return response()->json([
                'message' => 'Pedido no encontrado.'
            ], 404);
        }
        
        return response()->json(new OrderResource($order));
    }
}
