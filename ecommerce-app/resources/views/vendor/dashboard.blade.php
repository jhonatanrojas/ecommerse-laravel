@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Dashboard vendedor</h1>
<div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    <div class="bg-white p-4 rounded border"><div class="text-xs text-gray-500">Productos</div><div class="text-2xl font-bold">{{ $stats['products_total'] }}</div></div>
    <div class="bg-white p-4 rounded border"><div class="text-xs text-gray-500">Aprobados</div><div class="text-2xl font-bold">{{ $stats['products_approved'] }}</div></div>
    <div class="bg-white p-4 rounded border"><div class="text-xs text-gray-500">Órdenes</div><div class="text-2xl font-bold">{{ $stats['orders_total'] }}</div></div>
    <div class="bg-white p-4 rounded border"><div class="text-xs text-gray-500">Por liquidar</div><div class="text-2xl font-bold">${{ number_format($stats['pending_payout'],2) }}</div></div>
    <div class="bg-white p-4 rounded border"><div class="text-xs text-gray-500">Liquidado</div><div class="text-2xl font-bold">${{ number_format($stats['paid_out'],2) }}</div></div>
</div>
@endsection
