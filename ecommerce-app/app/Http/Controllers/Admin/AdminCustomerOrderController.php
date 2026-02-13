<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CustomerOrderResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminCustomerOrderController extends Controller
{
    public function index(Request $request, int $id): AnonymousResourceCollection
    {
        $customer = Customer::query()->with('user')->findOrFail($id);

        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(10, min($perPage, 100));

        $orders = $customer->user->orders()
            ->with(['orderStatus', 'shippingStatus'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return CustomerOrderResource::collection($orders);
    }
}
