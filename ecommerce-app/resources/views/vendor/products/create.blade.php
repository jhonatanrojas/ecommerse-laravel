@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Crear producto</h1>
@include('vendor.products.form', [
    'action' => route('vendor.products.store'),
    'method' => 'POST',
    'product' => null,
    'vendorProduct' => null,
])
@endsection
