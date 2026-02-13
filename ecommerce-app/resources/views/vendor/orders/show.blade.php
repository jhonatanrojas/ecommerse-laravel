@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Orden {{ $vendorOrder->order->order_number }}</h1>

<div class="bg-white border rounded p-4 mb-4">
    <p><strong>Subtotal:</strong> ${{ number_format($vendorOrder->subtotal,2) }}</p>
    <p><strong>Comisión:</strong> ${{ number_format($vendorOrder->commission_amount,2) }}</p>
    <p><strong>Ganancia:</strong> ${{ number_format($vendorOrder->vendor_earnings,2) }}</p>
</div>

<form method="POST" action="{{ route('vendor.orders.shipping.update', $vendorOrder->id) }}" class="bg-white border rounded p-4 space-y-3">
    @csrf
    @method('PATCH')
    <div>
        <label class="block text-sm">Estado de envío</label>
        <select name="shipping_status" class="border rounded px-3 py-2 w-full">
            @foreach(['pending','processing','shipped','delivered','cancelled'] as $status)
                <option value="{{ $status }}" @selected($vendorOrder->shipping_status === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <input name="shipping_method" value="{{ $vendorOrder->shipping_method }}" class="border rounded px-3 py-2 w-full" placeholder="Método envío">
    <input name="tracking_number" value="{{ $vendorOrder->tracking_number }}" class="border rounded px-3 py-2 w-full" placeholder="Tracking">
    <button class="px-4 py-2 bg-black text-white rounded">Actualizar envío</button>
</form>
@endsection
