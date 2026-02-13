<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentMethodRequest;
use App\Http\Requests\Admin\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('search', '');

        $paymentMethods = PaymentMethod::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('driver_class', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payment-methods.index', compact('paymentMethods', 'search'));
    }

    public function create(): View
    {
        return view('admin.payment-methods.create');
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        PaymentMethod::create($request->validatedPayload());

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    public function edit(PaymentMethod $payment_method): View
    {
        return view('admin.payment-methods.edit', [
            'paymentMethod' => $payment_method,
        ]);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $payment_method): RedirectResponse
    {
        $payment_method->update($request->validatedPayload());

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Método de pago actualizado correctamente.');
    }

    public function toggleStatus(PaymentMethod $payment_method): RedirectResponse
    {
        $payment_method->update([
            'is_active' => !$payment_method->is_active,
        ]);

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(PaymentMethod $payment_method): RedirectResponse
    {
        $payment_method->delete();

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Método de pago eliminado correctamente.');
    }

    public function indexJson(Request $request)
    {
        $paymentMethods = PaymentMethod::query()->latest('id')->get();

        return PaymentMethodResource::collection($paymentMethods);
    }
}

