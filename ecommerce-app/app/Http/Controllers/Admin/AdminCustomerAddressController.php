<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CustomerAddressResource;
use App\Models\Customer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminCustomerAddressController extends Controller
{
    public function index(int $id): AnonymousResourceCollection
    {
        $customer = Customer::query()->findOrFail($id);

        $addresses = $customer->addresses()
            ->with('customer')
            ->latest()
            ->get();

        return CustomerAddressResource::collection($addresses);
    }
}
