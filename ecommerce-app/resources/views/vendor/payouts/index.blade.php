@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Liquidaciones</h1>
<div class="bg-white border rounded p-4 mb-4">
    <p class="text-sm text-gray-500">Saldo disponible</p>
    <p class="text-3xl font-bold">${{ number_format($pendingEarnings,2) }}</p>
    <form method="POST" action="{{ route('vendor.payouts.request') }}" class="mt-3 flex gap-2">
        @csrf
        <input type="number" step="0.01" min="0" name="amount" class="border rounded px-3 py-2" placeholder="Monto (opcional)">
        <button class="px-4 py-2 bg-black text-white rounded">Solicitar liquidación</button>
    </form>
</div>
<div class="bg-white border rounded overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100"><tr><th class="p-2 text-left">Fecha</th><th class="p-2">Monto</th><th class="p-2">Estado</th><th class="p-2">Referencia</th></tr></thead>
        <tbody>
            @foreach($payouts as $payout)
                <tr class="border-t"><td class="p-2">{{ $payout->created_at }}</td><td class="p-2">${{ number_format($payout->amount,2) }}</td><td class="p-2">{{ $payout->status }}</td><td class="p-2">{{ $payout->transaction_reference }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $payouts->links() }}</div>
@endsection
