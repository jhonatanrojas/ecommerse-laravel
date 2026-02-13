@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Métodos de Pago</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configura, activa y desactiva métodos de pago para el checkout.</p>
</div>

<div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
        <div class="w-full md:w-1/2">
            <form method="GET" action="{{ route('admin.payment-methods.index') }}" class="flex items-center">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Buscar método...">
                </div>
                <button type="submit" class="ml-2 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">Buscar</button>
                @if($search)
                    <a href="{{ route('admin.payment-methods.index') }}" class="ml-2 text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-4 py-2">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="w-full md:w-auto">
            <a href="{{ route('admin.payment-methods.create') }}" class="inline-flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nuevo Método
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Driver</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Configuración</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentMethods as $method)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $method->name }}</td>
                        <td class="px-4 py-3">{{ $method->slug }}</td>
                        <td class="px-4 py-3">
                            <code class="text-xs text-gray-600 dark:text-gray-300">{{ $method->driver_class }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.payment-methods.toggle', $method) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $method->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-600 dark:text-gray-300">{{ is_array($method->config) ? count($method->config) : 0 }} claves</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.payment-methods.edit', $method) }}" class="text-blue-600 hover:text-blue-900 text-xs font-semibold">Editar</a>
                                <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" onsubmit="return confirm('¿Eliminar método de pago?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-semibold">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay métodos de pago registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t dark:border-gray-700">
        {{ $paymentMethods->links() }}
    </div>
</div>
@endsection

