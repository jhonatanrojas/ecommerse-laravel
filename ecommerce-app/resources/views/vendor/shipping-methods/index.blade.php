@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Métodos de envío</h1>
<form method="POST" action="{{ route('vendor.shipping-methods.store') }}" class="bg-white border rounded p-4 grid grid-cols-1 md:grid-cols-4 gap-2 mb-4">
    @csrf
    <input name="name" class="border rounded px-3 py-2" placeholder="Nombre" required>
    <input name="code" class="border rounded px-3 py-2" placeholder="Código" required>
    <input name="base_rate" class="border rounded px-3 py-2" placeholder="Tarifa base" type="number" step="0.01" required>
    <input name="extra_rate" class="border rounded px-3 py-2" placeholder="Tarifa extra" type="number" step="0.01">
    <button class="md:col-span-4 px-4 py-2 bg-black text-white rounded">Crear método</button>
</form>

<div class="bg-white border rounded overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100"><tr><th class="p-2 text-left">Nombre</th><th class="p-2">Código</th><th class="p-2">Base</th><th class="p-2">Extra</th><th class="p-2">Activo</th><th></th></tr></thead>
        <tbody>
            @foreach($methods as $method)
                <tr class="border-t">
                    <td class="p-2">{{ $method->name }}</td>
                    <td class="p-2">{{ $method->code }}</td>
                    <td class="p-2">${{ number_format($method->base_rate,2) }}</td>
                    <td class="p-2">${{ number_format($method->extra_rate,2) }}</td>
                    <td class="p-2">{{ $method->is_active ? 'Sí' : 'No' }}</td>
                    <td class="p-2 text-right">
                        <form method="POST" action="{{ route('vendor.shipping-methods.destroy', $method) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
