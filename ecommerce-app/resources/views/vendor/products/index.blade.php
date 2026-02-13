@extends('layouts.vendor')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Mis productos</h1>
    <a href="{{ route('vendor.products.create') }}" class="px-4 py-2 bg-black text-white rounded">Nuevo producto</a>
</div>

<div class="bg-white border rounded overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Producto</th>
                <th class="p-2 text-left">Precio</th>
                <th class="p-2 text-left">Moderación</th>
                <th class="p-2 text-left">Activo</th>
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendorProducts as $vp)
                <tr class="border-t">
                    <td class="p-2">{{ $vp->product->name }}</td>
                    <td class="p-2">${{ number_format($vp->product->price, 2) }}</td>
                    <td class="p-2">{{ $vp->is_approved ? 'Aprobado' : 'Pendiente' }}</td>
                    <td class="p-2">{{ $vp->is_active ? 'Sí' : 'No' }}</td>
                    <td class="p-2 text-right space-x-2">
                        <a href="{{ route('vendor.products.edit', $vp->product_id) }}" class="text-blue-600">Editar</a>
                        <form class="inline" method="POST" action="{{ route('vendor.products.toggle', $vp->product_id) }}">
                            @csrf @method('PATCH')
                            <button class="text-orange-600">{{ $vp->is_active ? 'Pausar' : 'Activar' }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $vendorProducts->links() }}</div>
@endsection
