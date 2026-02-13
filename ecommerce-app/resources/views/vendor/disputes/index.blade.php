@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Disputas</h1>
<form method="POST" action="{{ route('vendor.disputes.store') }}" class="bg-white border rounded p-4 space-y-3 mb-4">
    @csrf
    <select name="order_id" class="border rounded px-3 py-2 w-full">
        <option value="">Orden opcional</option>
        @foreach($vendorOrders as $vendorOrder)
            <option value="{{ $vendorOrder->order_id }}">{{ $vendorOrder->order->order_number ?? ('#'.$vendorOrder->order_id) }}</option>
        @endforeach
    </select>
    <input name="title" class="border rounded px-3 py-2 w-full" placeholder="Título" required>
    <textarea name="description" class="border rounded px-3 py-2 w-full" placeholder="Descripción" required></textarea>
    <button class="px-4 py-2 bg-black text-white rounded">Crear disputa</button>
</form>

<div class="bg-white border rounded overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100"><tr><th class="p-2 text-left">Título</th><th class="p-2">Estado</th><th class="p-2">Fecha</th><th class="p-2 text-left">Respuesta admin</th></tr></thead>
        <tbody>
            @foreach($disputes as $dispute)
                <tr class="border-t"><td class="p-2">{{ $dispute->title }}</td><td class="p-2">{{ $dispute->status }}</td><td class="p-2">{{ $dispute->created_at }}</td><td class="p-2">{{ $dispute->admin_response }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $disputes->links() }}</div>
@endsection
