<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(10, min($perPage, 100));

        $customers = Customer::query()
            ->with('user')
            ->whereHas('user')
            ->when($filters['search'], function (Builder $query, string $search): void {
                $query->whereHas('user', function (Builder $userQuery) use ($search): void {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] !== null && $filters['status'] !== '', function (Builder $query): void {
                $query->whereHas('user', function (Builder $userQuery) use ($filters): void {
                    $userQuery->where('is_active', (bool) $filters['status']);
                });
            })
            ->when($filters['date_from'], function (Builder $query, string $dateFrom): void {
                $query->whereHas('user', function (Builder $userQuery) use ($dateFrom): void {
                    $userQuery->whereDate('created_at', '>=', $dateFrom);
                });
            })
            ->when($filters['date_to'], function (Builder $query, string $dateTo): void {
                $query->whereHas('user', function (Builder $userQuery) use ($dateTo): void {
                    $userQuery->whereDate('created_at', '<=', $dateTo);
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'filters'));
    }

    public function show(int $id): View
    {
        $customer = Customer::query()
            ->with(['user', 'defaultShippingAddress', 'defaultBillingAddress'])
            ->findOrFail($id);

        $orders = $customer->user->orders()
            ->with(['orderStatus', 'shippingStatus'])
            ->latest()
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString();

        $addresses = $customer->addresses()
            ->latest()
            ->get();

        $totalSpent = (float) $customer->user->orders()
            ->where('payment_status', 'paid')
            ->sum('total');

        $lastPurchase = $customer->user->orders()
            ->latest('created_at')
            ->first();

        $totalOrders = $customer->user->orders()->count();

        return view('admin.customers.show', compact(
            'customer',
            'orders',
            'addresses',
            'totalSpent',
            'lastPurchase',
            'totalOrders'
        ));
    }

    public function update(UpdateCustomerRequest $request, int $id): RedirectResponse
    {
        $customer = Customer::query()->with('user')->findOrFail($id);

        DB::transaction(function () use ($request, $customer): void {
            $customer->user->update([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
            ]);

            $customer->update([
                'phone' => $request->validated('phone'),
                'document' => $request->validated('document'),
                'birthdate' => $request->validated('birthdate'),
            ]);
        });

        return redirect()
            ->route('admin.customers.show', $customer->id)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $customer = Customer::query()->with('user')->findOrFail($id);

        $customer->user->update([
            'is_active' => !$customer->user->is_active,
        ]);

        return back()->with('success', 'Estado del cliente actualizado correctamente.');
    }
}
