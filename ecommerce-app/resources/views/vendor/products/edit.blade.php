@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Editar producto</h1>
@include('vendor.products.form', [
    'action' => route('vendor.products.update', $product->id),
    'method' => 'PUT',
    'product' => $product,
    'vendorProduct' => $vendorProduct,
])
@endsection
