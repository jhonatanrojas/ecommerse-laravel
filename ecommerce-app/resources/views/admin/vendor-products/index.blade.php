@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Moderación de productos marketplace</h1>
<div class="bg-white rounded border overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100"><tr><th class="p-2 text-left">Producto</th><th class="p-2">Vendedor</th><th class="p-2">Estado</th><th class="p-2 text-left">Notas</th><th></th></tr></thead>
        <tbody>
            @foreach($products as $row)
                <tr class="border-t">
                    <td class="p-2">{{ $row->product->name }}</td>
                    <td class="p-2">{{ $row->vendor->business_name }}</td>
                    <td class="p-2">{{ $row->is_approved ? 'Aprobado' : 'Pendiente/Rechazado' }}</td>
                    <td class="p-2">{{ $row->moderation_notes }}</td>
                    <td class="p-2">
                        <form method="POST" action="{{ route('admin.vendor-products.moderate', $row) }}" class="flex gap-2">
                            @csrf @method('PATCH')
                            <input name="moderation_notes" class="border rounded px-2 py-1" placeholder="Nota">
                            <button name="action" value="approve" class="px-3 py-1 bg-green-600 text-white rounded">Aprobar</button>
                            <button name="action" value="reject" class="px-3 py-1 bg-red-600 text-white rounded">Rechazar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
