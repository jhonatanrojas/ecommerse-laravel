@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">{{ $vendor->business_name }}</h1>

<div class="bg-white rounded border p-4 mb-4">
    <p><strong>Usuario:</strong> {{ $vendor->user->name }} ({{ $vendor->user->email }})</p>
    <p><strong>Documento:</strong> {{ $vendor->document }}</p>
    <p><strong>Estado actual:</strong> {{ $vendor->status }}</p>

    <form method="POST" action="{{ route('admin.vendors.status.update', $vendor) }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2">
        @csrf @method('PATCH')
        <select name="status" class="border rounded px-3 py-2">
            @foreach(['pending','approved','rejected','suspended'] as $status)
                <option value="{{ $status }}" @selected($vendor->status === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <input name="rejection_reason" class="border rounded px-3 py-2" placeholder="Motivo rechazo/suspensión">
        <button class="px-4 py-2 bg-black text-white rounded">Actualizar estado</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded border p-4">
        <h2 class="font-semibold mb-2">Productos</h2>
        <p>Total: {{ $vendor->products->count() }}</p>
    </div>
    <div class="bg-white rounded border p-4">
        <h2 class="font-semibold mb-2">Órdenes</h2>
        <p>Total: {{ $vendor->orders->count() }}</p>
    </div>
    <div class="bg-white rounded border p-4">
        <h2 class="font-semibold mb-2">Payouts</h2>
        <p>Total: {{ $vendor->payouts->count() }}</p>
    </div>
</div>
@endsection
