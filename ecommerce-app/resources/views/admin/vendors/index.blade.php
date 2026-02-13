@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Vendedores</h1>
<div class="bg-white rounded border overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100"><tr><th class="p-2 text-left">Negocio</th><th class="p-2">Documento</th><th class="p-2">Estado</th><th class="p-2">Comisión</th><th class="p-2"></th></tr></thead>
        <tbody>
            @foreach($vendors as $vendor)
                <tr class="border-t">
                    <td class="p-2">{{ $vendor->business_name }}</td>
                    <td class="p-2">{{ $vendor->document }}</td>
                    <td class="p-2">{{ $vendor->status }}</td>
                    <td class="p-2">{{ $vendor->commission_rate ?? 'Global' }}%</td>
                    <td class="p-2 text-right"><a class="text-blue-600" href="{{ route('admin.vendors.show', $vendor) }}">Gestionar</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $vendors->links() }}</div>
@endsection
