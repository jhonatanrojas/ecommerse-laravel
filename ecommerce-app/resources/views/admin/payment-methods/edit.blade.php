@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Editar Método de Pago</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Actualiza credenciales, estado y driver del método.</p>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden">
    <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        @include('admin.payment-methods._form', ['paymentMethod' => $paymentMethod, 'submitButtonText' => 'Actualizar Método'])
    </form>
</div>
@endsection

