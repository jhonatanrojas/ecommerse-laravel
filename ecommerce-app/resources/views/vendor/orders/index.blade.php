@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Órdenes del vendedor</h1>
<div class="bg-white border rounded overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Orden</th>
                <th class="p-2 text-left">Cliente</th>
                <th class="p-2 text-left">Subtotal</th>
                <th class="p-2 text-left">Comisión</th>
                <th class="p-2 text-left">Ganancia</th>
                <th class="p-2 text-left">Shipping</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $row)
                <tr class="border-t">
                    <td class="p-2">{{ $row->order->order_number }}</td>
                    <td class="p-2">{{ $row->order->user->name ?? 'N/A' }}</td>
                    <td class="p-2">${{ number_format($row->subtotal,2) }}</td>
                    <td class="p-2">${{ number_format($row->commission_amount,2) }}</td>
                    <td class="p-2">${{ number_format($row->vendor_earnings,2) }}</td>
                    <td class="p-2">{{ $row->shipping_status }}</td>
                    <td class="p-2 text-right"><a class="text-blue-600" href="{{ route('vendor.orders.show', $row->id) }}">Ver</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
