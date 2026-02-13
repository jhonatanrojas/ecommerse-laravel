@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Liquidaciones a vendedores</h1>
<div class="bg-white rounded border overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100"><tr><th class="p-2 text-left">Vendedor</th><th class="p-2">Monto</th><th class="p-2">Proveedor</th><th class="p-2">Estado</th><th></th></tr></thead>
        <tbody>
            @foreach($payouts as $payout)
                <tr class="border-t">
                    <td class="p-2">{{ $payout->vendor->business_name }}</td>
                    <td class="p-2">${{ number_format($payout->amount,2) }}</td>
                    <td class="p-2">{{ $payout->provider }}</td>
                    <td class="p-2">{{ $payout->status }}</td>
                    <td class="p-2 text-right">
                        @if(in_array($payout->status, ['pending', 'failed']))
                            <form method="POST" action="{{ route('admin.vendor-payouts.process', $payout->id) }}">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1 bg-black text-white rounded">Procesar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $payouts->links() }}</div>
@endsection
